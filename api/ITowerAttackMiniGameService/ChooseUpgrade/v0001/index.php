<?php
	if( !isset( $_POST[ 'format' ] ) || $_POST[ 'format' ] !== 'json' )
	{
		http_response_code( 400 );
		die;
	}
	
	if( empty( $_POST[ 'input_json' ] ) || empty( $_POST[ 'access_token' ] ) )
	{
		http_response_code( 400 );
		die;
	}
	
	header( 'Content-type: application/json' );
	
	$Data = json_encode(array(
		'method' => 'ChooseUpgrade',
		'access_token' => $_POST[ 'access_token' ],
		'input_json' => $_POST['input_json']
	));
	
	$Socket = socket_create( AF_INET, SOCK_DGRAM, SOL_UDP ); 
	if ( socket_bind( $Socket, '127.0.0.1' ) === false ) {
	    die( "Error socket_bind():" . socket_strerror( socket_last_error( $Socket ) ) );
	}

	socket_sendto( $Socket, $Data, strlen( $Data ), 0, '127.0.0.1', 5337 );
	socket_recvfrom( $Socket, $Buffer, 4096, 0, $From, $Port ); // Proper buffer 4096?
	socket_close( $Socket );
	echo $Buffer;
?>
