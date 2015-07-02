<?php
namespace SteamDB\CTowerAttack;

use SteamDB\CTowerAttack\Server;
use SteamDB\CTowerAttack\Player;
use SteamDB\CTowerAttack\Player\TechTree\AbilityItem;

class Game
{
	/*
	optional uint32 level = 1;
	repeated Lane lanes = 2;
	optional uint32 timestamp = 3;
	optional EMiniGamEnums\EStatus status = 4;
	repeated Event events = 5;
	optional uint32 timestamp_game_start = 6;
	optional uint32 timestamp_level_start = 7;
	optional string universe_state = 8;
	*/

	private $AbilityQueue;
	public $Players = array();
	private $Level = 1;
	public $Time;
	public $Lanes = array();
	private $Chat = []; // TODO: SplQueue
	//private $Timestamp; - Use function instead?
	private $Status;
	//private $Events; - Not used, morning/evning deals
	private $TimestampGameStart;
	private $TimestampLevelStart;
	private $UniverseState;
	private $LastMobId = 0;

	// Stats
	public $NumClicks = 0;
	public $NumMobsKilled = 0;
	public $NumTowersKilled = 0;
	public $NumMiniBossesKilled = 0;
	public $NumBossesKilled = 0;
	public $NumTreasuresKilled = 0;
	public $NumAbilitiesActivated = 0;
	public $NumAbilityItemsActivated = 0;

	public $TimeSimulating = 0.0;
	public $HighestTick = 0.0;
	public $WormholeCount = 0;

	private function GetLastMobId()
	{
		return $this->LastMobId;
	}

	private function GetNextMobId()
	{
		$this->LastMobId++;
		return $this->LastMobId;
	}

	public function __construct( $GameId )
	{
		$this->GameId = $GameId;
		$this->Time = time();
		$this->TimestampGameStart = $this->Time;
		$this->TimestampLevelStart = $this->Time;
		$this->SetStatus( Enums\EStatus::WaitingForPlayers );
		$this->GenerateNewLanes();

		Server::GetLogger()->info( 'Created new game #' . $GameId );
	}

	public function CreatePlayer( $AccountId, $Name )
	{
		Server::GetLogger()->debug( 'Creating new player ' . $Name . ': #' . $AccountId);

		$Player = new Player\Player($AccountId, $Name);
		$Player->LastActive = $this->Time;

		$this->Players[ $AccountId ] = $Player;

		if( $this->Status === Enums\EStatus::WaitingForPlayers && count( $this->Players ) === Server::GetTuningData( 'minimum_players' ) )
		{
			$this->SetStatus( Enums\EStatus::Running );
		}

		return $Player;
	}

	public function GenerateNewLevel()
	{
		$this->IncreaseLevel();
		$this->GenerateNewEnemies();
		Server::GetLogger()->info( 'Game #' . $this->GameId . ' moved to level #' . $this->GetLevel() );
	}

	public function ToArray()
	{
		return array(
			'chat' => array_reverse( array_slice( $this->Chat, -50, 50 ) ), // TODO: Chat should be SplQueue with FIFO, this is just a temporary hack
			'level' => (int) $this->GetLevel(),
			'lanes' => $this->GetLanesArray(),
			'timestamp' => $this->Time,
			'status' => $this->GetStatus(),
			'timestamp_game_start' => $this->TimestampGameStart,
			'timestamp_level_start' => $this->TimestampLevelStart
		);
	}

	public function GetStats()
	{
		// TODO: get real data
		return array(
			'num_players' => count( $this->Players ),
			'num_mobs_killed' => $this->NumMobsKilled,
			'num_towers_killed' => $this->NumTowersKilled,
			'num_minibosses_killed' => $this->NumMiniBossesKilled,
			'num_bosses_killed' => $this->NumBossesKilled,
			'num_treasures_killed' => $this->NumTreasuresKilled,
			'num_clicks' => $this->NumClicks,
			'num_abilities_activated' => $this->NumAbilitiesActivated,
			'num_ability_items_activated' => $this->NumAbilityItemsActivated,
			'num_active_players' => count( $this->GetActivePlayers() ), # TODO: replace this with an increasing/decreasing variable
			'time_total_ticks' => number_format( $this->TimeSimulating, 7 ),
			'time_slowest_tick' => number_format( $this->HighestTick, 7 ),
		);
	}

	public function GetLevel()
	{
		return $this->Level;
	}

	public function SetLevel( $Level )
	{
		$this->Level = $Level;
		$this->TimestampLevelStart = $this->Time;
	}

