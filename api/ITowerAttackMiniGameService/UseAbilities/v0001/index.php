<?php
	if( !isset( $_POST[ 'format' ] ) || $_POST[ 'format' ] !== 'json' )
	{
		http_response_code( 400 );
		die;
	}
	
	if( empty( $_POST[ 'input_json' ] ) )
	{
		http_response_code( 400 );
		die;
	}
	
	header( 'Content-type: application/json' );
	echo file_get_contents( 'UseAbilities.json' );
	
	$Data = $_POST[ 'input_json' ];
	
	$Socket = socket_create( AF_INET, SOCK_DGRAM, SOL_UDP ); 
	socket_sendto( $Socket, $Data, strlen( $Data ), 0, '127.0.0.1', 5337 );
