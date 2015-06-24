<?php
namespace SteamDB\CTowerAttack\Enums;

class EEnemyType
{
	const Tower = 0; // Spawner
	const Mob = 1; // Creep
	const Boss = 2;
	const MiniBoss = 3;
	const TreasureMob = 4;
	const Max = 5;

	public static function GetList()
	{
        return ( new \ReflectionClass(__CLASS__) )->getConstants();
	}
}
