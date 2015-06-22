<?php
	if( empty( $_GET[ 'gameid' ] ) || empty( $_GET[ 'steamid' ] ) )
	{
		http_response_code( 400 );
		die;
	}
	
	require __DIR__ . '/../../Init.php';
	
	Handle( INPUT_GET, [
		'method' => 'GetPlayerData',
		'steamid' => filter_input( INPUT_GET, 'steamid' ),
		'player_name' => isset( $_SESSION[ 'Name' ] ) ? $_SESSION[ 'Name' ] : '[unnamed]',
		'include_tech_tree' => isset( $_GET[ 'include_tech_tree' ] ) && $_GET[ 'include_tech_tree' ]
	] );
