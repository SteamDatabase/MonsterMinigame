<?php
namespace SteamDB\CTowerAttack\Enums;

class EElement
{
	const Invalid = 0;
	const Fire = 1;
	const Water = 2;
	const Air = 3;
	const Earth = 4;

	public static function GetList()
	{
        return ( new \ReflectionClass(__CLASS__) )->getConstants();
	}
}
