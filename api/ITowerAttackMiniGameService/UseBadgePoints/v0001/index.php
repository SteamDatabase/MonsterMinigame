<?php
	if( empty( $_POST[ 'ability_items' ] ) || empty( $_POST[ 'access_token' ] ) )
	{
		http_response_code( 400 );
		die;
	}
	
	require __DIR__ . '/../../Init.php';
	
	$Abilities = json_decode( $_POST[ 'ability_items' ], true );
	
	if( empty( $Abilities ) || !is_array( $Abilities ) )
	{
		http_response_code( 400 );
		die;
	}
	
	foreach( $Abilities as $Ability )
	{
		if( !is_int( $Ability ) )
		{
			http_response_code( 400 );
			die;
		}
		
		// TODO: Validate if this ability exists
	}
	
	Handle( INPUT_POST, [
		'method' => 'UseBadgePoints',
		'access_token' => $_POST[ 'access_token' ],
		'ability_items' => $Abilities
	] );
