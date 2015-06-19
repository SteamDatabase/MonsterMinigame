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
	
	require __DIR__ . '/../../Init.php';
	
	Handle( [
		'method' => 'UseAbilities',
		'access_token' => $_POST[ 'access_token' ],
		'input_json' => $_POST['input_json']
	] );
