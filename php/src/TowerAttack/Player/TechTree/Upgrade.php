<?php
namespace SteamDB\CTowerAttack\Player\TechTree;

use SteamDB\CTowerAttack\Server;
use SteamDB\CTowerAttack\Util;

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

	public static function IsLevelOneUpgrade( $UpgradeId )
	{
		return self::GetAbility( $UpgradeId ) !== null;
	}

	public static function IsElementalUpgrade( $UpgradeId )
	{
		return self::GetElement( $UpgradeId ) !== null;
	}

	public static function GetElement( $UpgradeId )
	{
		return self::GetTuningData( $UpgradeId, 'element' );
	}

	public static function GetName( $UpgradeId )
	{
		return self::GetTuningData( $UpgradeId, 'name' );
	}

	public static  function GetInitialValue( $UpgradeId )
	{
		return self::GetTuningData( $UpgradeId, 'initial_value' );
	}

	public static  function GetMultiplier( $UpgradeId )
	{
		return self::GetTuningData( $UpgradeId, 'multiplier' );
	}

	public static function GetType( $UpgradeId )
	{
		return self::GetTuningData( $UpgradeId, 'type' );
	}

	public static function GetCost( $UpgradeId )
	{
		return self::GetTuningData( $UpgradeId, 'cost' );
	}

	public static function GetCostExponentialBase( $UpgradeId )
	{
		return self::GetTuningData( $UpgradeId, 'cost_exponential_base' );
	}

	public static function HasRequiredUpgrade( $UpgradeId )
	{
		return self::GetTuningData( $UpgradeId, 'required_upgrade' ) !== null;
	}

	public static function GetRequiredUpgrade( $UpgradeId )
	{
		return self::GetTuningData( $UpgradeId, 'required_upgrade' );
	}

	public static function GetRequiredLevel( $UpgradeId )
	{
		return self::GetTuningData( $UpgradeId, 'required_level' );
	}

	public static function GetAbility( $UpgradeId )
	{
		return self::GetTuningData( $UpgradeId, 'ability' );
	}

	public static function GetDescription( $UpgradeId )
	{
		return self::GetTuningData( $UpgradeId, 'desc' );
	}

	public static function GetTuningData( $UpgradeId, $Key = null )
	{
		$TuningData = Server::GetTuningData( 'upgrades' );
		if( $UpgradeId === null )
		{
			return $TuningData;
		}
		else if( $Key === null )
		{
			return $TuningData[ $UpgradeId ];
		}
		else if( !isset( $TuningData[ $UpgradeId ][ $Key ] ) )
		{
			return null;
		}
		return $TuningData[ $UpgradeId ][ $Key ];
	}


	private function GetUpgradeTuningData( $Key = null )
	{
		return self::GetTuningData( $this->UpgradeId, $Key );
	}

	private function GetPredictedCost($Level = null)
	{
		return Util::PredictValue(
			$Level !== null ? $Level : $this->GetLevel(),
			self::GetCost( $this->GetUpgradeId() ),
			self::GetCostExponentialBase( $this->GetUpgradeId() ),
			true
		);
	}
}
