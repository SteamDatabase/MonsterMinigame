<?php
	if( !isset( $_GET[ 'format' ] ) || $_GET[ 'format' ] !== 'json' )
	{
		http_response_code( 400 );
		die;
	}
	
	header( 'Content-type: application/json' );
	
	require __DIR__ . '/../../Enums.php';
	
	$Lane =
	[
		'element' => 4,
		'dps' => 0,
		'active_player_ability_gold_per_click' => 0,
		'active_player_ability_decrease_cooldowns' => 0.25,
		'active_player_abilities' =>
		[
			
		],
		'player_hp_buckets' =>
		[
			
		],
		'enemies' =>
		[
			[
				'id' => 2353793,
				'type' => ETowerAttackEnemyType::Boss,
				'hp' => 6851814772839,
				'max_hp' => 6851814772839,
				'dps' => 2805538,
				'timer' => 1.01,
				'gold' => 118943606
			]
		]
	];
	
	$GameData =
	[
		'level' => 1000000 - 1, // level is zero based
		'status' => EMiniGameStatus::Running,
		'timestamp' => time(),
		'timestamp_game_start' => 1337,
		'timestamp_level_start' => 1338,
		'lanes' =>
		[
			$Lane,
			$Lane,
			$Lane
		]
	];
	
	echo json_encode(
		[
			'response' =>
			[
				'game_data' => $GameData
			]
		] 
	);