	public function IncreaseLevel()
	{
		$this->Level++;
		$this->TimestampLevelStart = $this->Time;
	}

	public function IsBossLevel()
	{
		return $this->Level % 10 === 0;
	}

	public function IsGoldHelmBossLevel()
	{
		return $this->Level % 100 === 0;
	}

	public function GenerateNewEnemies()
	{
		Server::GetLogger()->info( 'Starting to generate new enemies for level #' . $this->GetLevel() . ' in game #' . $this->GameId );
		$NumPlayers = count( $this->GetActivePlayers() ); # TODO: Or count( $this->Players )
		$HasTreasureMob = false;

		if( $this->IsBossLevel() )
		{
			$BossLaneId = mt_rand( 0, 2 );
		}

		foreach( $this->Lanes as $LaneId => $Lane )
		{
			$Enemies = array();
			if( $this->IsBossLevel() )
			{
				// Boss
				if( $LaneId === $BossLaneId )
				{
					$Enemies[] = new Enemy(
						$NumPlayers,
						$this->GetNextMobId(),
						0,
						Enums\EEnemyType::Boss,
						$this->GetLevel()
					);
				}
				else
				{

					$MiniBossDps = Enemy::GetDpsAtLevel( Enums\EEnemyType::MiniBoss, $this->GetLevel() );
					$MiniBossGold = Enemy::GetGoldAtLevel( Enums\EEnemyType::MiniBoss, $this->GetLevel() );

					for( $a = 0; 3 > $a; $a++ )
					{
						$Enemies[] = new Enemy(
							$NumPlayers,
							$this->GetNextMobId(),
							$a,
							Enums\EEnemyType::MiniBoss,
							$this->GetLevel(),
							$MiniBossDps,
							$MiniBossGold
						);
					}
				}
			}
			else
			{
				// Standard Tower (Spawner) + 3 Mobs per lane
				$Enemies[] = new Enemy(
					$NumPlayers,
					$this->GetNextMobId(),
					0,
					Enums\EEnemyType::Tower,
					$this->GetLevel()
				);

				$MobDps = Enemy::GetDpsAtLevel( Enums\EEnemyType::Mob, $this->GetLevel() );
				$MobGold = Enemy::GetGoldAtLevel( Enums\EEnemyType::Mob, $this->GetLevel() );

				for( $a = 0; 3 > $a; $a++ )
				{
					if( !$HasTreasureMob && Enemy::SpawnTreasureMob() )
					{
						// Spawn Treasure mob
						$Enemies[] = new Enemy(
							$NumPlayers,
							$this->GetNextMobId(),
							$a,
							Enums\EEnemyType::TreasureMob,
							$this->GetLevel()
						);

						$HasTreasureMob = true;

						$this->AddChatEntry( 'server', '', 'Treasure spawned in ' . $Lane->GetLaneName() . ' lane' );
					}
					else
					{
						// Spawn normal mob
						$Enemies[] = new Enemy(
							$NumPlayers,
							$this->GetNextMobId(),
							$a,
							Enums\EEnemyType::Mob,
							$this->GetLevel(),
							$MobDps,
							$MobGold
						);
					}
				}
			}
			$Lane->Enemies = $Enemies;
			$Lane->Element = mt_rand( Enums\EElement::Start, Enums\EElement::End );
		}
	}

	public function GenerateNewLanes()
	{
		Server::GetLogger()->info( 'Starting to generate new lanes for level #' . $this->GetLevel() . ' in game #' . $this->GameId );
		$this->Lanes = array();
		for( $i = 0; 3 > $i; $i++ )
		{
			$this->Lanes[] = new Lane( $i );
		}
		$this->GenerateNewEnemies();
	}

	public function GetLane($LaneId)
	{
		return $this->Lanes[$LaneId];
	}

	public function GetLanes()
	{
		return $this->Lanes;
	}

	public function GetLanesArray()
	{
		$LaneArray = array();
		foreach( $this->GetLanes() as $Lane )
		{
			$LaneArray[] = $Lane->ToArray();
		}
		return $LaneArray;
	}

	public function GetStatus()
	{
		return $this->Status;
	}

	public function SetStatus( $Status )
	{
		$this->Status = $Status;
	}

	public function IsRunning()
	{
		return $this->Status == Enums\EStatus::Running;
	}

	public function AddChatEntry( $ChatType, $Actor, $Message )
	{
		$this->Chat[] =
		[
			'type' => $ChatType,
			'time' => $this->Time,
			'actor' => $Actor,
			'message' => $Message
		];
	}

