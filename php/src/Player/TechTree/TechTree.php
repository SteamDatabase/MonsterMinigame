<?php
namespace SteamDB\CTowerAttack\Player\TechTree;

use SteamDB\CTowerAttack\Enums;
use SteamDB\CTowerAttack\Server;

class TechTree
{
	/*
	repeated Upgrade upgrades = 1;
	optional double damage_per_click = 2 [default = 1.0];
	optional double damage_multiplier_fire = 3 [default = 1.0];
	optional double damage_multiplier_water = 4 [default = 1.0];
	optional double damage_multiplier_air = 5 [default = 1.0];
	optional double damage_multiplier_earth = 6 [default = 1.0];
	optional double damage_multiplier_crit = 7 [default = 2.0];
	optional uint64 unlocked_abilities_bitfield = 8 [default = 0];
	optional double hp_multiplier = 9 [default = 1.0];
	optional double crit_percentage = 10 [default = 0];
	optional double badge_points = 11;
	repeated AbilityItem ability_items = 12;
	optional double boss_loot_drop_percentage = 13 [default = 0.25];
	optional double damage_multiplier_dps = 14 [default = 1.0];
	optional double base_dps = 15;
	optional double damage_per_click_multiplier = 16 [default = 1.0];
	optional double max_hp = 17;
	optional double dps = 18;
	*/
	private $Upgrades = array();
	private $DamagePerClick = 1.0;
	private $DamageMultiplierFire = 1.0;
	private $DamageMultiplierWater = 1.0;
	private $DamageMultiplierAir = 1.0;
	private $DamageMultiplierEarth = 1.0;
	private $DamageMultiplierCrit = 2.0;
	private $UnlockedAbilitiesBitfield = 0;
	private $HpMultiplier = 1.0;
	private $CritPercentage = 0;
	private $BadgePoints = 1000; #TODO: Remove
	private $AbilityItems = array();
	private $BossLootDropPercentage = 0.25;
	private $DamageMultiplierDps = 1.0;
	private $DamagePerClickMultiplier = 0;
	private $MaxHp = 0;
	public $BaseDps = 0;
	public $Dps = 0;

	public function __construct() {
		$this->Upgrades = array();
		foreach( Server::GetTuningData( 'upgrades' ) as $UpgradeId => $Upgrade) {
			$this->Upgrades[] = new Upgrade( $UpgradeId, 0, $Upgrade[ 'cost' ] );
		}
		$this->DamagePerClick = $this->GetTuningData( 'damage_per_click' );
		$this->DamageMultiplierFire = $this->GetTuningData( 'damage_multiplier_fire' );
		$this->DamageMultiplierWater = $this->GetTuningData( 'damage_multiplier_water' );
		$this->DamageMultiplierAir = $this->GetTuningData( 'damage_multiplier_air' );
		$this->DamageMultiplierEarth = $this->GetTuningData( 'damage_multiplier_earth' );
		$this->DamageMultiplierCrit = $this->GetTuningData( 'damage_multiplier_crit' );
		$this->UnlockedAbilitiesBitfield = 0;
		$this->CritPercentage = $this->GetTuningData( 'crit_percentage' );
		$this->AbilityItems = [];
		$this->BossLootDropPercentage = $this->GetTuningData( 'loot_chance' );
		$this->DamageMultiplierDps = 1;
		$this->BaseDps = $this->GetTuningData( 'dps' );
		$this->MaxHp = $this->GetTuningData( 'hp' );
		$this->Dps = $this->GetTuningData( 'dps' );
	}

