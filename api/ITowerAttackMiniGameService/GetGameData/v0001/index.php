<?php
	require __DIR__ . '/../../Init.php';
	
	Handle( INPUT_GET, [
		'method' => 'GetGameData',
		'include_stats' => isset( $_GET[ 'include_stats' ] ) && $_GET[ 'include_stats' ]
	] );
