<?php
namespace SteamDB\CTowerAttack;

class Logger extends \Psr\Log\AbstractLogger
{

	public function log( $level, $message, array $context = array() )
	{
		if( $level === \Psr\Log\LogLevel::INFO ) # TODO: only display INFO messages for now
		{
			echo '[' . date( DATE_RSS ) . '] ' . '[' . $level . '] ' . $message . PHP_EOL;
		}
	}
}