	public function ToArray()
	{
		return array(
			'upgrades' => $this->GetUpgradesArray(),
			'damage_per_click' => (double) $this->GetDamagePerClick(),
			'damage_multiplier_fire' => (double) $this->GetDamageMultiplierFire(),
			'damage_multiplier_water' => (double) $this->GetDamageMultiplierWater(),
			'damage_multiplier_air' => (double) $this->GetDamageMultiplierAir(),
			'damage_multiplier_earth' => (double) $this->GetDamageMultiplierEarth(),
			'damage_multiplier_crit' => (double) $this->GetDamageMultiplierCrit(),
			'unlocked_abilities_bitfield' => (int) $this->GetUnlockedAbilitiesBitfield(),
			'hp_multiplier' => (double) $this->GetHpMultiplier(),
			'crit_percentage' => (double) $this->GetCritPercentage(),
			'badge_points' => (double) $this->GetBadgePoints(),
			'ability_items' => $this->GetAbilityItemsToArray(),
			'boss_loot_drop_percentage' => (double) $this->GetBossLootDropPercentage(),
			'damage_multiplier_dps' => (double) $this->GetDamageMultiplierDps(),
			'base_dps' => (double) $this->GetBaseDps(),
			'damage_per_click_multiplier' => (double) $this->GetDamagePerClickMultiplier(),
			'max_hp' => (double) $this->GetMaxHp(),
			'dps' => (double) $this->GetDps()
		);
	}

	// TODO: @Contex: Do we really need to create a new class? Why not just return the upgrade Ids.
	// TODO: @Contex: Also, shouldn't we use EUpgrade instead of EUpgradeType (even though they all have their own type)
	public function GetElementalUpgrades()
	{
		return array(
			Enums\EUpgradeType::DamageMultiplier_Fire => $this->GetUpgrade( Enums\EUpgradeType::DamageMultiplier_Fire ),
			Enums\EUpgradeType::DamageMultiplier_Water => $this->GetUpgrade( Enums\EUpgradeType::DamageMultiplier_Water ),
			Enums\EUpgradeType::DamageMultiplier_Air => $this->GetUpgrade( Enums\EUpgradeType::DamageMultiplier_Air ),
			Enums\EUpgradeType::DamageMultiplier_Earth => $this->GetUpgrade( Enums\EUpgradeType::DamageMultiplier_Earth )
		);
	}

	public function GetHighestElementalMultiplier()
	{
		return max(
			$this->DamageMultiplierFire,
			$this->DamageMultiplierWater,
			$this->DamageMultiplierAir,
			$this->DamageMultiplierEarth
		);
	}

	// TODO: @Contex: Do we really need to create a new class? Why not just return the upgrade Ids.
	public function GetAbilityUpgrades()
	{
		return [
			Enums\EUpgrade::PurchaseAbility_Medics => $this->GetUpgrade( Enums\EUpgrade::PurchaseAbility_Medics ),
			Enums\EUpgrade::PurchaseAbility_MooraleBooster => $this->GetUpgrade( Enums\EUpgrade::PurchaseAbility_MooraleBooster ),
			Enums\EUpgrade::PurchaseAbility_GoodLuckCharms => $this->GetUpgrade( Enums\EUpgrade::PurchaseAbility_GoodLuckCharms ),
			Enums\EUpgrade::PurchaseAbility_MetalDetector => $this->GetUpgrade( Enums\EUpgrade::PurchaseAbility_MetalDetector ),
			Enums\EUpgrade::PurchaseAbility_DecreaseCooldowns => $this->GetUpgrade( Enums\EUpgrade::PurchaseAbility_DecreaseCooldowns ),
			Enums\EUpgrade::PurchaseAbility_TacticalNuke => $this->GetUpgrade( Enums\EUpgrade::PurchaseAbility_TacticalNuke ),
			Enums\EUpgrade::PurchaseAbility_ClusterBomb => $this->GetUpgrade( Enums\EUpgrade::PurchaseAbility_ClusterBomb ),
			Enums\EUpgrade::PurchaseAbility_BossLoot => $this->GetUpgrade( Enums\EUpgrade::PurchaseAbility_BossLoot )
		];
	}

