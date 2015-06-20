<?php
namespace SteamDB\CTowerAttack;

use SteamDB\CTowerAttack\Game as Game;

class Server
{
	public $TickRate;
	private $LastTick;
	private $Socket;
	private $Game;
	protected static $TuningData = array();

	public function __construct( $Port )
	{
		$this->Socket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );

		socket_bind( $this->Socket, 'localhost', $Port);
		socket_listen( $this->Socket, 5 );

		l( 'Listening on port ' . $Port );

		self::LoadTuningData();

		$this->Game = new Game();
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
				case 'ChatMessage':
					$Response = true;
					
					$this->Game->Chat[] =
					[
						'time' => time(),
						'actor' => $Data[ 'steamid' ],
						'message' => $Data[ 'message' ]
					];
					break;
				case 'GetGameData':
					$Response =
					[
						'game_data' => $this->Game->ToArray()
					];
					
					if( $Data[ 'include_stats' ] )
					{
						$Response[ 'stats' ] = $this->Game->GetStats();
					}
					break;
				case 'GetPlayerData':
					$Player = $this->Game->GetPlayer( $Data[ 'steamid' ] );
					if( $Player !== null ) 
					{
						$Response = array(
							'player_data' => $Player->ToArray(),
							'tech_tree' => $Player->GetTechTree()->ToArray()
						);
					}
					break;
				case 'UseBadgePoints':
				case 'ChooseUpgrade':
				case 'UseAbilities':
					// TODO: use ticks/queue instead
					$SteamId = $Data[ 'access_token' ];
					$Player = $this->Game->GetPlayer( $SteamId );
					if( $Player !== null ) 
					{
						if( $Data[ 'method' ] == 'ChooseUpgrade' ) 
						{
							$Player->HandleUpgrade( $this->Game, $Data[ 'upgrades' ] );
							$this->Game->UpdatePlayer( $Player );
							$this->UpdateGame();
							$Response = array(
								'tech_tree' => $Player->GetTechTree()->ToArray()
							);
						} 
						else if( $Data[ 'method' ] == 'UseAbilities' ) 
						{
							$Player->HandleAbilityUsage( $this->Game, $Data[ 'requested_abilities' ] );
							$this->Game->UpdatePlayer( $Player );
							$this->UpdateGame();
							$Response = array(
								'player_data' => $Player->ToArray()
							);
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
			
			if( $DebugTime > $this->Game->HighestTick )
			{
				$this->Game->HighestTick = $DebugTime;
			}
			$this->Game->TimeSimulating += $DebugTime;
		}
	}

	private function Tick()
	{
		l( 'Ticking...' );

		// Give Players money (TEMPLORARY)
		foreach( $this->Game->Players as $Player )
		{
			$Player->IncreaseGold(50000);
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

	public function GetGame()
	{
		return $this->Game;
	}

	public function UpdateGame()
	{
		/*foreach( $Game->GetLanes() as $Lane ) {
			foreach( $Lane->GetEnemies() as $Enemy ) {
				if ($Enemey->GetHp() <= 0) {}
			}
		}*/
		// TODO: do something or something?
	}
}
