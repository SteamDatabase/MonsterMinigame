<?php
namespace SteamDB\CTowerAttack\Enums;

class EAbilityType
{
	const Action = 0;
	const Support = 1;
	const Offensive = 2;
	const Item = 3;
	const MaxTypes = 4;

	public static function GetList()
	{
        return ( new \ReflectionClass(__CLASS__) )->getConstants();
	}
}
