<?php
namespace SteamDB\CTowerAttack;

class Server
{
	const VERSION = 'Beta v0.5';
	const TICK_RATE = 0.1; // 100 / 1000

	public $SaneServer;
	private $Shutdown;
	private $Running;
	private $LastTick;
	private $LastSecond;
	private $Socket;
	private $Game;
	private $Queue;
	protected static $TuningData = array();

	public function __construct( $Port )
	{
		$this->Queue = new \SplQueue();
		$this->Queue->setIteratorMode( \SplDoublyLinkedList::IT_MODE_DELETE );

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
			$Response = [];

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
						'version' => self::VERSION,
						'game_data' => $this->Game->ToArray(),
					];

					if( $Data[ 'include_stats' ] )
					{
						$Response[ 'stats' ] = $this->Game->GetStats();
					}

					break;
				case 'GetPlayerData':
					$Player = $this->Game->GetPlayer( $Data[ 'steamid' ] );

					if( $Player === null )
					{
						// TODO: for now
						$Player = $this->Game->CreatePlayer( $Data[ 'steamid' ] );
						//break;
					}

					$Response =
					[
						'player_data' => $Player->ToArray(),
					];

					if( $Data[ 'include_tech_tree' ] )
					{
						$Response[ 'tech_tree' ] = $Player->GetTechTree()->ToArray();
					}

					break;
				case 'UseBadgePoints':
				case 'ChooseUpgrade':
				case 'UseAbilities':
					$Player = $this->Game->GetPlayer( $Data[ 'steamid' ] );

					if( $Player === null )
					{
						$Player = $this->Game->CreatePlayer( $Data[ 'steamid' ] );
					}

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

					$this->Queue->enqueue( [
						'Player' => $Player,
						'Method' => $Data[ 'method' ],
						'Data' => $QueueData
					] );

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
				$this->LastTick = $Tick + self::TICK_RATE;

				if( $Tick >= $this->LastSecond )
				{
					$SecondsPassed = isset( $this->LastSecond ) ? floor( $Tick + 1.0 - $this->LastSecond ) : false;

					$this->LastSecond = $Tick + 1.0; // constant rate, does not change

					$this->Tick( $Tick, $SecondsPassed );
				}
				else
				{
					$this->Tick( $Tick, false );
				}
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

	private function Tick( $Tick, $SecondsPassed )
	{
		l( 'Ticking... seconds passed: ' . $SecondsPassed );

		if( $this->Shutdown > 0 )
		{
			if( $Tick - $this->Shutdown > Player\Player::ACTIVE_PERIOD )
			{
				l( 'Good bye' );

				$this->Running = false;
			}
		}
		else if( $this->SaneServer )
		{
			pcntl_signal_dispatch();
		}

		foreach( $this->Queue as $QueueItem )
		{
			$Player = $QueueItem[ 'Player' ];

			if( $QueueItem[ 'Method' ] == 'ChooseUpgrade' )
			{
				$Player->HandleUpgrade( $this->Game, $QueueItem[ 'Data' ] );
				$this->Game->UpdatePlayer( $Player );
			}
			else if( $QueueItem[ 'Method' ] == 'UseAbilities' )
			{
				$Player->HandleAbilityUsage( $this->Game, $QueueItem[ 'Data' ] );
				$this->Game->UpdatePlayer( $Player );
			}
		}

		$this->Game->Update( $SecondsPassed );
	}

	public static function LoadTuningData()
	{
		$file = file_get_contents( __DIR__ . '/../files/tuningData.json' );

		self::$TuningData = json_decode( $file, true );

		if( empty( $file ) )
		{
			l( 'Failed to load tuning data' );

			die;
		}
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

		// kill it straight away for now
		$this->Running = false;

		$this->Shutdown = microtime( true );

		$this->Game->SetStatus( Enums\EStatus::Ended );

		l( 'Waiting ' . Player\Player::ACTIVE_PERIOD . ' seconds until shutdown' );
	}
}
