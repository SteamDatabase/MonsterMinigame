<?php
require __DIR__ . '/api/ITowerAttackMiniGameService/Enums.php';

function l( $String )
{
    echo '[' . date( DATE_RSS ) . '] ' . $String . PHP_EOL;
}

require 'php/autoload.php';

$Server = new \SteamDB\CTowerAttack\Server( 5337 );
$Server->TickRate = 100 / 1000;
$Server->Listen();
?>