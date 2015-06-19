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
	private $Players = array();
	private $GameId;
	private $Level = 0;
	private $Lanes = array();
	//private $Timestamp; - Use function instead?
	private $Status;
	//private $Events; - Not used, morning/evning deals
	private $TimestampGameStart;
	private $TimestampLevelStart;
	private $UniverseState;
	private $LastMobId = 0;

	private function GetLastMobId()
	{
		return $this->LastMobId;
	}

	private function GetNextMobId()
	{
		$this->LastMobId++;
		return $this->LastMobId;
	}
	
	public function __construct($GameId)
	{
		//TODO: Add waiting logic and set proper status $this->SetStatus( EMiniGameStatus::WaitingForPlayers );
		$this->GameId = $GameId;
		$this->SetLevel( 0 );
		$this->GenerateNewLanes();
		$this->SetStatus( \EMiniGameStatus::Running );
		$this->TimestampGameStart = time();
		l( 'Created game #' . $this->GetGameId() );
	}

	private function CreatePlayer( $ID )
	{
		$this->Players[ $ID ] = new Player\Base(
			$ID, //steam id/account id, remember to cast (string)
			rand(3000, 6000), //hp
			1, //current lane
			0, //target
			time(), //time died (timestamp)
			rand(1000, 5000), //gold
			0, // active abilities bitfield (generate from active abilities?)
			array(), // active abilities
			0, // crit damage
			array(), // loot
			new Player\TechTree\Base( // tech tree
				array(), // upgrades
				1.0, // damage per click
				$DamageMultiplierFire = 1.0, // damage multiplier fire
				$DamageMultiplierWater = 1.0, // damage multiplier water
				$DamageMultiplierAir = 1.0, // damage multiplier air
				$DamageMultiplierEarth = 1.0, // damage multiplier earth
				$DamageMultiplierCrit = 2.0, // damage multiplier crit
				$UnlockedAbilitiesBitfield = 0, // unlocked abilities bitfield
				$HpMultiplier = 1.0, // hp multiplier
				$CritPercentage = 0, // crit percentage
				$BadgePoints = 0, // badge points
				$AbilityItems = array(), // ability items
				$BossLootDropPercentage = 0.25, // boss loot drop percentage
				$DamageMultiplierDps = 1.0, // damage multiplier dps
				$BaseDps = 0, // base dps
				$DamagePerClickMultiplier = 1.0, // damage per click multiplier
				$MaxHp = 0, // max hp
				$Dps = 0 // dps
			)
		);
	}

	public function GenerateNewLevel()
	{
		$this->IncreaseLevel();
		$this->GenerateNewLanes();
		// Remove status? $this->SetStatus( \EMiniGameStatus::Running );
		l( 'Game #' . $this->GetGameId() . ' moved to level #' . $this->GetLevel() );
	}

	public function ToArray()
	{
		return array(
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
			'num_players' => 991,
			'num_mobs_killed' => '691',
			'num_towers_killed' => '232',
			'num_minibosses_killed' => '66',
			'num_bosses_killed' => '11',
			'num_clicks' => '665422',
			'num_abilities_activated' => '1214',
			'num_players_reaching_milestone_level' => '991',
			'num_ability_items_activated' => '5177',
			'num_active_players' => 126,
			'time_simulating' => 3.0477301080604891,
			'time_saving' => 41.225250769888
		);
	}

	public function GetGameId()
	{
		return $this->GameId;
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

	public function GenerateNewLanes()
	{
		$this->Lanes = array();
		$ActivePlayerAbilities = array();
		$PlayerHpBuckets = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 300); // active players with health between 10 levels (bars) = team health

		// Create 3 lanes
		for ( $i = 0; 3 > $i; $i++ ) {
			// Create 3 enemy in each lane
			$Enemies = array();
			$Enemies[] = new Enemy(
				$this->GetNextMobId(),
				\ETowerAttackEnemyType::Tower, // 0
				$this->GetLevel()
			);

			for ( $a = 0; 3 > $a; $a++ ) {
				$Enemies[] = new Enemy(
					$this->GetNextMobId(),
					\ETowerAttackEnemyType::Mob, // 1
					$this->GetLevel()
				);
			}

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
				$ElementalArray[array_rand($ElementalArray)], //element
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
		foreach ( $this->GetLanes() as $Lane ){
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

	public function GetPlayers()
	{
		return $this->Players;
	}

	public function GetPlayer( $AccountId )
	{
		//TODO: remove this
		if( !isset( $this->Players[ $AccountId ] ) )
		{
			$this->CreatePlayer( $AccountId );
		}
		
		//TODO: return array_key_exists( $AccountId, $this->Players ) ? $this->Players[$AccountId] : null;
		return $this->Players[ $AccountId ];
	}

	public function UpdatePlayer( $Player )
	{
		$this->Players[ $Player->GetAccountId() ] = $Player;
	}
}
?>
