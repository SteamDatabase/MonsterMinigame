<?php
namespace SteamDB\CTowerAttack\Player\TechTree;

use SteamDB\CTowerAttack\Server;

class AbilityItem
{
	/*
		optional ETowerAttackAbility ability = 1;
		optional uint32 quantity = 2;
	*/

	private $Ability;
	private $Quantity;

	public function __construct( $Ability, $Quantity = 1 )
	{
		$this->Ability = $Ability;
		$this->Quantity = $Quantity;
	}

	public function ToArray()
	{
		return [
			'ability' => $this->Ability,
			'quantity' => $this->Quantity
		];
	}

	public function GetAbility()
	{
		return $this->Ability;
	}

	public function GetQuantity()
	{
		return $this->Quantity;
	}


	public function GetName()
	{
		return $this->GetAbilityTuningData( 'name' );
	}

	public function GetType()
	{
		return $this->GetAbilityTuningData( 'type' );
	}

	public function GetMultiplier()
	{
		return $this->GetAbilityTuningData( 'multiplier' );
	}

	public function GetBadgePointsCost()
	{
		return $this->GetAbilityTuningData( 'badge_points_cost' );
	}

	public function IsInstant()
	{
		return $this->GetAbilityTuningData( 'instant' ) === 1;
	}

	public function GetDuration()
	{
		return $this->GetAbilityTuningData( 'duration' );
	}

	public function GetCooldown()
	{
		return $this->GetAbilityTuningData( 'cooldown' );
	}

	public function GetDescription()
	{
		return $this->GetAbilityTuningData( 'desc' );
	}

	private function GetAbilityTuningData( $Key = null )
	{
		return self::GetTuningData( $this->Ability, $Key );
	}

	public static function GetNameOfAbility( $Ability )
	{
		return self::GetTuningData( $Ability, 'name' );
	}

	public static function GetTypeOfAbility( $Ability )
	{
		return self::GetTuningData( $Ability, 'type' );
	}

	public static function GetMultiplierOfAbility( $Ability )
	{
		return self::GetTuningData( $Ability, 'multiplier' );
	}

	public static function IsAbilityInstant( $Ability )
	{
		return self::GetTuningData( $Ability, 'instant' ) === 1;
	}

	public static function GetBadgePointCostOfAbility( $Ability )
	{
		return self::GetTuningData( $Ability, 'badge_points_cost' );
	}

	public static function GetDurationOfAbility( $Ability )
	{
		return self::GetTuningData( $Ability, 'duration' );
	}

	public static function GetCooldownOfAbility( $Ability )
	{
		return self::GetTuningData( $Ability, 'cooldown' );
	}

	public static function GetDescriptionOfAbility( $Ability )
	{
		return self::GetTuningData( $Ability, 'desc' );
	}

	public static function GetTuningData( $Ability, $Key = null )
	{
		$TuningData = Server::GetTuningData( 'abilities' );
		if( $Key === null ) 
		{
			return $TuningData[ $Ability ];
		} 
		else if( !array_key_exists( $Key, $TuningData[ $Ability ] ) ) 
		{
			return null;
		}
		return $TuningData[ $Ability ][ $Key ];
	}
}
