<?php
namespace SteamDB\CTowerAttack\Enums;

class EUpgradeType
{
	const HitPoints = 0;
	const DPS = 1;
	const ClickDamage = 2;
	const DamageMultiplier_Fire = 3;
	const DamageMultiplier_Water = 4;
	const DamageMultiplier_Air = 5;
	const DamageMultiplier_Earth = 6;
	const DamageMultiplier_Crit = 7;
	const PurchaseAbility = 8;
	const BossLootDropPercentage = 9;
	const MaxTypes = 10;

	public static function GetList()
	{
        return ( new \ReflectionClass(__CLASS__) )->getConstants();
	}
}
