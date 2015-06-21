<?php
	require __DIR__ . '/../../Init.php';
	
	$Result = Handle( INPUT_GET, [
		'method' => 'GetGameData',
		'include_stats' => isset( $_GET[ 'include_stats' ] ) && $_GET[ 'include_stats' ]
	] );

	if( !$Result )
	{
		http_response_code( 200 );
		echo json_encode( [ 'response' => [ 'game_data' => [ 'status' => 3 ] ] ] );
	}
