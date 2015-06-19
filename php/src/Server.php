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
		$this->Socket = stream_socket_server( 'udp://127.0.0.1:' . $Port, $errno, $errstr, STREAM_SERVER_BIND );
		
		if( !$this->Socket )
		{
			die( "$errstr ($errno)" );
		}
		
		l( 'Listening on port ' . $Port );

		self::LoadTuningData();
		
		$Game = new Game($this->LastGameId + 1);
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
			
			l( $Peer . ' - ' . $Data[ 'method' ] );

			// Handle the request, this could be moved elsewhere...
			$Response = null;
			switch ( $Data[ 'method' ] ) {
				case 'GetGameData':
					$GameId = $Data[ 'gameid' ];
					$Game = $this->GetGame( $GameId );
					$Response = null;
					if( $Game !== null ) {
						$Response = array(
							'game_data' => $Game->ToArray(),
							'stats' => $Game->GetStats()
						);
					}
					break;
				case 'GetPlayerData':
					$GameId = $Data[ 'gameid' ];
					$SteamId = $Data[ 'steamid' ];
					$Game = $this->GetGame( $GameId );
					$Response = null;
					if( $Game !== null ) {
						$Player = $Game->GetPlayer( $SteamId );
						if( $Player !== null ) {
							$Response = array(
								'player_data' => $Player->ToArray(),
								'tech_tree' => $Player->GetTechTree()->ToArray()
							);
						}
					}
					break;
				case 'ChooseUpgrade':
				case 'UseAbilities':
					// TODO: use ticks/queue instead
					$AccessToken = $Data[ 'access_token' ];
					$InputJson = $Data[ 'input_json' ];
					$Input = json_decode( $InputJson, true );
					$Game = $this->GetGame( $Input[ 'gameid' ] );
					$Response = null;
					if( $Game !== null ) {
						$SteamId = $this->GetSteamIdFromAccessToken( $AccessToken );
						$Player = $Game->GetPlayer( $SteamId );
						if( $Player !== null ) {
							if( $Data[ 'method' ] == 'ChooseUpgrade' ) {
								$Player->HandleUpgrade( $Input[ 'upgrades' ] );
								$Game->UpdatePlayer( $Player );
								$this->UpdateGame( $Game );
								$Response = array(
									'player_data' => $Player->ToArray()
								);
							} else if( $Data[ 'method' ] == 'UseAbilities' ) {
								$Player->HandleAbilityUsage( $Game, $Input[ 'requested_abilities' ] );
								$Game->UpdatePlayer( $Player );
								$this->UpdateGame( $Game );
								$Response = array(
									'tech_tree' => $Player->GetTechTree()->ToArray()
								);
							}
						}
					}
					break;
				default:
					// TODO: handle unknown methods
					break;
			}

			$this->SendResponse( $Peer, $Response );
			
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

	public function GetSteamIdFromAccessToken( $AccessToken )
	{
		// TODO: pls
		return "76561197990586091";
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
?>