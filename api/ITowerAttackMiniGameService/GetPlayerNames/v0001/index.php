<?php
	if( !isset( $_GET[ 'format' ] ) || $_GET[ 'format' ] !== 'json' )
	{
		http_response_code( 400 );
		die;
	}
	
	if( empty( $_GET[ 'gameid' ] ) || empty( $_GET[ 'steamid' ] ) )
	{
		http_response_code( 400 );
		die;
	}

	header( 'Content-type: application/json' );
	
	$Data = json_encode(array(
		'method' => 'GetPlayerNames',
		'input_json' => $_GET[ 'input_json' ]
	));
	
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
