<?php

require __DIR__ . '/api/ITowerAttackMiniGameService/Enums.php';

function l( $String )
{
	echo '[' . date( DATE_RSS ) . '] ' . $String . PHP_EOL;
}

$Server = new CTowerAttackServer( 5337 );
$Server->TickRate = 100 / 1000;
$Server->Listen();

class CTowerAttackServer
{
	public $TickRate;
	private $LastTick;
	private $Socket;
	private $LastGameId = 0;
	private $Games;
	
	public function __construct( $Port )
	{
		$this->Socket = stream_socket_server( 'udp://127.0.0.1:' . $Port, $errno, $errstr, STREAM_SERVER_BIND );
		
		if( !$this->Socket )
		{
			die( "$errstr ($errno)" );
		}
		
		l( 'Listening on port ' . $Port );
		
		$Game = new CTowerAttackGame($this->LastGameId + 1);
		$this->Games[$Game->GetGameId()] = $Game;
	}

	private function SendResponse( $Peer, $Response )
	{
		$Response = json_encode( array( 'response' => $Response ) );
		stream_socket_sendto ( $this->Socket , $Response, 0, $Peer );
	}
	
	public function Listen( )
	{
		while( true )
		{
			$Data = stream_socket_recvfrom( $this->Socket, 1500, 0, $Peer );

			$Data = @json_decode($Data, TRUE);
			if( ( $Data === null && json_last_error() !== JSON_ERROR_NONE ) || !array_key_exists( 'method', $Data ) ) {
				// Require all data sent to the server to be a JSON object and contain the "method" key, ignore everything else.
			    continue;
			}
			
			l( $Peer . ' - ' . $Data['method'] );

			// Handle the request, this could be moved elsewhere...
			switch ( $Data['method'] ) {
				case 'GetGameData':
					$GameId = $Data['gameid'];
					$Game = $this->GetGame( $GameId );
					$Response = null;
					if( $Game !== null ) {
						$Response = array(
							'game_data' => $Game->ToArray(),
							'stats' => array() //TODO
						);
					}
					$this->SendResponse( $Peer, $Response );
					break;
				case 'GetPlayerData':
					$GameId = $Data['gameid'];
					$SteamId = $Data['steamid'];
					$Game = $this->GetGame( $GameId );
					$Response = null;
					if( $Game !== null ) {
						$Player = $Game->GetPlayer( $SteamId );
						if( $Player !== null ) {
							$Response = array(
								'player_data' => $Player->ToArray(),
								'tech_tree' => array() //TODO
							);
						}
					}
					$this->SendResponse( $Peer, $Response );
					break;
				default:
					// TODO: handle unknown methods
					$this->SendResponse( $Peer, null );
					break;
			}
			
			$Tick = microtime( true );
			
			if( $Tick >= $this->LastTick )
			{
				$this->LastTick = $Tick + $this->TickRate;
				
				$this->Tick();
			}
		}
	}
	
	private function Tick()
	{
		l( 'Ticking...' );
	}

	public function GetGames()
	{
		return $this->Games;
	}

	public function GetGame( $GameId )
	{
		//TODO: return array_key_exists( $GameId, $this->Games ) ? $this->Games[$GameId] : null;
		return $this->Games[1];
	}
}

class CTowerAttackLane
{
	/*
	repeated Enemy enemies = 1;
	optional double dps = 2;
	optional double gold_dropped = 3;
	repeated ActiveAbility active_player_abilities = 4;
	repeated uint32 player_hp_buckets = 5;
	optional ETowerAttackElement element = 6;
	// for faster lookup
	optional double active_player_ability_decrease_cooldowns = 7 [default = 1];
	optional double active_player_ability_gold_per_click = 8 [default = 0];
	*/
	private $Enemies;
	private $Dps;
	private $GoldDropped;
	private $ActivePlayerAbilities;
	
	public function __construct(
		array $Enemies, 
		$Dps, 
		$GoldDropped, 
		array $ActivePlayerAbilities, 
		array $PlayerHpBuckets, 
		$Element, 
		$ActivePlayerAbilityDecreaseCooldowns, 
		$ActivePlayerAbilityGoldPerClick 
	) {
		$this->Enemies = $Enemies;
		$this->Dps = $Dps;
		$this->GoldDropped = $GoldDropped;
		$this->ActivePlayerAbilities = $ActivePlayerAbilities;
		$this->PlayerHpBuckets = $PlayerHpBuckets;
		$this->Element = $Element;
		$this->ActivePlayerAbilityDecreaseCooldowns = $ActivePlayerAbilityDecreaseCooldowns;
		$this->ActivePlayerAbilityGoldPerClick = $ActivePlayerAbilityGoldPerClick;
	}

