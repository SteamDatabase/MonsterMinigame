<?php
namespace SteamDB\CTowerAttack\Enums;

class ELane
{
	const Left = 0;
	const Center = 1;
	const Right = 2;

	public static function GetList()
	{
        return ( new \ReflectionClass(__CLASS__) )->getConstants();
	}
}
