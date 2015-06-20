<?php

class EMiniGameStatus
{
	const Invalid = 0;
	const WaitingForPlayers = 1;
	const Running = 2;
	const Ended = 3;
}

class ETowerAttackEnemyType
{
	const Tower = 0; // Spawner
	const Mob = 1; // Creep
	const Boss = 2;
	const MiniBoss = 3;
	const TreasureMob = 4;
	const Max = 5;
}

class ETowerAttackElement
{
	const Invalid = 0;
	const Fire = 1;
	const Water = 2;
	const Air = 3;
	const Earth = 4;
}

class ETowerAttackAbility
{
	const Invalid = 0;
	const Attack = 1;
	const ChangeLane = 2;
	const Respawn = 3;
	const ChangeTarget = 4;

	// support abilities
	const Support_IncreaseDamage = 5;
	const Support_IncreaseCritPercentage = 6;
	const Support_Heal = 7;
	const Support_IncreaseGoldDropped = 8;
	const Support_DecreaseCooldowns = 9;

	// offensive abilities
	const Offensive_HighDamageOneTarget = 10;
	const Offensive_DamageAllTargets = 11;
	const Offensive_DOTAllTargets = 12;

	// item
	const Item_Resurrection = 13;
	const Item_KillTower = 14;
	const Item_KillMob = 15;
	const Item_MaxElementalDamage = 16;
	const Item_GoldPerClick = 17;
	const Item_IncreaseCritPercentagePermanently = 18;
	const Item_IncreaseHPPermanently = 19;
	const Item_GoldForDamage = 20;
	const Item_Invulnerability = 21;
	const Item_GiveGold = 22;
	const Item_StealHealth = 23;
	const Item_ReflectDamage = 24;
	const Item_GiveRandomItem = 25;
	const Item_SkipLevels = 26;
	const Item_ClearCooldowns = 27;
	const Item_Start = 13;
	const Item_End = 27;

	const MaxAbilities = 28;
}

class ETowerAttackUpgradeType
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
}
