<?php
namespace SteamDB\CTowerAttack\Player\TechTree;

class AbilityItem
{
	/*
	optional uint32 upgrade = 1;
	optional uint32 level = 2;
	optional double cost_for_next_level = 3;
	*/
	private $Upgrade;
	private $Level;
	private $CostForNextLevel;

	public function __construct( $Upgrade, $Level, $CostForNextLevel )
	{
		$this->Upgrade = $Upgrade;
		$this->Level = $Level;
		$this->CostForNextLevel = $CostForNextLevel;
	}

	public function GetUpgrade()
	{
		return $this->Upgrade;
	}

	public function GetLevel()
	{
		return $this->Level;
	}

	public function GetCostForNextLevel()
	{
		return $this->CostForNextLevel;
	}
}
?>