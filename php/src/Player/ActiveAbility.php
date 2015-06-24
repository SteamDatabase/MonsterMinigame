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

	public function __construct( $Ability, $Actor )
	{
		$this->Actor = $Actor;
		$this->Ability = $Ability;
		$this->Time = time();
		$this->TimestampDone = $this->Time + AbilityItem::GetDuration( $Ability );
		$this->TimestampCooldown = $this->Time + AbilityItem::GetCooldown( $Ability );
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
