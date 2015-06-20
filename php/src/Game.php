<?php
namespace SteamDB\CTowerAttack;

use SteamDB\CTowerAttack\Player as Player;

class Game
{
	/*
	optional uint32 level = 1;
	repeated Lane lanes = 2;
	optional uint32 timestamp = 3;
	optional EMiniGameStatus status = 4;
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
		//TODO: Add waiting logic and set proper status $this->SetStatus( EMiniGameStatus::WaitingForPlayers );
		$this->GenerateNewLanes();
		$this->SetStatus( \EMiniGameStatus::Running );
		$this->TimestampGameStart = time();
		$this->TimestampLevelStart = time();
		l( 'Created new game' );
	}

	private function CreatePlayer( $AccountId )
	{
		$this->Players[ $AccountId ] = new Player\Base(
			$AccountId //steam id/account id, remember to cast (string)
		);
	}

	public function GenerateNewLevel()
	{
		$this->IncreaseLevel();
		$this->GenerateNewLanes();
		// Remove status? $this->SetStatus( \EMiniGameStatus::Running );
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
		$ActivePlayerAbilities =
		[
			[
				'accountid_caster' => 0, 
				'caster' => 'Test User',
				'ability' => rand( \ETowerAttackAbility::Item_Resurrection, \ETowerAttackAbility::Item_ReflectDamage ),
				'timestamp_done' => time() + 1,
				'multiplier' => 1.0,
			]
		];
		$PlayerHpBuckets = array(10, 20, 30, 40, 50, 60, 70, 80, 90, 100); // active players with health between 10 levels (bars) = team health

		if( $this->IsBossLevel() )
		{
			$BossLaneId = rand( 0, 2 );
		}

		// Create 3 lanes
		for( $i = 0; 3 > $i; $i++ ) 
		{
			$Enemies = array();
			if( $this->IsBossLevel() )
			{
				// Boss
				if( $BossLaneId === $i )
				{
					$Enemies[] = new Enemy(
						$this->GetNextMobId(),
						\ETowerAttackEnemyType::Boss, // 2
						$this->GetLevel()
					);
				}
			}
			else
			{
				// Standard Tower (Spawner) + 3 Mobs per lane
				$Enemies[] = new Enemy(
					$this->GetNextMobId(),
					\ETowerAttackEnemyType::Tower, // 0
					$this->GetLevel()
				);

				for( $a = 0; 3 > $a; $a++ ) 
				{
					$Enemies[] = new Enemy(
						$this->GetNextMobId(),
						\ETowerAttackEnemyType::Mob, // 1
						$this->GetLevel()
					);
				}
			}
			# TODO: Add Minibosses and treasure mobs

			$ElementalArray = array(
				\ETowerAttackElement::Fire,
				\ETowerAttackElement::Water,
				\ETowerAttackElement::Air,
				\ETowerAttackElement::Earth
			);

			$this->Lanes[] = new Lane(
				$Enemies,
				0, //dps
				0, //gold dropped
				$ActivePlayerAbilities,
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

	public function UpdatePlayer( $Player )
	{
		$Player->LastActive = time();
		$this->Players[ $Player->GetAccountId() ] = $Player;
	}

	public function Update()
	{
		// Loop through lanes and deal damage etc
		$DeadLanes = 0;
		foreach( $this->Lanes as $Lane )
		{
			$DeadEnemies = 0;
			foreach( $Lane->Enemies as $Enemy )
			{
				if( $Enemy->IsDead() )
				{
					$DeadEnemies++;
				}
				else
				{
					$Enemy->DecreaseHp( $Enemy->DamageTaken );
					$Enemy->DamageTaken = 0;
					if( $Enemy->IsDead() )
					{
						switch( $Enemy->GetType() ) 
						{
							case \ETowerAttackEnemyType::Tower:
								$this->NumTowersKilled++;
								break;
							case \ETowerAttackEnemyType::Mob:
								$this->NumMobsKilled++;
								break;
							case \ETowerAttackEnemyType::Boss:
								$this->NumBossesKilled++;
								break;
							case \ETowerAttackEnemyType::MiniBoss:
								$this->NumMiniBossesKilled++;
								break;
							case \ETowerAttackEnemyType::TreasureMob:
								$this->NumTreasureMobsKilled++;
								break;
						}
						$DeadEnemies++;
						$Lane->GiveGoldToPlayers( $this, $Enemy->GetGold() );
					}
				}
			}
			$DeadLanes += $DeadEnemies === count( $Lane->Enemies ) ? 1 : 0;
		}
		if( $DeadLanes === 3 ) 
		{
			$this->GenerateNewLevel();
		}
	}
}
?>
