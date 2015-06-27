<?php
namespace SteamDB\CTowerAttack;

class Logger extends \Psr\Log\AbstractLogger
{
	public function log( $level, $message, array $context = array() )
	{
		echo '[' . date( DATE_RSS ) . '] ' . '[' . $level . '] ' . $message . PHP_EOL;
	}
}
