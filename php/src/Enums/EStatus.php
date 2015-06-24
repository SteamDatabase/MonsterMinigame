<?php
namespace SteamDB\CTowerAttack\Enums;

class EStatus
{
	const Invalid = 0;
	const WaitingForPlayers = 1;
	const Running = 2;
	const Ended = 3;

	public static function GetList()
	{
        return ( new \ReflectionClass(__CLASS__) )->getConstants();
	}
}
