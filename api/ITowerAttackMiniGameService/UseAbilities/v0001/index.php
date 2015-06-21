<?php
	if( empty( $_POST[ 'requested_abilities' ] ) || empty( $_POST[ 'access_token' ] ) )
	{
		http_response_code( 400 );
		die;
	}
	
	require __DIR__ . '/../../Init.php';
	
	$Abilities = json_decode( $_POST[ 'requested_abilities' ], true );
	
	if( empty( $Abilities ) || !is_array( $Abilities ) )
	{
		http_response_code( 400 );
		die;
	}
	
	foreach( $Abilities as $Ability )
	{
		if( !isset( $Ability[ 'ability' ] ) )
		{
			http_response_code( 400 );
			die;
		}
		
		// TODO: Validate if this ability exists
	}
	
	Handle( INPUT_POST, [
		'method' => 'UseAbilities',
		'steamid' => $_POST[ 'access_token' ], // TODO
		'requested_abilities' => $Abilities
	] );
