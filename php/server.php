<?php
require_once 'autoloader.php';

function SignalHandler( $Signal )
{
	global $Server; // ayy

	$Server->Shutdown();
}

$Loader = new \SteamDB\Psr4AutoloaderClass;

// register the autoloader
$Loader->register();

// register the base directories for the namespace prefix
$Loader->addNamespace('SteamDB\CTowerAttack', __DIR__ . '/src/TowerAttack');
$Loader->addNamespace('Psr', __DIR__ . '/src/Psr');

$Server = new \SteamDB\CTowerAttack\Server( 5337 );

if( function_exists( 'pcntl_signal' ) )
{
	$Server->SaneServer = true;

	pcntl_signal( SIGTERM, 'SignalHandler' );
	pcntl_signal( SIGINT, 'SignalHandler' );
}

$Server->Listen();
