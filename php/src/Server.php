<?php
namespace SteamDB\CTowerAttack;

use SteamDB\CTowerAttack\Game as Game;

class Server
{
	public $SaneServer;
	public $TickRate;
	private $Shutdown;
	private $Running;
	private $LastTick;
	private $Socket;
	private $Game;
	private $Queue = array();
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
		$this->Running = true;
		
		while( $this->Running )
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
					if( $Data[ 'method' ] == 'ChooseUpgrade' ) 
					{
						$QueueData = $Data[ 'upgrades' ];
						$Response = array(
							'tech_tree' => $Player->GetTechTree()->ToArray()
						);
					}
					else if( $Data[ 'method' ] == 'UseAbilities' ) 
					{
						$QueueData = $Data[ 'requested_abilities' ];
						$Response = array(
							'player_data' => $Player->ToArray()
						);
					}
					$this->Queue[] = array(
						'AccountId' => $Data[ 'access_token' ],
						'Method' => $Data[ 'method' ],
						'Data' => $QueueData
					);
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

				$this->Tick( $Tick );
			}
			
			$DebugTime = microtime( true ) - $DebugTime;
			
			l( 'Spent ' . $DebugTime . ' seconds handling sockets and ticks' );
			
			if( $DebugTime > $this->Game->HighestTick )
			{
				$this->Game->HighestTick = $DebugTime;
			}
			$this->Game->TimeSimulating += $DebugTime;
		}

		socket_shutdown( $this->Socket, 2 );
		socket_close( $this->Socket );
		
		l( 'Sockets closed' );
	}

	private function Tick( $Tick )
	{
		l( 'Ticking...' );

		if( $this->Shutdown > 0 )
		{
			if( $Tick - $this->Shutdown > Player\Base::ACTIVE_PERIOD )
			{
				l( 'Good bye' );

				$this->Running = false;
			}
		}
		else if( $this->SaneServer )
		{
			pcntl_signal_dispatch();
		}

		// Give Players money (TEMPLORARY)
		foreach( $this->Game->Players as $Player )
		{
			$Player->IncreaseGold(50000);
		}

		foreach( $this->Queue as $Key => $QueueItem )
		{
			$Player = $this->Game->GetPlayer( $QueueItem[ 'AccountId' ] );
			if( $Player !== null ) 
			{
				if( $QueueItem[ 'Method' ] == 'ChooseUpgrade' ) 
				{
					$Player->HandleUpgrade( $this->Game, $QueueItem[ 'Data' ] );
					$this->Game->UpdatePlayer( $Player );
				} 
				else if( $QueueItem[ 'method' ] == 'UseAbilities' ) 
				{
					$Player->HandleAbilityUsage( $this->Game, $QueueItem[ 'Data' ] );
					$this->Game->UpdatePlayer( $Player );
				}
			}
			unset( $this->Queue[ $Key ] );
		}
		$this->Game->Update();
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

	public function Shutdown()
	{
		if( $this->Shutdown > 0 )
		{
			return;
		}

		$this->Shutdown = microtime( true );

		$this->Game->SetStatus( \EMiniGameStatus::Ended );
		
		l( 'Waiting ' . Player\Base::ACTIVE_PERIOD . ' seconds until shutdown' );
	}
}