	public static function GetUpgradeTypeOfElement( $ElementId )
	{
		switch( $ElementId )
		{
			case Enums\EElement::Fire:
				return Enums\EUpgradeType::DamageMultiplier_Fire;
			case Enums\EElement::Water:
				return Enums\EUpgradeType::DamageMultiplier_Water;
			case Enums\EElement::Air:
				return Enums\EUpgradeType::DamageMultiplier_Air;
			case Enums\EElement::Earth:
				return Enums\EUpgradeType::DamageMultiplier_Earth;
		}
	}

	public function GetUpgrade( $UpgradeId )
	{
		return $this->Upgrades[ $UpgradeId ];
	}

	public function GetUpgradeByAbility( $AbilityId )
	{
		foreach( $this->Upgrades as $Upgrade )
		{
			if( Upgrade::GetAbility( $Upgrade->GetUpgradeId() ) === $AbilityId )
			{
				return $Upgrade;
			}
		}
		return null;
	}

	public function GetUpgrades()
	{
		return $this->Upgrades;
	}

	public function GetUpgradesArray()
	{
		$Upgrades = array();
		foreach( $this->GetUpgrades() as $Upgrade ) {
			$Upgrades[] = $Upgrade->ToArray();
		}
		return $Upgrades;
	}

	public function GetUpgradesByType( $UpgradeType , $MinimumLevel = null )
	{
		$Upgrades = array();
		foreach( $this->GetUpgrades() as $Upgrade ) {
			if( $Upgrade->GetType() === $UpgradeType && ( $MinimumLevel !== null ? $Upgrade->GetLevel() >= $MinimumLevel : true ) )
			{
				$Upgrades[] = $Upgrade->ToArray();
			}
		}
		return $Upgrades;
	}

	public function GetExtraDamageMultipliers( $UpgradeType, $IsElement = true )
	{
		if( $IsElement )
		{
			$UpgradeType = self::GetUpgradeTypeOfElement( $UpgradeType );
		}
		$DamageMultiplier = 0;
		switch( $UpgradeType )
		{
			case Enums\EUpgradeType::DamageMultiplier_Fire:
				$DamageMultiplier += $this->DamageMultiplierFire;
				break;
			case Enums\EUpgradeType::DamageMultiplier_Water:
				$DamageMultiplier += $this->DamageMultiplierWater;
				break;
			case Enums\EUpgradeType::DamageMultiplier_Air:
				$DamageMultiplier += $this->DamageMultiplierAir;
				break;
			case Enums\EUpgradeType::DamageMultiplier_Earth:
				$DamageMultiplier += $this->DamageMultiplierEarth;
				break;
			case Enums\EUpgradeType::DamageMultiplier_Crit:
				$DamageMultiplier += $this->DamageMultiplierCrit;
				break;
		}
		return $DamageMultiplier !== 0 ? $DamageMultiplier : 1;
	}

	public function GetDamagePerClick()
	{
		return $this->DamagePerClick;
	}

	public function GetDamageMultiplierFire()
	{
		return $this->DamageMultiplierFire;
	}

	public function GetDamageMultiplierWater()
	{
		return $this->DamageMultiplierWater;
	}

	public function GetDamageMultiplierAir()
	{
		return $this->DamageMultiplierAir;
	}

	public function GetDamageMultiplierEarth()
	{
		return $this->DamageMultiplierEarth;
	}

	public function GetDamageMultiplierCrit()
	{
		return $this->DamageMultiplierCrit;
	}

	public function GetUnlockedAbilitiesBitfield()
	{
		return $this->UnlockedAbilitiesBitfield;
	}

	public function GetHpMultiplier()
	{
		return $this->HpMultiplier;
	}

	public function IncreaseHpMultiplier( $Multiplier )
	{
		$this->HpMultiplier += $Multiplier;
	}

	public function GetCritPercentage()
	{
		return $this->CritPercentage;
	}

