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
	private $Games;
	
	public function __construct( $Port )
	{
		$this->Socket = stream_socket_server( 'udp://127.0.0.1:' . $Port, $errno, $errstr, STREAM_SERVER_BIND );
		
		if( !$this->Socket )
		{
			die( "$errstr ($errno)" );
		}
		
		l( 'Listening on port ' . $Port );
		
		$Game = new CTowerAttackGame;
		$this->Games[$Game->GetId()] = $Game;
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
					$Response = NULL;
					if( $Game !== NULL ) {
						$Response = array(
							'game_data' => $Game->ToArray(),
							'stats' => array() //TODO
						);
					}
					$this->SendResponse( $Peer, $Response );
					break;
				default:
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
		return array(
			'id' => $this->GetId(),
			'type' => $this->GetType(),
			'hp' => $this->GetHp(),
			'max_hp' => $this->GetMaxHp(),
			'dps' => $this->GetDps(),
			'timer' => $this->GetTimer(),
			'gold' => $this->GetGold()
		);
	}

	public function GetId()
	{
		return $this->Id;
	}

	public function GetType()
	{
		return $this->Id;
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
	private $GameId;
	private $Level;
	private $Lanes;
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
	
	public function __construct()
	{
		//TODO: Add waiting logic and set proper status $this->SetStatus( EMiniGameStatus::WaitingForPlayers );
		$this->GameId = 1;
		$this->SetLevel( 1 );
		$this->GenerateNewLanes();
		$this->SetStatus( EMiniGameStatus::Running );
		$this->TimestampGameStart = time();
		l( 'Created game #' . $this->GetId() );
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

	public function GetId()
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
		$Enemies = array();

		// Create 1 enemy
		$Enemies[] = new CTowerAttackEnemy(
			$this->GetNextMobId(),
			ETowerAttackEnemyType::Mob,
			1, //hp
			1, //max hp
			1, //dps
			null, //timer
			1 //gold
		);

		$ActivePlayerAbilities = array();
		$PlayerHpBuckets = array();

		// Create 3 lanes
		for ( $i = 0; 3 > $i; $i++ ) {
			$this->Lanes[] = new CTowerAttackLane(
				$Enemies,
				0, //dps
				0, //gold dropped
				$ActivePlayerAbilities,
				$PlayerHpBuckets,
				ETowerAttackElement::Fire, //element
				0, //decrease cooldown
				0 //gold per click
			);
		}
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
}
