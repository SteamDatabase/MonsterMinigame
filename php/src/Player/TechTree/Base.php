<?php
namespace SteamDB\CTowerAttack\Player\TechTree;

class Base
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
	private $BadgePoints = 0;
	private $AbilityItems = array();
	private $BossLootDropPercentage = 0.25;
	private $DamageMultiplierDps = 1.0;
	private $BaseDps = 0;
	private $DamagePerClickMultiplier = 1.0;
	private $MaxHp = 0;
	private $Dps = 0;

	public function __construct() {
		$this->Upgrades = array();
		foreach( \SteamDB\CTowerAttack\Server::GetTuningData( 'upgrades' ) as $UpgradeId => $Upgrade) {
			$this->Upgrades[] = new Upgrade( $UpgradeId, 0, $Upgrade[ 'cost' ] );
		}
		$this->DamagePerClick = $this->GetTuningData( 'damage_per_click' );
		$this->DamageMultiplierFire = $this->GetTuningData( 'damage_multiplier_fire' );
		$this->DamageMultiplierWater = $this->GetTuningData( 'damage_multiplier_water' );
		$this->DamageMultiplierAir = $this->GetTuningData( 'damage_multiplier_air' );
		$this->DamageMultiplierEarth = $this->GetTuningData( 'damage_multiplier_earth' );
		$this->DamageMultiplierCrit = $this->GetTuningData( 'damage_multiplier_crit' );
		$this->UnlockedAbilitiesBitfield = 0;
		$this->HpMultiplier = 1; # TODO
		$this->CritPercentage = $this->GetTuningData( 'crit_percentage' );
		// TODO: Give 0.1 badgepoints per previous level (start_condition_minigame_badge)
		// TODO: Give badgepoints for badge (1 & 10 points)
		$this->BadgePoints = 0;
		$this->AbilityItems = array();
		$this->BossLootDropPercentage = $this->GetTuningData( 'loot_chance' );
		$this->DamageMultiplierDps = 1; # TODO
		$this->BaseDps = $this->GetTuningData( 'Dps' );
		$this->DamagePerClickMultiplier = 1; # TODO
		$this->MaxHp = 0; # TODO: Delete?
		$this->Dps = $this->GetTuningData( 'Dps' );
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
			'ability_items' => $this->GetAbilityItems(),
			'boss_loot_drop_percentage' => (double) $this->GetBossLootDropPercentage(),
			'damage_multiplier_dps' => (double) $this->GetDamageMultiplierDps(),
			'damage_per_click_multiplier' => (double) $this->GetDamagePerClickMultiplier(),
			'max_hp' => (double) $this->GetMaxHp(),
			'dps' => (double) $this->GetDps()
		);
	}

	public function GetElementalUpgrades()
	{
		return array(
			3 => $this->GetUpgrade( 3 ), // Fire
			4 => $this->GetUpgrade( 4 ), // Water
			5 => $this->GetUpgrade( 5 ), // Air
			6 => $this->GetUpgrade( 6 ) // Earth
		);
	}

	public function GetUpgrade( $UpgradeId )
	{
		return $this->Upgrades[ $UpgradeId ];
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

	public function GetCritPercentage()
	{
		return $this->CritPercentage;
	}

	public function GetBadgePoints()
	{
		return $this->BadgePoints;
	}

	public function GetAbilityItems()
	{
		return $this->AbilityItems;
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

	public function GetDps()
	{
		return $this->Dps;
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
			'hp_multiplier' => (double) 1,
			'crit_percentage' => (double) $this->GetTuningData( 'crit_percentage' ),
			'boss_loot_drop_percentage' => (double) $this->GetTuningData( 'loot_chance' ),
			'damage_multiplier_dps' => (double) 1,
			'damage_per_click_multiplier' => (double) 1
		);

		foreach( $this->GetUpgrades() as $Upgrade ) 
		{
			$Value = $Upgrade->GetMultiplier() * $Upgrade->GetLevel();
			if( $Value === 0 ) 
			{
				continue;
			}
			switch( $Upgrade->GetType() ) 
			{
				case \ETowerAttackUpgradeType::HitPoints:
					$Data[ 'hp_multiplier' ] += $Value;
					break;
				case \ETowerAttackUpgradeType::DPS:
					$Data[ 'damage_multiplier_dps' ] += $Value; // TODO: initial_value?
					break;
				case \ETowerAttackUpgradeType::ClickDamage:
					var_dump($Upgrade);
					var_dump($Value);
					$Data[ 'damage_per_click_multiplier' ] += $Value;
					break;
				case \ETowerAttackUpgradeType::DamageMultiplier_Fire:
					$Data[ 'damage_multiplier_fire' ] += $Value;
					break;
				case \ETowerAttackUpgradeType::DamageMultiplier_Water:
					$Data[ 'damage_multiplier_water' ] += $Value;
					break;
				case \ETowerAttackUpgradeType::DamageMultiplier_Air:
					$Data[ 'damage_multiplier_air' ] += $Value;
					break;
				case \ETowerAttackUpgradeType::DamageMultiplier_Earth:
					$Data[ 'damage_multiplier_earth' ] += $Value;
					break;
				case \ETowerAttackUpgradeType::DamageMultiplier_Crit:
					$Data[ 'damage_multiplier_crit' ] += $Value;
					break;
				case \ETowerAttackUpgradeType::PurchaseAbility:
					# TODO: ?
					break;
				case \ETowerAttackUpgradeType::BossLootDropPercentage:
					$Data[ 'boss_loot_drop_percentage' ] += $Value; // TODO: Not percentage but multiplier?
					break;
			}
		}
		#$this->DamagePerClick = $Data['damage_per_click'];
		$this->DamageMultiplierFire = $Data['damage_multiplier_fire'];
		$this->DamageMultiplierWater = $Data['damage_multiplier_water'];
		$this->DamageMultiplierAir = $Data['damage_multiplier_air'];
		$this->DamageMultiplierEarth = $Data['damage_multiplier_earth'];
		$this->DamageMultiplierCrit = $Data['damage_multiplier_crit'];
		$this->HpMultiplier = $Data['hp_multiplier'];
		#$this->CritPercentage = $Data['crit_percentage'];
		$this->BossLootDropPercentage = $Data['boss_loot_drop_percentage'];
		$this->DamageMultiplierDps = $Data['damage_multiplier_dps'];
		$this->DamagePerClickMultiplier = $Data['damage_per_click_multiplier'];
		$this->DamagePerClick = 10 * $this->GetDamagePerClickMultiplier();
	}

	private function GetTuningData( $Key = null )
	{
		$TuningData = \SteamDB\CTowerAttack\Server::GetTuningData( 'player' );
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
?>