	public function GetEvents()
	{
		return $this->Events;
	}

	public function IncreaseEnemiesKilled( $Enemy )
	{
		switch( $Enemy->GetType() )
		{
			case Enums\EEnemyType::Tower:
				$this->NumTowersKilled++;
				break;
			case Enums\EEnemyType::Mob:
				$this->NumMobsKilled++;
				break;
			case Enums\EEnemyType::Boss:
				foreach( $this->Players as $Player )
				{
					if( $Player->IsLootDropped() )
					{
						$Player->AddLoot( $this->Time, AbilityItem::GetRandomAbilityItem() );
					}
				}

				$this->NumBossesKilled++;
				break;
			case Enums\EEnemyType::MiniBoss:
				$this->NumMiniBossesKilled++;
				break;
			case Enums\EEnemyType::TreasureMob:
				$this->NumTreasuresKilled++;
				break;
		}
	}

	public function GetUniverseState()
	{
		return $this->UniverseState;
	}

	public function GetActivePlayers()
	{
		$ActivePlayers = array();

		// So dirty to loop through this...
		foreach( $this->Players as $Player )
		{
			if( $Player->IsActive( $this->Time ) )
			{
				$ActivePlayers[] = $Player;
			}
		}

		return $ActivePlayers;
	}

	public function GetPlayers()
	{
		return $this->Players;
	}

	public function GetPlayer( $AccountId )
	{
		if( !isset( $this->Players[ $AccountId ] ) )
		{
			return null;
		}

		return $this->Players[ $AccountId ];
	}

	public function GetPlayersInLane( $LaneId )
	{
		# TODO: Instead of looping, keep an updated array of current players in the lane?
		$Players = array();
		foreach( $this->Players as $Player )
		{
			if( $Player->GetCurrentLane() === $LaneId )
			{
				$Players[] = $Player;
			}
		}
		return $Players;
	}

	public function UpdatePlayer( $Player )
	{
		Server::GetLogger()->debug( 'Updating player ' . $Player->PlayerName . ' (#' . $Player->GetAccountId() . ')' );
		$Player->LastActive = $this->Time;
		$this->Players[ $Player->GetAccountId() ] = $Player;
	}

