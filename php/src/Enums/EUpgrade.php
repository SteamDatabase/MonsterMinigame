<?php
namespace SteamDB\CTowerAttack\Enums;

class EUpgrade
{
	const Invalid = 0;
	const HitPoints_LightArmor = 0;
	const DPS_AutoFireCanon = 1;
	const ClickDamage_ArmorPiercingRound = 2;
	const DamageMultiplier_Fire = 3;
	const DamageMultiplier_Water = 4;
	const DamageMultiplier_Air = 5;
	const DamageMultiplier_Earth = 6;
	const DamageMultiplier_Crit = 7;
	const HitPoints_HeavyArmor = 8;
	const DPS_AdvancedTargeting = 9;
	const ClickDamage_ExplosiveRounds = 10;
	const PurchaseAbility_Medics = 11;
	const PurchaseAbility_MooraleBooster = 12;
	const PurchaseAbility_GoodLuckCharms = 13;
	const PurchaseAbility_MetalDetector = 14;
	const PurchaseAbility_DecreaseCooldowns = 15;
	const PurchaseAbility_TacticalNuke = 16;
	const PurchaseAbility_ClusterBomb = 17;
	const PurchaseAbility_Napalm = 18;
	const PurchaseAbility_BossLoot = 19;
	const HitPoints_EnergyShields = 20;
	const DPS_FarmingEquipment = 21;
	const ClickDamage_Railgun = 22;
	const HitPoints_PersonalTraining = 23;
	const DPS_AFKEquipment = 24;
	const ClickDamage_NewMouseButton = 25;
	const HitPoints_CyberneticEnhancements = 26;
	const DPS_Level1SentryGun = 27;
	const ClickDamage_TitaniumMouseButton = 28;
	const HitPoints_Exoskeleton = 29;
	const DPS_Level2SentryGun = 30;
	const ClickDamage_DoubleBarrelledMouse = 31;
	const HitPoints_YogaTraining = 32;
	const DPS_Level3SentryGun = 33;
	const ClickDamage_BionicFinger = 34;
	const HitPoints_ProteinShakes = 35;
	const DPS_Level1Drones = 36;
	const ClickDamage_TwoBionicFingers = 37;
	const HitPoints_MartialArtsTraining = 38;
	const DPS_Level2Drones = 39;
	const ClickDamage_BionicHand = 40;
	const HitPoints_SeriouslyAnotherHpUpgrade = 41;
	const DPS_Level3Drones = 42;
	const ClickDamage_BionicArm = 43;
	const MaxUpgrades = 44;

	public static function GetList()
	{
        return ( new \ReflectionClass(__CLASS__) )->getConstants();
	}
}
