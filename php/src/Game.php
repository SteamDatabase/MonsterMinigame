<?php
namespace SteamDB\CTowerAttack;

class Game
{
	const START_LIMIT = 1; // Start game with 1 players
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
	public $Lanes = array();
	public $Chat = [];
	//private $Timestamp; - Use function instead?
	private $Status;
	//private $Events; - Not used, morning/evning deals
	private $TimestampGameStart;
	private $TimestampLevelStart;
	private $UniverseState;
	private $LastMobId = 0;
	
	public $NumClicks = 0;
	public $NumMobsKilled = 0;
	public $NumTowersKilled = 0;
	public $NumMiniBossesKilled = 0;
	public $NumBossesKilled = 0;
	public $NumTreasuresKilled = 0;
	public $TimeSimulating = 0.0;
	public $HighestTick = 0.0;

	private function GetLastMobId()
	{
		return $this->LastMobId;
	}

	private function GetNextMobId()
	{
		$this->LastMobId++;
		return $this->LastMobId;
	}

	public function __construct()
	{
		//TODO: Add waiting logic and set proper status $this->SetStatus( EMiniGamEnums\EStatus::WaitingForPlayers );
		$this->GenerateNewLanes();
		$this->SetStatus( Enums\EStatus::WaitingForPlayers );
		$this->TimestampGameStart = time();
		$this->TimestampLevelStart = time();
		l( 'Created new game' );
	}

	private function CreatePlayer( $AccountId )
	{
		$this->Players[ $AccountId ] = new Player\Base(
			$AccountId //steam id/account id, remember to cast (string)
		);
		if( count( $this->Players ) == self::START_LIMIT )
		{
			$this->SetStatus( Enums\EStatus::Running );
		}
	}

	public function GenerateNewLevel()
	{
		$this->IncreaseLevel();
		$this->GenerateNewLanes();
		// Remove status? $this->SetStatus( Enums\EStatus::Running );
		l( 'Game moved to level #' . $this->GetLevel() );
	}

	public function ToArray()
	{
		return array(
			'chat' => $this->Chat,
			'level' => (int) $this->GetLevel(),
			'lanes' => $this->GetLanesArray(),
			'timestamp' => (int) $this->GetTimestamp(),
			'status' => $this->GetStatus(),
			'timestamp_game_start' => (int) $this->GetTimestampGameStart(),
			'timestamp_level_start' => (int) $this->GetTimestampLevelStart()
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
			'num_abilities_activated' => 0,
			'num_players_reaching_milestone_level' => 0,
			'num_ability_items_activated' => 0,
			'num_active_players' => count( $this->GetActivePlayers() ), # TODO: replace this with an increasing/decreasing variable
			'time_simulating' => $this->TimeSimulating,
			'time_saving' => 0,
			'time_highest_tick' => $this->HighestTick
		);
	}

	public function GetLevel()
	{
		return $this->Level;
	}

	public function SetLevel( $Level )
	{
		$this->Level = $Level;
		$this->TimestampLevelStart = time();
	}

	public function IncreaseLevel()
	{
		$this->Level++;
		$this->TimestampLevelStart = time();
	}

	public function IsBossLevel()
	{
		return $this->Level % 10 === 0;
	}

