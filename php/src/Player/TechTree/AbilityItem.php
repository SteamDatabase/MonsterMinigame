<?php
namespace SteamDB\CTowerAttack\Player\TechTree;

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
}
