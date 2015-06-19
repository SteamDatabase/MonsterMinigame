<?php
	if( !isset( $_GET[ 'format' ] ) || $_GET[ 'format' ] !== 'json' )
	{
		http_response_code( 400 );
		die;
	}
	
	if( empty( $_GET[ 'gameid' ] ) )
	{
		http_response_code( 400 );
		die;
	}

	// TODO: include_stats=1 query
	
	header( 'Content-type: application/json' );
	
	$Data = json_encode(array(
		'method' => 'GetGameData',
		'gameid' => $_GET[ 'gameid' ]
	)) . PHP_EOL;
	
	$Socket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
	
	if( socket_connect( $Socket, 'localhost', 5337 ) )
	{
		socket_write( $Socket, $Data, strlen( $Data ) );
		
		$Buffer = '';
		
		while( $Response = @socket_read( $Socket, 2048 ) )
		{
			$Buffer .= $Response;
		}
		
		echo $Buffer;
	}
	
	socket_close( $Socket );
