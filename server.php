<?php
require __DIR__ . '/api/ITowerAttackMiniGameService/Enums.php';

function SignalHandler( $Signal )
{
	l( 'Caught signal ' . $Signal );
	
	global $Server; // ayy

	$Server->Shutdown();
}

function l( $String )
{
    echo '[' . date( DATE_RSS ) . '] ' . $String . PHP_EOL;
}

foreach( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( __DIR__ . '/php/src/' ) ) as $File )
{
	if( pathinfo( $File, PATHINFO_EXTENSION ) !== 'php' )
	{
		continue;
	}

    require $File;
}

$Server = new \SteamDB\CTowerAttack\Server( 5337 );

if( function_exists( 'pcntl_signal' ) )
{
	$Server->SaneServer = true;

	pcntl_signal( SIGTERM, 'SignalHandler' );
	pcntl_signal( SIGINT, 'SignalHandler' );
}

$Server->Listen();