	public function IncreaseCritPercentage( $Percent )
	{
		$this->CritPercentage += $Percent;
	}

	public function GetBadgePoints()
	{
		return $this->BadgePoints;
	}

	public function DecreaseBadgePoints( $Amount )
	{
		$this->BadgePoints -= $Amount;
		if( $this->BadgePoints < 0 )
		{
			$this->BadgePoints = 0;
		}
		return $this->BadgePoints;
	}

	public function AddAbilityItem( $AbilityId, $Quantity = 1 )
	{
		if( AbilityItem::GetType( $AbilityId ) !== Enums\EAbilityType::Item )
		{
			$Upgrade = $this->GetUpgradeByAbility( $AbilityId );
			if( $Upgrade !== null && $Upgrade->GetLevel() === 0 ) 
			{
				$Upgrade->IncreaseLevel();
			}
			$this->UnlockAbility( $AbilityId );
			return;
		}
		if ( !isset( $this->AbilityItems[ $AbilityId ] ) )
		{
			$this->AbilityItems[ $AbilityId ] = [
				'ability' => $AbilityId,
				'quantity' => 1
			];
		}
		else
		{
			$this->AbilityItems[ $AbilityId ][ 'quantity' ]++;
		}
	}

	public function RemoveAbilityItem( $AbilityId, $Quantity = 1 )
	{
		if( isset( $this->AbilityItems[ $AbilityId ] ) )
		{
			$this->AbilityItems[ $AbilityId ][ 'quantity' ] -= $Quantity;
			if( $this->AbilityItems[ $AbilityId ][ 'quantity' ] <= 0 )
			{
				unset( $this->AbilityItems[ $AbilityId ] );
			}
		}
	}

	public function HasAbilityItem( $AbilityId )
	{
		if( isset( $this->AbilityItems[ $AbilityId ] ) )
		{
			return $this->AbilityItems[ $AbilityId ][ 'quantity' ] > 0;
		}
		else
		{
			$Upgrade = $this->GetUpgradeByAbility( $AbilityId );
			if( $Upgrade !== null ) 
			{
				return $Upgrade->GetLevel() > 0;
			}
		}
		return false;
	}

	public function GetAbilityQuantity( $AbilityId )
	{
		return $this->AbilityItems[ $AbilityId ][ 'quantity' ];
	}

	public function GetAbilityItem( $AbilityId )
	{
		return $this->AbilityItems[ $AbilityId ];
	}

	public function GetAbilityItems()
	{
		return $this->AbilityItems;
	}

	public function GetAbilityItemsToArray()
	{
		return array_values( $this->AbilityItems );
	}

	public function GetBossLootDropPercentage()
	{
		return $this->BossLootDropPercentage;
	}

	public function GetDamageMultiplierDps()
	{
		return $this->DamageMultiplierDps;
	}

	public function GetBaseDps()
	{
		return $this->BaseDps;
	}

	public function GetDamagePerClickMultiplier()
	{
		return $this->DamagePerClickMultiplier;
	}

	public function GetMaxHp()
	{
		return $this->MaxHp;
	}

	public function IncreaseMaxHp( $Amount )
	{
		$this->MaxHp += $Amount;
	}

	public function GetDps()
	{
		return $this->Dps;
	}

	public function UnlockAbility( $AbilityId )
	{
		$this->UnlockedAbilitiesBitfield |= ( 1 << $AbilityId );
	}

	public function LockAbility( $AbilityId )
	{
		$this->UnlockedAbilitiesBitfield &= ~( 1 << $AbilityId );
	}

