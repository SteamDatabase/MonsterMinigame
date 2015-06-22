<?php
	if( empty( $_POST[ 'access_token' ] ) || empty( $_POST[ 'message' ] ) )
	{
		http_response_code( 400 );
		die;
	}
	
	require __DIR__ . '/../../Init.php';

	Handle( INPUT_POST, [
		'method' => 'ChatMessage',
		'steamid' => filter_input( INPUT_POST, 'access_token' ),
		'message' => substr( filter_input( INPUT_POST, 'message' ), 0, 500 )
	] );