	public function ToArray()
	{
		return array(
			'enemies' => $this->GetEnemiesArray(),
			'dps' => $this->GetDps(),
			'gold_dropped' => $this->GetGoldDropped(),
			'active_player_abilities' => $this->GetActivePlayerAbilities(),
			'player_hp_buckets' => $this->GetPlayerHpBuckets(),
			'element' => $this->GetElement(),
			'active_player_ability_decrease_cooldowns' => $this->GetActivePlayerAbilityDecreaseCooldowns(),
			'active_player_ability_gold_per_click' => $this->GetActivePlayerAbilityGoldPerClick()
		);
	}

	public function GetEnemies()
	{
		return $this->Enemies;
	}

	public function GetEnemiesArray()
	{
		$EnemyArray = array();
		foreach ( $this->GetEnemies() as $Enemy ){
			$EnemyArray[] = $Enemy->ToArray();
		}
		return $EnemyArray;
	}

	public function GetDps()
	{
		return $this->Dps;
	}

	public function GetGoldDropped()
	{
		return $this->GoldDropped;
	}

	public function GetActivePlayerAbilities()
	{
		return $this->ActivePlayerAbilities;
	}

	public function GetPlayerHpBuckets()
	{
		return $this->PlayerHpBuckets;
	}

	public function GetElement()
	{
		return $this->Element;
	}

	public function GetActivePlayerAbilityDecreaseCooldowns()
	{
		return $this->ActivePlayerAbilityDecreaseCooldowns;
	}

	public function GetActivePlayerAbilityGoldPerClick()
	{
		return $this->ActivePlayerAbilityGoldPerClick;
	}
}

class CTowerAttackActiveAbility
{
	/*
	optional uint32 accountid_caster = 1;
	optional uint32 ability = 2;
	optional uint32 timestamp_done = 3;
	optional double multiplier = 4;
	*/
	private $AccountIdCaster;
	private $Ability;
	private $TimestampDone;
	private $Multiplier;

	public function __construct( $AccountIdCaster, $Ability, $TimestampDone, $Multiplier )
	{
		$this->AccountIdCaster = $AccountIdCaster;
		$this->Ability = $Ability;
		$this->TimestampDone = $TimestampDone;
		$this->Multiplier = $Multiplier;
	}

	public function GetAccountIdCaster()
	{
		return $this->AccountIdCaster;
	}

	public function GetAbility()
	{
		return $this->Ability;
	}

	public function GetTimestampDone()
	{
		return $this->TimestampDone;
	}

	public function GetMultiplier()
	{
		return $this->Multiplier;
	}
}

class CTowerAttackEnemy
{
	/*
	optional uint64 id = 1;
	optional ETowerAttackEnemyType type = 2;
	optional double hp = 3;
	optional double max_hp = 4;
	optional double dps = 5;
	optional double timer = 6;
	optional double gold = 7;
	*/
	private $Id;
	private $Type;
	private $Hp;
	private $MaxHp;
	private $Dps;
	private $Timer;
	private $Gold;
	
	public function __construct( $Id, $Type, $Hp, $MaxHp, $Dps, $Timer, $Gold )
	{
		$this->Id = $Id;
		$this->Type = $Type;
		$this->MaxHp = $MaxHp;
		$this->Dps = $Dps;
		$this->Timer = $Timer;
		$this->Gold = $Gold;
		l( "Created new enemy [Id=$Id, Type=$Type, Hp=$Hp, MaxHp=$MaxHp, Dps=$Dps, Timer=$Timer, Gold=$Gold]" );
	}

	public function ToArray()
	{
		$ReturnArray = array(
			'id' => $this->GetId(),
			'type' => $this->GetType(),
			'hp' => $this->GetHp(),
			'max_hp' => $this->GetMaxHp(),
			'dps' => $this->GetDps(),
			'gold' => $this->GetGold()
		);
		if ($this->GetTimer() !== null) {
			$ReturnArray['timer'] = $this->GetTimer();
		}
		return $ReturnArray;
	}

	public function GetId()
	{
		return $this->Id;
	}

	public function GetType()
	{
		return $this->Type;
	}

	public function GetHp()
	{
		return $this->Type;
	}

	public function GetMaxHp()
	{
		return $this->MaxHp;
	}

	public function GetDps()
	{
		return $this->Dps;
	}

	public function GetTimer()
	{
		return $this->Timer;
	}

	public function GetGold()
	{
		return $this->Gold;
	}
}

