<?php

require __DIR__ . '/api/ITowerAttackMiniGameService/Enums.php';

$Server = new CTowerAttackServer( 5337 );
$Server->TickRate = 100 / 1000;
$Server->Listen();

class CTowerAttackServer
{
	public $TickRate;
	private $LastTick;
	private $Socket;
	private $AbilityQueue;
	
	public function __construct( $Port )
	{
		$this->Socket = stream_socket_server( 'udp://127.0.0.1:' . $Port, $errno, $errstr, STREAM_SERVER_BIND );
		
		if( !$this->Socket )
		{
			die( "$errstr ($errno)" );
		}
		
		$this->Log( 'Listening on port ' . $Port );
	}
	
	public function Listen( )
	{
		while( true )
		{
			$Data = stream_socket_recvfrom( $this->Socket, 1500, 0, $Peer );
			
			$this->Log( $Peer . ' - ' . $Data );
			
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
		$this->Log( 'Ticking...' );
	}
	
	public function Log( $String )
	{
		echo '[' . date( DATE_RSS ) . '] ' . $String . PHP_EOL;
	}
}
