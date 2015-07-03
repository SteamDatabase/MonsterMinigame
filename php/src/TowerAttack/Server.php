<?php
namespace SteamDB\CTowerAttack;

class Server
{
	public $SaneServer;
	private $Running;
	private $LastTick;
	private $LastSecond;
	private $Socket;
	private $Game;
	private static $Logger;
	protected $TickRate;
	protected static $TuningData = array();

	public function __construct( $Port )
	{
		self::$Logger = new Logger;
		$this->Socket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );

		socket_bind( $this->Socket, 'localhost', $Port);
		socket_listen( $this->Socket, 5 );

		self::GetLogger()->info( 'Server is listening on port ' . $Port );

		self::LoadTuningData();

		$this->TickRate = self::GetTuningData( 'tick_rate' );
		$this->Game = new Game( 44925 ); #TODO: dynamic room ids
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

			self::GetLogger()->debug( ( isset( $Data[ 'steamid' ] ) ? ( $Data[ 'steamid' ] . ' - ' ) : '' ) . $Data[ 'method' ] );

			// Handle the request, this could be moved elsewhere...
			$Response = [];

			switch ( $Data[ 'method' ] )
			{
				case 'ChatMessage':
					$Player = $this->Game->GetPlayer( $Data[ 'steamid' ] );

					if( $Player === null )
					{
						break;
					}

					$Response = true;

					$this->Game->AddChatEntry( 'chat', $Player->PlayerName, $Data[ 'message' ] ); // Message is truncated to 500 characters on API level

					// TODO: This is for debugging only, remove later
					if( function_exists( 'SendToIRC' ) )
					{
						SendToIRC( "[GAME] \x0312" . $Player->PlayerName . "\x0F said: " . $Data[ 'message' ] );
					}

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

					if( $Player === null )
					{
						// TODO: for now
						$Player = $this->Game->CreatePlayer( $Data[ 'steamid' ], $Data[ 'player_name' ] );
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

					if( $Data[ 'include_stats' ] )
					{
						$Response[ 'stats' ] = $Player->Stats->ToArray();
					}

					break;
				case 'UseBadgePoints':
					$Player = $this->Game->GetPlayer( $Data[ 'steamid' ] );

					if( $Player === null )
					{
						break;
					}

					$Player->HandleBadgePoints( $this->Game, $Data[ 'ability_items' ] );
					$this->Game->UpdatePlayer( $Player );
					$Response =
					[
						'player_data' => $Player->ToArray(),
						'tech_tree' => $Player->GetTechTree()->ToArray()
					];

					break;
				case 'ChooseUpgrade':
					$Player = $this->Game->GetPlayer( $Data[ 'steamid' ] );

					if( $Player === null )
					{
						break;
					}

					$Player->HandleUpgrade( $this->Game, $Data[ 'upgrades' ] );
					$this->Game->UpdatePlayer( $Player );
					$Response =
					[
						'tech_tree' => $Player->GetTechTree()->ToArray()
					];

					break;
				case 'UseAbilities':
					$Player = $this->Game->GetPlayer( $Data[ 'steamid' ] );

					if( $Player === null )
					{
						break;
					}

					$Player->HandleAbilityUsage( $this->Game, $Data[ 'requested_abilities' ] );
					$this->Game->UpdatePlayer( $Player );

					$Response =
					[
						'player_data' => $Player->ToArray()
					];

					foreach( $Data[ 'requested_abilities' ] as $RequestedAbility )
					{
						// We don't need to send techtree if user only sent clicks to target change
						if( $RequestedAbility[ 'ability' ] !== Enums\EAbility::Attack
						||  $RequestedAbility[ 'ability' ] !== Enums\EAbility::ChangeTarget )
						{
							$Response[ 'tech_tree' ] = $Player->GetTechTree()->ToArray();
							break;
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
				
				$this->Game->TotalTicks++;
			}

			$DebugTime = microtime( true ) - $DebugTime;

			self::GetLogger()->debug( 'Spent ' . number_format( $DebugTime, 7 ) . ' seconds handling sockets and ticks' );

			if( $DebugTime > $this->Game->HighestTick )
			{
				$this->Game->HighestTick = $DebugTime;
			}

			$this->Game->TimeSimulating += $DebugTime;
		}

		socket_shutdown( $this->Socket, 2 );
		socket_close( $this->Socket );

		self::GetLogger()->info( 'Sockets closed' );
	}

	private function Tick( $Tick, $SecondsPassed )
	{
		self::GetLogger()->debug( 'Ticking... seconds passed: ' . $SecondsPassed );

		if( $this->SaneServer )
		{
			pcntl_signal_dispatch();
		}

		$this->Game->Update( $SecondsPassed );
	}

	public static function LoadTuningData()
	{
		$File = file_get_contents( __DIR__ . '/../../files/tuningData.json' );

		self::$TuningData = json_decode( $File, true );

		if( empty( $File ) )
		{
			self::GetLogger()->error( 'Failed to load tuning data' );

			die;
		}
	}

	public static function GetTuningData( $key = null )
	{
		if ( empty( self::$TuningData ) ) {
			self::LoadTuningData();
		}
		return $key !== null ? self::$TuningData[$key] : self::$TuningData;
	}

	public function GetGame()
	{
		return $this->Game;
	}

	public function Shutdown()
	{
		if( $this->Running )
		{
			$this->Running = false;

			self::GetLogger()->info( 'Good bye' );
		}
	}

	public static function GetLogger()
	{
		return self::$Logger;
	}
}