class CTowerAttackPlayer
{
	/*
	optional double hp = 1;
	optional uint32 current_lane = 2;
	optional uint32 target = 3;
	optional uint32 time_died = 4;
	optional double gold = 5;
	optional uint64 active_abilities_bitfield = 6;
	repeated ActiveAbility active_abilities = 7;
	optional double crit_damage = 8;
	repeated Loot loot = 9;
	*/

	private $AccountId;
	private $Hp;
	private $CurrentLane;
	private $Target;
	private $TimeDied;
	private $Gold;
	private $ActiveAbilitiesBitfield;
	private $ActiveAbilities = array();
	private $CritDamage;
	private $Loot;

	public function __construct(
		$AccountId,
		$Hp,
		$CurrentLane,
		$Target,
		$TimeDied,
		$Gold,
		$ActiveAbilitiesBitfield,
		$ActiveAbilities,
		$CritDamage,
		$Loot
	)
	{
		$this->AccountId = $AccountId;
		$this->Hp = $Hp;
		$this->CurrentLane = $CurrentLane;
		$this->Target = $Target;
		$this->TimeDied = $TimeDied;
		$this->Gold = $Gold;
		$this->ActiveAbilitiesBitfield = $ActiveAbilitiesBitfield;
		$this->ActiveAbilities = $ActiveAbilities;
		$this->CritDamage = $CritDamage;
		$this->Loot = $Loot;
	}

	public function ToArray()
	{
		return array(
			'hp' => $this->GetHp(),
			'current_lane' => $this->GetCurrentLane(),
			'target' => $this->GetTarget(),
			'time_died' => $this->GetTimeDied(),
			'gold' => $this->GetGold(),
			'active_abilities_bitfield' => $this->GetActiveAbilitiesBitfield(),
			'crit_damage' => $this->GetCritDamage()
		);
	}

	public function GetAccountId()
	{
		return $this->AccountId;
	}

	public function GetHp()
	{
		return $this->Hp;
	}

	public function GetCurrentLane()
	{
		return $this->CurrentLane;
	}

	public function GetTarget()
	{
		return $this->Target;
	}

	public function GetTimeDied()
	{
		return $this->TimeDied;
	}

	public function GetGold()
	{
		return $this->Gold;
	}

	public function GetActiveAbilitiesBitfield()
	{
		return $this->ActiveAbilitiesBitfield;
	}

	public function GetActiveAbilities()
	{
		return $this->ActiveAbilities;
	}

	public function GetCritDamage()
	{
		return $this->CritDamage;
	}

	public function GetLoot()
	{
		return $this->Loot;
	}
}

class CTowerAttackGame
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
		//TODO: Set players correctly
		$this->Players['76561197990586091'] = new CTowerAttackPlayer(
			'76561197990586091', //steam id/account id, remember to cast (string)
			rand(3000, 6000), //hp
			1, //current lane
			0, //target
			time(), //time died (timestamp)
			rand(1000, 5000), //gold
			0, // active abilities bitfield (generate from active abilities?)
			array(), // active abilities
			0, // crit damage
			array() // loot
		);

		$this->SetLevel( 0 );
		$this->GenerateNewLanes();
		$this->SetStatus( EMiniGameStatus::Running );
		$this->TimestampGameStart = time();
		l( 'Created game #' . $this->GetGameId() );
	}

	public function ToArray()
	{
		return array(
			'level' => $this->GetLevel(),
			'lanes' => $this->GetLanesArray(),
			'timestamp' => $this->GetTimestamp(),
			'status' => $this->GetStatus(),
			'timestamp_game_start' => $this->GetTimestampGameStart(),
			'timestamp_level_start' => $this->GetTimestampLevelStart()
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

	public function GenerateNewLanes()
	{
		$ActivePlayerAbilities = array();
		$PlayerHpBuckets = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 300); // active players with health between 10 levels (bars) = team health

		// Create 3 lanes
		for ( $i = 0; 3 > $i; $i++ ) {
			// Create 1 enemy in each lane
			$Enemies = array();
			$Enemies[] = new CTowerAttackEnemy(
				$this->GetNextMobId(),
				ETowerAttackEnemyType::Mob,
				rand(1, 10), //hp
				10, //max hp
				1, //dps
				null, //timer
				rand(10, 100) //gold
			);

			$this->Lanes[] = new CTowerAttackLane(
				$Enemies,
				0, //dps
				0, //gold dropped
				$ActivePlayerAbilities,
				$PlayerHpBuckets,
				array_rand(array(
					ETowerAttackElement::Fire,
					ETowerAttackElement::Water,
					ETowerAttackElement::Air,
					ETowerAttackElement::Earth
				)), //element
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
		//TODO: return array_key_exists( $AccountId, $this->Players ) ? $this->Players[$AccountId] : null;
		return $this->Players[$AccountId];
	}
}
