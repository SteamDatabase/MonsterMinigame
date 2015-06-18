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
		'request' => 'GetGameData',
		'gameid' => $_GET[ 'gameid' ]
	));
	
	$Socket = socket_create( AF_INET, SOCK_DGRAM, SOL_UDP ); 
	socket_sendto( $Socket, $Data, strlen( $Data ), 0, '127.0.0.1', 5337 );
