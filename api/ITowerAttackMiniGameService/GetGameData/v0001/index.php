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
	
	require __DIR__ . '/../../Init.php';
	
	Handle( [
		'method' => 'GetGameData',
		'gameid' => $_GET[ 'gameid' ]
	] );
