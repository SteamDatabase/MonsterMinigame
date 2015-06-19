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
	
	require __DIR__ . '/../../Init.php';
	
	Handle( [
		'method' => 'GetPlayerData',
		'gameid' => $_GET[ 'gameid' ],
		'steamid' => $_GET[ 'steamid' ],
		'include_tech_tree' => 1
	] );
