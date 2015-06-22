<?php
namespace SteamDB\CTowerAttack\Player;

use SteamDB\CTowerAttack\Enums;
use SteamDB\CTowerAttack\Player\TechTree\AbilityItem;

class ActiveAbility
{
	/*
	optional uint32 ability = 1;
	optional uint32 timestamp_done = 2;
	optional uint32 timestamp_cooldown = 3;
	*/
	private $Ability;
	private $TimestampDone;
	private $TimestampCooldown;

	public function __construct( $Ability )
	{
		$this->Ability = $Ability;
		$this->TimestampDone = time() + $this->GetDuration();
		$this->TimestampCooldown = time() + $this->GetCooldown();
	}

	public function ToArray()
	{
		return [
			'ability' => $this->Ability,
			'timestamp_done' => $this->TimestampDone,
			'timestamp_cooldown' => $this->TimestampCooldown
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
