<?php
	if( empty( $_GET[ 'gameid' ] ) || empty( $_GET[ 'steamid' ] ) )
	{
		http_response_code( 400 );
		die;
	}
	
	require __DIR__ . '/../../Init.php';
	
	session_start();
	
	Handle( INPUT_GET, [
		'method' => 'GetPlayerData',
		'steamid' => filter_input( INPUT_GET, 'steamid' ),
		'player_name' => isset( $_SESSION[ 'Name' ] ) ? mb_substr( $_SESSION[ 'Name' ], 0, 22, 'UTF-8' ) : '[unnamed]',
		'include_tech_tree' => isset( $_GET[ 'include_tech_tree' ] ) && $_GET[ 'include_tech_tree' ],
		'include_stats' => isset( $_GET[ 'include_stats' ] ) && $_GET[ 'include_stats' ]
	] );
