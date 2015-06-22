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

	public function __construct( $Ability, $Quantity )
	{
		$this->Ability = $Ability;
		$this->Quantity = $Quantity;
	}

	public function GetAbility()
	{
		return $this->Ability;
	}

	public function GetQuantity()
	{
		return $this->Quantity;
	}

	public function ToArray()
	{
		return [
			'ability' => $this->Ability,
			'quantity' => $this->Quantity
		];
	}

	private function GetAbilityTuningData( $Key = null )
	{
		return self::GetTuningData( $this->Ability, $Key );
	}

	public static function GetTypeOfAbility( $Ability )
	{
		return self::GetTuningData( $Ability, 'type' );
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