	public function Update( $SecondsPassed = false )
	{
		$this->Time = time();

		if( !$this->IsRunning() )
		{
			Server::GetLogger()->debug( 'Game #' . $this->GameId . ' is not running, canceling update' );
			return;
		}

		Server::GetLogger()->debug( 'Updating game #' . $this->GameId );

		$SecondPassed = $SecondsPassed !== false && $SecondsPassed > 0;
		$LaneDps = [
			0 => 0,
			1 => 0,
			2 => 0
		];

		foreach( $this->Players as $Player )
		{
			$Player->ClearLoot( $this->Time );
			$Player->CheckActiveAbilities( $this );

			if( $SecondPassed && !$Player->IsDead() )
			{
				// Deal DPS damage to current target
				$Lane = $this->Lanes[ $Player->GetCurrentLane() ];
				$Enemy = $Lane->GetEnemy( $Player->GetTarget() );

				if( $Enemy === null || $Enemy->IsDead() )
				{
					$Enemy = $Lane->GetAliveEnemy();
					if( $Enemy !== null )
					{
						$Player->SetTarget( $Enemy->GetPosition() );
					}
				}

				if( $Enemy !== null )
				{
					$DealtDpsDamage = $Player->GetTechTree()->GetDps()
									* $Player->GetTechTree()->GetExtraDamageMultipliers( $this->Lanes[ $Player->GetCurrentLane() ]->GetElement() )
									* $this->Lanes[ $Player->GetCurrentLane() ]->GetDamageMultiplier()
									* $SecondsPassed;
					if( $this->Lanes[ $Player->GetCurrentLane() ]->HasActivePlayerAbilityMaxElementalDamage() )
					{
						$DealtDpsDamage *= $Player->GetTechTree()->GetHighestElementalMultiplier();
					}
					if( $Player->GetCritDamage() > 0 )
					{
						$DealtDpsDamage *= $Player->GetTechTree()->GetDamageMultiplierCrit();
						$Player->Stats->CritDamageDealt += $DealtDpsDamage;
						$Player->CritDamage = 0;
					}
					$Player->Stats->DpsDamageDealt += $DealtDpsDamage;
					$Enemy->DpsDamageTaken += $DealtDpsDamage;
					if( $DealtDpsDamage > 0)
					{
						Server::GetLogger()->debug(
							'Player ' . $Player->PlayerName . ' (#' . $Player->GetAccountId() . ')' .
							' did ' . $DealtDpsDamage . ' DPS damage to enemy #' . $Enemy->GetId()
						);
					}
					foreach( $Player->LaneDamageBuffer as $LaneId => $LaneDamage )
					{
						$LaneDps[ $LaneId ] += $LaneDamage / $SecondsPassed; // TODO: This is damage done by clicks, not per second, remove or keep?
						$Player->LaneDamageBuffer[ $LaneId ] = 0;
					}
					$LaneDps[ $Player->GetCurrentLane() ] += $Player->GetTechTree()->GetDps() * $SecondsPassed; #TODO: $DealtDpsDamage?
				}
			}

			if( $Player->IsDead() && $Player->CanRespawn( $this->Time, true ) )
			{
				// Respawn player
				$Player->Respawn();
			}
		}

		// Loop through lanes and deal damage etc
		$DeadLanes = 0;

		foreach( $this->Lanes as $LaneId => $Lane )
		{
			if( $SecondPassed )
			{
				# TODO: Apply this in Lane::CheckActivePlayerAbilities instead?
				$ReflectDamageMultiplier = $Lane->GetReflectDamageMultiplier();
				$NapalmDamageMultiplier = $Lane->GetNapalmDamageMultiplier();
			}

			$DeadEnemies = 0;
			$EnemyCount = count( $Lane->Enemies );
			$EnemyDpsDamage = 0;

			foreach( $Lane->Enemies as $Enemy )
			{
				if( $Enemy->IsDead() )
				{
					if( $Enemy->GetDpsHpDifference() > 0 )
					{
						// Find next enemy to deal the rest of the DPS damage to
						$NextEnemy = $Lane->GetAliveEnemy();

						if( $NextEnemy !== null )
						{
							Server::GetLogger()->debug(
								'Enemy #' . $Enemy->GetId() . ' is dead. Passing on ' .
								$Enemy->GetDpsHpDifference() . ' DPS damage to enemy #' .
								$NextEnemy->GetId()
							);

							$NextEnemy->DpsDamageTaken += $Enemy->GetDpsHpDifference();
						}
					}

					$DeadEnemies++;
				}
				else
				{
					if( $SecondPassed )
					{
						# TODO: Apply this in Lane::CheckActivePlayerAbilities instead?
						# TODO: Check if $ReflectDamageMultiplier is 0.5% or 50%, 0.5% would make more sense if it stacks..
						if( $ReflectDamageMultiplier > 0 )
						{
							$Enemy->AbilityDamageTaken += $Enemy->GetHp() * $ReflectDamageMultiplier * $SecondsPassed;
						}

						if( $NapalmDamageMultiplier > 0 )
						{
							$Damage = $Enemy->GetMaxHp() * $NapalmDamageMultiplier * $SecondsPassed;
							$Enemy->AbilityDamageTaken += $Damage;
						}
					}

					$Enemy->Hp -= $Enemy->DpsDamageTaken;

					if( $Enemy->GetDpsHpDifference() > 0 )
					{
						// Find next enemy to deal the rest of the DPS damage to
						$NextEnemy = $Lane->GetAliveEnemy();

						if( $NextEnemy !== null )
						{
							Server::GetLogger()->debug(
								'Enemy #' . $Enemy->GetId() . ' is dead. Passing on ' .
								$Enemy->GetDpsHpDifference() . ' DPS damage to enemy #' .
								$NextEnemy->GetId()
							);
							$NextEnemy->DpsDamageTaken += $Enemy->GetDpsHpDifference();
						}
					}

					$Enemy->Hp -= $Enemy->ClickDamageTaken;
					$Enemy->Hp -= $Enemy->AbilityDamageTaken;

					if( $Enemy->IsDead() )
					{
						$this->IncreaseEnemiesKilled( $Enemy );
						$Enemy->SetHp( 0 );
						$DeadEnemies++;
						$EnemyGold = $Enemy->GetGold() * $Lane->GetEnemyGoldMultiplier();

						Server::GetLogger()->debug(
							'Enemy #' . $Enemy->GetId() . ' is dead. Giving ' . $EnemyGold .
							' gold (Multiplier: ' . $Lane->GetEnemyGoldMultiplier() . ') to players in lane #' . $Lane->GetLaneId()
						);

						$Lane->GiveGoldToPlayers( $this, $EnemyGold );
					}
					else
					{
						$EnemyDpsDamage += $Enemy->GetDps();
					}
				}

				$Enemy->DpsDamageTaken = 0;
				$Enemy->ClickDamageTaken = 0;
				$Enemy->AbilityDamageTaken = 0;

				if( $Enemy->HasTimer() && $Enemy->IsTimerEnabled() && $Enemy->HasTimerRanOut( $SecondsPassed ) )
				{
					switch( $Enemy->GetType() )
					{
						case Enums\EEnemyType::Tower:
							if( $Enemy->IsDead() )
							{
								continue;
							}

							// Revive dead mobs in the lane if the tower timer ran out
							Server::GetLogger()->debug(
								'Respawn timer has ran out in lane #' .
								$Lane->GetLaneId() . ', reviving dead mobs in the lane'
							);

							foreach( $Lane->GetDeadEnemies( Enums\EEnemyType::Mob ) as $DeadEnemy )
							{
								$DeadEnemy->ResetHp();
							}

							break;
						case Enums\EEnemyType::MiniBoss:
							if( !$Enemy->IsDead() )
							{
								continue;
							}

							// Revive dead miniboss if he's dead and the timer ran out
							Server::GetLogger()->debug(
								'Respawn timer has ran out for miniboss #' . $Enemy->GetId() .
								' in lane #' . $Lane->GetLaneId() . ', reviving dead miniboss'
							);

							$Enemy->ResetHp();
							break;
						case Enums\EEnemyType::TreasureMob:
							if( $Enemy->IsDead() )
							{
								continue;
							}

							// Kill the treasure mob and set gold to 0 if the timer (lifetime) ran out
							Server::GetLogger()->debug(
								'Treasure mob #' . $Enemy->GetId() .
								' timer has ran out in lane #' . $Lane->GetLaneId() .
								', killing treasure mob'
							);

							$Enemy->SetHp( 0 );
							$Enemy->SetGold( 0 );
							$Enemy->DisableTimer();

							$this->AddChatEntry( 'server', '', 'You were too slow to kill the treasure, it has despawned' );

							break;
					}

					$Enemy->ResetTimer();
				}
			}

			$DeadLanes += $DeadEnemies === count( $Lane->Enemies ) ? 1 : 0;

			// Deal damage to players in lane
			$PlayersInLane = [];

			foreach( $this->Players as $Player )
			{
				if( $Player->GetCurrentLane() === $LaneId )
				{
					$PlayersInLane[] = $Player;

					if( $Player->IsInvulnerable() )
					{
						continue;
					}

					if( $SecondPassed && !$Player->IsDead() )
					{
						$EnemyDamage = $EnemyDpsDamage * $SecondsPassed;
						$PlayerHp = $Player->Hp - $EnemyDamage;

						if( $PlayerHp > 0 )
						{
							$Player->Stats->DamageTaken += $EnemyDamage;
							$Player->Hp = $PlayerHp;
						}
						else
						{
							$Player->Stats->DamageTaken += $Player->Hp;
							$Player->Hp = 0;
							$Player->Kill( $this->Time );
						}
					}
				}
			}

			$Lane->Dps = $LaneDps[ $LaneId ];
			$Lane->CheckActivePlayerAbilities( $this, $SecondsPassed );
			$Lane->UpdateHpBuckets( $this->Time, $PlayersInLane );
		}

		if( $DeadLanes === 3 )
		{
			if( $this->WormholeCount > 0 )
			{
				if( $this->IsGoldHelmBossLevel() )
				{
					$this->WormholeCount *= 10;
				}

				$this->Level += $this->WormholeCount;

				$BadgePoints = Util::FloorToMultipleOf( 10, $this->WormholeCount * 0.1 );
				$AbilityGold = AbilityItem::GetGoldMultiplier( Enums\EAbility::Item_SkipLevels );

				$Players = $this->GetPlayers();

				foreach( $Players as $Player )
				{
					$Player->IncreaseGold( $AbilityGold * $this->WormholeCount ); # TODO: Is gold stackable as well?

					if( $BadgePoints > 0 )
					{
						$Player->GetTechTree()->IncreaseBadgePoints( $BadgePoints );
					}
				}

				$this->AddChatEntry( 'server', '', 'Skipped ' . number_format( $this->WormholeCount ) . ' level' . ( $this->WormholeCount === 1 ? '' : 's' ) );

				$this->WormholeCount = 0;

				foreach( $this->Lanes as $Lane )
				{
					$Lane->RemoveActiveWormholes();
				}
			}

			$this->GenerateNewLevel();
		}
	}
}
