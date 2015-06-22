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

	public static function GetName( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'name' );
	}

	public static function GetType( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'type' );
	}

	public static function GetMaxNumClicks( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'max_num_clicks' );
	}

	public static function GetMultiplier( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'multiplier' );
	}

	public static function IsAbilityInstant( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'instant' ) === 1;
	}

	public static function GetBadgePointCost( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'badge_points_cost' );
	}

	public static function GetDuration( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'duration' );
	}

	public static function GetCooldown( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'cooldown' );
	}

	public static function GetDescription( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'desc' );
	}

	public static function GetTuningData( $AbilityId = null, $Key = null )
	{
		$TuningData = Server::GetTuningData( 'abilities' );
		if( $AbilityId === null ) 
		{
			return $TuningData;
		} 
		else if( $Key === null ) 
		{
			return $TuningData[ $AbilityId ];
		} 
		else if( !isset( $TuningData[ $AbilityId ][ $Key ] ) ) 
		{
			return null;
		}
		return $TuningData[ $AbilityId ][ $Key ];
	}
}
