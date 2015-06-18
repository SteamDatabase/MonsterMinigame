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

	// TODO: include_tech_tree=1 query
	
	header( 'Content-type: application/json' );
	
	$Data = json_encode(array(
		'method' => 'GetPlayerData',
		'gameid' => $_GET[ 'gameid' ],
		'steamid' => $_GET[ 'steamid' ],
		'include_tech_tree' => 1
	));
	
	$Socket = socket_create( AF_INET, SOCK_DGRAM, SOL_UDP ); 
	if ( socket_bind( $Socket, '127.0.0.1' ) === false ) {
	    die( "Error socket_bind():" . socket_strerror( socket_last_error( $Socket ) ) );
	}

	socket_sendto( $Socket, $Data, strlen( $Data ), 0, '127.0.0.1', 5337 );
	socket_recvfrom( $Socket, $Buffer, 4096, 0, $From, $Port ); // TODO: Proper buffer 4096?
	socket_close( $Socket );
	echo $Buffer;
?>