	public function RecalulateUpgrades()
	{
		$Data = array(
			'damage_per_click' => (double) $this->GetTuningData( 'damage_per_click' ),
			'damage_multiplier_fire' => (double) $this->GetTuningData( 'damage_multiplier_fire' ),
			'damage_multiplier_water' => (double) $this->GetTuningData( 'damage_multiplier_water' ),
			'damage_multiplier_air' => (double) $this->GetTuningData( 'damage_multiplier_air' ),
			'damage_multiplier_earth' => (double) $this->GetTuningData( 'damage_multiplier_earth' ),
			'damage_multiplier_crit' => (double) $this->GetTuningData( 'damage_multiplier_crit' ),
			'hp_multiplier' => (double) $this->GetHpMultiplier(),
			'crit_percentage' => (double) $this->GetCritPercentage(),
			'boss_loot_drop_percentage' => (double) $this->GetTuningData( 'loot_chance' ),
			'damage_multiplier_dps' => (double) 0,
			'damage_per_click_multiplier' => (double) 1
		);

		foreach( $this->GetUpgrades() as $Upgrade ) 
		{
			$Value = Upgrade::GetMultiplier( $Upgrade->GetUpgradeId() ) * $Upgrade->GetLevel();
			if( $Value === 0 ) 
			{
				continue;
			}
			switch( Upgrade::GetType( $Upgrade->GetUpgradeId() ) ) 
			{
				case Enums\EUpgradeType::HitPoints:
					$Data[ 'hp_multiplier' ] += $Value;
					break;
				case Enums\EUpgradeType::DPS:
					$Data[ 'damage_multiplier_dps' ] += $Value;
					break;
				case Enums\EUpgradeType::ClickDamage:
					$Data[ 'damage_per_click_multiplier' ] += $Value;
					break;
				case Enums\EUpgradeType::DamageMultiplier_Fire:
					$Data[ 'damage_multiplier_fire' ] += $Value;
					break;
				case Enums\EUpgradeType::DamageMultiplier_Water:
					$Data[ 'damage_multiplier_water' ] += $Value;
					break;
				case Enums\EUpgradeType::DamageMultiplier_Air:
					$Data[ 'damage_multiplier_air' ] += $Value;
					break;
				case Enums\EUpgradeType::DamageMultiplier_Earth:
					$Data[ 'damage_multiplier_earth' ] += $Value;
					break;
				case Enums\EUpgradeType::DamageMultiplier_Crit:
					$Data[ 'damage_multiplier_crit' ] += $Value;
					break;
				case Enums\EUpgradeType::PurchaseAbility:
					# TODO: ?
					break;
				case Enums\EUpgradeType::BossLootDropPercentage:
					$Data[ 'boss_loot_drop_percentage' ] += $Value; // TODO: Not percentage but multiplier?
					break;
			}
		}
		$this->DamageMultiplierFire = $Data['damage_multiplier_fire'];
		$this->DamageMultiplierWater = $Data['damage_multiplier_water'];
		$this->DamageMultiplierAir = $Data['damage_multiplier_air'];
		$this->DamageMultiplierEarth = $Data['damage_multiplier_earth'];
		$this->DamageMultiplierCrit = $Data['damage_multiplier_crit'];
		$this->HpMultiplier = $Data['hp_multiplier'];
		$this->CritPercentage = $Data['crit_percentage'];
		$this->BossLootDropPercentage = $Data['boss_loot_drop_percentage'];
		$this->DamageMultiplierDps = $Data['damage_multiplier_dps'];
		$this->DamagePerClickMultiplier = $Data['damage_per_click_multiplier'];
		$this->DamagePerClick = $Data['damage_per_click'] * $this->GetDamagePerClickMultiplier();
		$this->Dps = $this->GetBaseDps() * $this->GetDamageMultiplierDps();
		$this->MaxHp = $this->GetTuningData( 'hp' ) * $this->GetHpMultiplier();
	}

	private function GetTuningData( $Key = null )
	{
		$TuningData = Server::GetTuningData( 'player' );
		if( $Key === null ) 
		{
			return $TuningData;
		} 
		else if( !array_key_exists( $Key, $TuningData ) ) 
		{
			return null;
		}
		return $TuningData[ $Key ];
	}
}
