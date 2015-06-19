<?php

header( 'Content-type: application/json' );

function Handle( $Data )
{
	$Data = json_encode( $Data ) . PHP_EOL;
	
	$Socket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
	
	socket_set_option( $Socket, SOL_SOCKET, SO_KEEPALIVE, 0 );
	socket_set_option( $Socket, SOL_SOCKET, SO_REUSEADDR, 1 );
	socket_set_option( $Socket, SOL_SOCKET, SO_RCVTIMEO, [ 'sec' => 1, 'usec' => 0 ] );
	socket_set_option( $Socket, SOL_SOCKET, SO_SNDTIMEO, [ 'sec' => 1, 'usec' => 0 ] );
	
	if( @socket_connect( $Socket, 'localhost', 5337 ) )
	{
		socket_write( $Socket, $Data, strlen( $Data ) );
		socket_shutdown( $Socket, 1 );
		
		$Buffer = '';
		
		while( $Response = socket_read( $Socket, 10, 0 ) )
		{
			$Buffer .= $Response;
		}
		
		echo $Buffer;
		
		socket_shutdown( $Socket, 0 );
	}
	else
	{
		http_response_code( 500 );
	}
	
	socket_close( $Socket );
}
