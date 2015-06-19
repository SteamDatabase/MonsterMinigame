<?php
namespace SteamDB\CTowerAttack\Player;

class Base
{
	/*
	optional double hp = 1;
	optional uint32 current_lane = 2;
	optional uint32 target = 3;
	optional uint32 time_died = 4;
	optional double gold = 5;
	optional uint64 active_abilities_bitfield = 6;
	repeated ActiveAbility active_abilities = 7;
	optional double crit_damage = 8;
	repeated Loot loot = 9;
	*/

	private $AccountId;
	private $Hp;
	private $CurrentLane;
	private $Target;
	private $TimeDied;
	private $Gold;
	private $ActiveAbilitiesBitfield;
	private $ActiveAbilities = array();
	private $CritDamage;
	private $Loot;

	public function __construct(
		$AccountId,
		$Hp,
		$CurrentLane,
		$Target,
		$TimeDied,
		$Gold,
		$ActiveAbilitiesBitfield,
		$ActiveAbilities,
		$CritDamage,
		$Loot,
		TechTree\Base $TechTree
	)
	{
		$this->AccountId = $AccountId;
		$this->Hp = $Hp;
		$this->CurrentLane = $CurrentLane;
		$this->Target = $Target;
		$this->TimeDied = $TimeDied;
		$this->Gold = $Gold;
		$this->ActiveAbilitiesBitfield = $ActiveAbilitiesBitfield;
		$this->ActiveAbilities = $ActiveAbilities;
		$this->CritDamage = $CritDamage;
		$this->Loot = $Loot;
		$this->TechTree = $TechTree;
	}

	public function ToArray()
	{
		return array(
			'hp' => $this->GetHp(),
			'current_lane' => $this->GetCurrentLane(),
			'target' => $this->GetTarget(),
			'time_died' => $this->GetTimeDied(),
			'gold' => $this->GetGold(),
			'active_abilities_bitfield' => $this->GetActiveAbilitiesBitfield(),
			'crit_damage' => $this->GetCritDamage()
		);
	}

	public function HandleAbilityUsage( $RequestedAbilities )
	{
		foreach( $RequestedAbilities as $RequestedAbility ) {
			switch( $RequestedAbility['ability'] ) {
				case \ETowerAttackAbility::Attack:
					break;
				case \ETowerAttackAbility::ChangeLane:
					$this->SetLane( $RequestedAbility[ 'new_lane' ] );
					break;
				case \ETowerAttackAbility::Respawn:
					break;
				case \ETowerAttackAbility::ChangeTarget:
					break;
				default:
					// Handle unknown ability?
					break;
			}
		}
	}

	public function HandleUpgrade( $Upgrades )
	{
		//
	}

	public function GetTechTree()
	{
		return $this->TechTree;
	}

	public function GetAccountId()
	{
		return $this->AccountId;
	}

	public function GetHp()
	{
		return $this->Hp;
	}

	public function GetCurrentLane()
	{
		return $this->CurrentLane;
	}

	public function SetLane( $Lane )
	{
		return $this->CurrentLane = $Lane;
	}

	public function GetTarget()
	{
		return $this->Target;
	}

	public function GetTimeDied()
	{
		return $this->TimeDied;
	}

	public function GetGold()
	{
		return $this->Gold;
	}

	public function GetActiveAbilitiesBitfield()
	{
		return $this->ActiveAbilitiesBitfield;
	}

	public function GetActiveAbilities()
	{
		return $this->ActiveAbilities;
	}

	public function GetCritDamage()
	{
		return $this->CritDamage;
	}

	public function GetLoot()
	{
		return $this->Loot;
	}
}
?>