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

	private function GetAbilityTuningData( $Key = null )
	{
		return self::GetTuningData( $this->Ability, $Key );
	}

	public static function GetName( $Ability )
	{
		return self::GetTuningData( $Ability, 'name' );
	}

	public static function GetType( $Ability )
	{
		return self::GetTuningData( $Ability, 'type' );
	}

	public static function GetMaxNumClicks( $Ability )
	{
		return self::GetTuningData( $Ability, 'max_num_clicks' );
	}

	public static function GetMultiplier( $Ability )
	{
		return self::GetTuningData( $Ability, 'multiplier' );
	}

	public static function IsAbilityInstant( $Ability )
	{
		return self::GetTuningData( $Ability, 'instant' ) === 1;
	}

	public static function GetBadgePointCost( $Ability )
	{
		return self::GetTuningData( $Ability, 'badge_points_cost' );
	}

	public static function GetDuration( $Ability )
	{
		return self::GetTuningData( $Ability, 'duration' );
	}

	public static function GetCooldown( $Ability )
	{
		return self::GetTuningData( $Ability, 'cooldown' );
	}

	public static function GetDescription( $Ability )
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
		else if( !isset( $TuningData[ $Ability ][ $Key ] ) ) 
		{
			return null;
		}
		return $TuningData[ $Ability ][ $Key ];
	}
}
