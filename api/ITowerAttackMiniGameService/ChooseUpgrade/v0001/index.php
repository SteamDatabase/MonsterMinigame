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