	public function GenerateNewLanes()
	{
		$this->Lanes = array();

		if( $this->IsBossLevel() )
		{
			$BossLaneId = rand( 0, 2 );
		}

		// Create 3 lanes
		for( $i = 0; 3 > $i; $i++ ) 
		{
			$PlayerHpBuckets = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
			$ActivePlayerAbilities = [];
			$ActivityLog = [];
			foreach( $this->Players as $Player )
			{
				if( $Player->GetCurrentLane() === $i )
				{
					$PlayerHpBuckets[ $Player->GetHpLevel() ]++;
					foreach( $Player->GetActiveAbilities() as $ActiveAbility )
					{
						if ( !isset( $ActivePlayerAbilities[ $ActiveAbility->GetAbility() ] ) )
						{
							$ActivePlayerAbilities[ $ActiveAbility->GetAbility() ] = [
								'ability' => $ActiveAbility->GetAbility(),
								'quantity' => 1
							];
						}
						else
						{
							$ActivePlayerAbilities[ $ActiveAbility->GetAbility() ][ 'quantity' ]++;
						}
					}
					# TODO: Delete debugging
					$RandomAbilityId = rand( Enums\EAbility::Item_Resurrection, Enums\EAbility::Item_ReflectDamage );
					$ActivePlayerAbilities[ $RandomAbilityId ] = [
						'ability' => $RandomAbilityId,
						'quantity' => rand( 1, 5 )
					];
					// TODO: Remove active abilties after their time runs out 
				}
			}

			$ActivePlayerAbilities = array_values($ActivePlayerAbilities); // This is really stupid, we should just do ActiveAbilityId: Quantity...

			$Enemies = array();
			if( $this->IsBossLevel() )
			{
				// Boss
				if( $BossLaneId === $i )
				{
					$Enemies[] = new Enemy(
						$this->GetNextMobId(),
						Enums\EEnemyType::Boss, // 2
						$this->GetLevel()
					);
				}
			}
			else
			{
				// Standard Tower (Spawner) + 3 Mobs per lane
				$Enemies[] = new Enemy(
					$this->GetNextMobId(),
					Enums\EEnemyType::Tower, // 0
					$this->GetLevel()
				);

				for( $a = 0; 3 > $a; $a++ ) 
				{
					$Enemies[] = new Enemy(
						$this->GetNextMobId(),
						Enums\EEnemyType::Mob, // 1
						$this->GetLevel()
					);
				}
			}
			# TODO: Add Minibosses and treasure mobs

			$ElementalArray = array(
				Enums\EElement::Fire,
				Enums\EElement::Water,
				Enums\EElement::Air,
				Enums\EElement::Earth
			);

			$this->Lanes[] = new Lane(
				$Enemies,
				0, //dps
				0, //gold dropped
				$ActivePlayerAbilities,
				$ActivityLog,
				$PlayerHpBuckets,
				$ElementalArray[ array_rand( $ElementalArray ) ], //element
				0, //decrease cooldown
				0 //gold per click
			);
		}
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

	public function GetTimestamp()
	{
		return time();
	}

	public function GetStatus()
	{
		return $this->Status;
	}

	public function SetStatus( $Status )
	{
		$this->Status = $Status;
	}

	public function GetEvents()
	{
		return $this->Events;
	}

	public function GetTimestampGameStart()
	{
		return $this->TimestampGameStart;
	}

	public function GetTimestampLevelStart()
	{
		return $this->TimestampLevelStart;
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
			if ($Player->IsActive())
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
		//TODO: remove this
		if( !array_key_exists( $AccountId, $this->Players ) )
		{
			l( 'Creating new player ' . $AccountId );
			$this->CreatePlayer( $AccountId );
		}

		//TODO: return array_key_exists( $AccountId, $this->Players ) ? $this->Players[$AccountId] : null;
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
		$Player->LastActive = time();
		$this->Players[ $Player->GetAccountId() ] = $Player;
	}

	public function Update( $SecondsPassed )
	{
		$SecondPassed = $SecondsPassed !== false && $SecondsPassed > 0;

		$LaneDps = [
			0 => 0,
			1 => 0,
			2 => 0
		];

		foreach( $this->Players as $Player )
		{
			// Give Players money (TEMPLORARY)
			$Player->IncreaseGold(50000);

			if( $SecondPassed && !$Player->IsDead() )
			{
				// Deal DPS damage to current target
				$Enemy = $this->Lanes[ $Player->GetCurrentLane() ]->GetEnemy( $Player->GetTarget() );
				
				if( $Enemy !== null )
				{
					$DealtDpsDamage = $Player->GetTechTree()->GetDps() 
									* $Player->GetTechTree()->GetExtraDamageMultipliers( $this->Lanes[ $Player->GetCurrentLane() ]->GetElement() ) 
									* $SecondsPassed;
					$Player->Stats->DpsDamageDealt += $DealtDpsDamage;
					$Enemy->DamageTaken += $DealtDpsDamage;
					foreach( $Player->LaneDamageBuffer as $LaneId => $LaneDamage )
					{
						$LaneDps[ $LaneId ] += $LaneDamage / $SecondPassed; // TODO: This is damage done by clicks, not per second, remove or keep?
						$Player->LaneDamageBuffer[ $LaneId ] = 0;
					}
					$LaneDps[ $Player->GetCurrentLane() ] += $Player->GetTechTree()->GetDps() * $SecondsPassed;
				}
			}

			if( $Player->IsDead() && $Player->CanRespawn( true ) )
			{
				// Respawn player
				$Player->Respawn();
			}
		}

		// Loop through lanes and deal damage etc
		$DeadLanes = 0;
		foreach( $this->Lanes as $LaneId => $Lane )
		{
			$DeadEnemies = 0;
			$EnemyCount = count( $Lane->Enemies );
			$EnemyDpsDamage = 0;
			foreach( $Lane->Enemies as $Enemy )
			{
				if( $Enemy->IsDead() )
				{
					if( $Enemy->GetHpDifference() > 0 && $LaneId !== $EnemyCount )
					{
						// Find next enemy to deal the rest of the damage to
						$NextEnemy = $Lane->GetAliveEnemy();
						if( $NextEnemy !== null )
						{
							$NextEnemy->DamageTaken += $Enemy->GetHpDifference();
						}
					}
					$DeadEnemies++;
				}
				else
				{
					$Enemy->DecreaseHp( $Enemy->DamageTaken );
					if( $Enemy->GetHpDifference() > 0 && $LaneId !== $EnemyCount )
					{
						// Find next enemy to deal the rest of the damage to
						$NextEnemy = $Lane->GetAliveEnemy();
						if( $NextEnemy !== null )
						{
							$NextEnemy->DamageTaken += $Enemy->GetHpDifference();
						}
					}
					if( $Enemy->IsDead() )
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
								$this->NumBossesKilled++;
								break;
							case Enums\EEnemyType::MiniBoss:
								$this->NumMiniBossesKilled++;
								break;
							case Enums\EEnemyType::TreasureMob:
								$this->NumTreasureMobsKilled++;
								break;
						}
						$DeadEnemies++;
						$Lane->GiveGoldToPlayers( $this, $Enemy->GetGold() );
					}
					else
					{
						$EnemyDpsDamage += $Enemy->GetDps();
					}
				}
				$Enemy->DamageTaken = 0;
			}
			$DeadLanes += $DeadEnemies === count( $Lane->Enemies ) ? 1 : 0;
			// Deal damage to players in lane
			$PlayersInLane = array();
			foreach( $this->Players as $Player )
			{
				if( $Player->GetCurrentLane() === $LaneId )
				{
					$PlayersInLane[] = $Player;
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
							$Player->Kill();
						}
					}
				}
			}
			$Lane->Dps = $LaneDps[ $LaneId ];
			$Lane->UpdateHpBuckets( $PlayersInLane );
		}
		if( $DeadLanes === 3 ) 
		{
			$this->GenerateNewLevel();
		}
	}
}
