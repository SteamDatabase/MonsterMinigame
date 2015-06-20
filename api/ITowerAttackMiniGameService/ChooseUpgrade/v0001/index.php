<?php
	if( empty( $_POST[ 'upgrades' ] ) || empty( $_POST[ 'access_token' ] ) )
	{
		http_response_code( 400 );
		die;
	}
	
	require __DIR__ . '/../../Init.php';
	
	$Upgrades = json_decode( $_POST[ 'upgrades' ], true );
	
	if( empty( $Upgrades ) || !is_array( $Upgrades ) )
	{
		http_response_code( 400 );
		die;
	}
	
	foreach( $Upgrades as $Upgrade )
	{
		if( !is_int( $Upgrade ) )
		{
			http_response_code( 400 );
			die;
		}
		
		// TODO: Validate if this upgrade exists
	}
	
	Handle( INPUT_POST, [
		'method' => 'ChooseUpgrade',
		'access_token' => $_POST[ 'access_token' ],
		'upgrades' => $Upgrades
	] );
