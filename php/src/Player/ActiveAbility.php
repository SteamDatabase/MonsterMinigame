<?php
namespace SteamDB\CTowerAttack\Player;

use SteamDB\CTowerAttack\Enums;
use SteamDB\CTowerAttack\Player\TechTree\AbilityItem;

class ActiveAbility
{
	private $Actor;
	private $Ability;
	private $Time;
	private $TimestampDone;
	private $TimestampCooldown;

	public function __construct( $Ability, $Actor, $DecreaseCooldown = false )
	{
		$this->Actor = $Actor;
		$this->Ability = $Ability;
		$this->Time = time();
		$Duration = AbilityItem::GetDuration( $Ability );
		$Cooldown = AbilityItem::GetCooldown( $Ability );
		$this->TimestampDone = $this->Time + AbilityItem::GetDuration( $Ability );
		if( $DecreaseCooldown ) 
		{
			$Cooldown *= AbilityItem::GetMultiplier( Enums\EAbility::Support_DecreaseCooldowns );
		}
		$this->TimestampCooldown = $this->Time + $Cooldown;
	}

	public function ToArray()
	{
		return [
			'actor' => $this->Actor,
			'ability' => $this->Ability,
			'time' => $this->Time
		];
	}

	public function GetAbility()
	{
		return $this->Ability;
	}

	public function GetTimestampDone()
	{
		return $this->TimestampDone;
	}

	public function IsDone()
	{
		// Wormhole is never done until the level ends
		if( $this->Ability === Enums\EAbility::Item_SkipLevels )
		{
			return false;
		}

		return $this->TimestampDone <= time();
	}

	public function GetTimestampCooldown()
	{
		return $this->TimestampCooldown;
	}

	public function IsCooledDown()
	{
		return $this->TimestampCooldown <= time();
	}
}
