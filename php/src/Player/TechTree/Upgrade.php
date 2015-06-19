<?php
namespace SteamDB\CTowerAttack\Player\TechTree;

class Upgrade
{
	/*
	optional uint32 upgrade = 1;
	optional uint32 level = 2;
	optional double cost_for_next_level = 3;
	*/
	private $UpgradeId;
	private $Level;
	private $CostForNextLevel;

	public function __construct( $UpgradeId, $Level, $CostForNextLevel )
	{
		$this->UpgradeId = $UpgradeId;
		$this->Level = $Level;
		$this->CostForNextLevel = $CostForNextLevel;
	}

	public function ToArray()
	{
		return array(
			'upgrade' => (int) $this->GetUpgradeId(),
			'level' => (int) $this->GetLevel(),
			'cost_for_next_level' => (double) $this->GetCostForNextLevel()
		);
	}

	public function GetUpgradeId()
	{
		return $this->UpgradeId;
	}

	public function GetLevel()
	{
		return $this->Level;
	}

	public function IncreaseLevel( $IncreaseLevel = 1, $NextLevel = null)
	{
		$this->Level += $IncreaseLevel;
		$this->CostForNextLevel = $this->GetPredictedCost( $NextLevel );
	}

	public function GetCostForNextLevel()
	{
		return $this->CostForNextLevel;
	}

	public function SetCostForNextLevel( $CostForNextLevel )
	{
		$this->CostForNextLevel = $CostForNextLevel;
	}

	public function SetPredictedCostForNextLevel( $NextLevel )
	{
		$this->SetCostForNextLevel( $this->GetPredictedCost( $NextLevel ) );
	}

	public function GetName()
	{
		return $this->GetTuningData( 'name' );
	}

	public function GetMultiplier()
	{
		return $this->GetTuningData( 'multiplier' );
	}

	public function GetType()
	{
		return $this->GetTuningData( 'type' );
	}

	public function GetCost()
	{
		return $this->GetTuningData( 'cost' );
	}

	public function GetCostExponentialBase()
	{
		return $this->GetTuningData( 'cost_exponential_base' );
	}

	public function HasRequiredUpgrade()
	{
		return $this->GetTuningData( 'required_upgrade' ) !== null;
	}

	public function GetRequiredUpgrade()
	{
		return $this->GetTuningData( 'required_upgrade' );
	}

	public function GetRequiredLevel()
	{
		return $this->GetTuningData( 'required_level' );
	}

	public function GetDescription()
	{
		return $this->GetTuningData( 'desc' );
	}

	private function GetTuningData( $Key = null )
	{	
		$Upgrades = \SteamDB\CTowerAttack\Server::GetTuningData( 'upgrades' );
		if ($Key === null) {
			return $Upgrades[ $this->GetUpgradeId() ];
		} else if (!array_key_exists( $Key, $Upgrades[ $this->GetUpgradeId() ] )) {
			return null;
		}
		return $Upgrades[ $this->GetUpgradeId() ][ $Key ];
	}

	private function GetPredictedCost($Level = null)
	{
		return self::FloorToMultipleOf(
			10, 
			self::CalcExponentialTuningValve(
				$Level !== null ? $Level : $this->GetLevel(), 
				$this->GetCost(), 
				$this->GetCostExponentialBase()
			) 
		);
		
	}

	private static function FloorToMultipleOf( $MultipleOf, $Number )
	{
		return floor( $Number / $MultipleOf ) * $MultipleOf;
	}

	private static function CalcExponentialTuningValve( $Level, $Coefficient, $Base )
	{
		return $Coefficient * pow( $Base, $Level );
	}
}
?>