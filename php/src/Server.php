<?php
namespace SteamDB\CTowerAttack;

use SteamDB\CTowerAttack\Game as Game;

class Server
{
	public $TickRate;
	private $LastTick;
	private $Socket;
	private $LastGameId = 0;
	private $Games;
	protected static $TuningData = array();

	public function __construct( $Port )
	{
		$this->Socket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );

		socket_bind( $this->Socket, 'localhost', $Port);
		socket_listen( $this->Socket, 5 );

		l( 'Listening on port ' . $Port );

		self::LoadTuningData();

		$Game = new Game($this->LastGameId + 1);
		$this->Games[$Game->GetGameId()] = $Game;
	}

	public function Listen( )
	{
		while( true )
		{
			$Message = socket_accept( $this->Socket );

			$DebugTime = microtime( true ); 

			$Data = socket_read( $Message, 2048 );
			$Data = json_decode( $Data, TRUE );

			if( !isset( $Data[ 'method' ] ) )
			{
				socket_shutdown( $Message, 2 );
				socket_close( $Message );
				
				// Require all data sent to the server to be a JSON object and contain the "method" key, ignore everything else.
			    continue;
			}

			l( $Data[ 'method' ] );

			// Handle the request, this could be moved elsewhere...
			$Response = null;
			switch ( $Data[ 'method' ] ) 
			{
				case 'GetGameData':
					$Game = $this->GetGame( $Data[ 'gameid' ] );
					$Response = null;
					if( $Game !== null ) 
					{
						$Response =
						[
							'game_data' => $Game->ToArray()
						];
						
						if( $Data[ 'include_stats' ] )
						{
							$Response[ 'stats' ] = $Game->GetStats();
						}
					}
					break;
				case 'GetPlayerData':
					$Game = $this->GetGame( $Data[ 'gameid' ] );
					$Response = null;
					if( $Game !== null ) 
					{
						$Player = $Game->GetPlayer( $Data[ 'steamid' ] );
						if( $Player !== null ) 
						{
							$Response = array(
								'player_data' => $Player->ToArray(),
								'tech_tree' => $Player->GetTechTree()->ToArray()
							);
						}
					}
					break;
				case 'UseBadgePoints':
				case 'ChooseUpgrade':
				case 'UseAbilities':
					// TODO: use ticks/queue instead
					$SteamId = $Data[ 'access_token' ];
					$Game = $this->GetGame( $Data[ 'gameid' ] );
					$Response = null;
					if( $Game !== null ) 
					{
						$Player = $Game->GetPlayer( $SteamId );
						if( $Player !== null ) 
						{
							if( $Data[ 'method' ] == 'ChooseUpgrade' ) 
							{
								$Player->HandleUpgrade( $Game, $Data[ 'upgrades' ] );
								$Game->UpdatePlayer( $Player );
								$this->UpdateGame( $Game );
								$Response = array(
									'tech_tree' => $Player->GetTechTree()->ToArray()
								);
							} 
							else if( $Data[ 'method' ] == 'UseAbilities' ) 
							{
								$Player->HandleAbilityUsage( $Game, $Data[ 'requested_abilities' ] );
								$Game->UpdatePlayer( $Player );
								$this->UpdateGame( $Game );
								$Response = array(
									'player_data' => $Player->ToArray()
								);
							}
						}
					}
					break;
				default:
					// TODO: handle unknown methods
					break;
			}

			$Response = json_encode( [ 'response' => $Response ], JSON_PRETTY_PRINT );

			socket_write( $Message, $Response );
			socket_shutdown( $Message, 1 );
			socket_close( $Message );

			$Tick = microtime( true );

			if( $Tick >= $this->LastTick )
			{
				$this->LastTick = $Tick + $this->TickRate;

				$this->Tick();
			}
			
			$DebugTime = microtime( true ) - $DebugTime;
			
			l( 'Spent ' . $DebugTime . ' seconds handling sockets and ticks' );
			
			$this->Games[ $Game->GetGameId() ]->TimeSimulating += $DebugTime;
			
			if( $DebugTime > $this->Games[ $Game->GetGameId() ]->HighestTick )
			{
				$this->Games[ $Game->GetGameId() ]->HighestTick = $DebugTime;
			}
		}
	}

	private function Tick()
	{
		l( 'Ticking...' );

		// Give Players money (TEMPLORARY)
		foreach($this->GetGames() as $Game)
		{
			foreach($Game->GetPlayers() as $Player)
			{
				$Player->IncreaseGold(50000);
			}
		}
	}

	public static function LoadTuningData()
	{
		$file = file_get_contents( FILES_DIR . 'tuningData.json' );
		self::$TuningData = json_decode( $file, true );
	}

	public static function GetTuningData( $key = null )
	{
		return $key !== null ? self::$TuningData[$key] : self::$TuningData;
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

	public function UpdateGame( $Game )
	{
		/*foreach( $Game->GetLanes() as $Lane ) {
			foreach( $Lane->GetEnemies() as $Enemy ) {
				if ($Enemey->GetHp() <= 0) {}
			}
		}*/
		$this->Games[ $Game->GetGameId() ] = $Game;
	}
}
