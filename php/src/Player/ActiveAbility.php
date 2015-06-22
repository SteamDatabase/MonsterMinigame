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

	public function GetType()
	{
		return AbilityItem::GetAbilityTuningData( $this->Ability, 'type' );
	}

	public function GetName()
	{
		return AbilityItem::GetAbilityTuningData( $this->Ability, 'name' );
	}

	public function GetMaxNumClicks()
	{
		return AbilityItem::GetAbilityTuningData( $this->Ability, 'max_num_clicks' );
	}

	public function GetMultiplier()
	{
		return AbilityItem::GetAbilityTuningData( $this->Ability, 'multiplier' );
	}

	public function GetCost()
	{
		return AbilityItem::GetAbilityTuningData( $this->Ability, 'cost' );
	}

	public function GetDuration()
	{
		return AbilityItem::GetAbilityTuningData( $this->Ability, 'duration' );
	}

	public function GetCooldown()
	{
		return AbilityItem::GetAbilityTuningData( $this->Ability, 'cooldown' );
	}

	public function GetDescription()
	{
		return AbilityItem::GetAbilityTuningData( $this->Ability, 'desc' );
	}

	public function IsInstant()
	{
		return AbilityItem::GetAbilityTuningData( $this->Ability, 'instant' ) === 1;
	}

	public function GetBadgePointCost()
	{
		return AbilityItem::GetAbilityTuningData( $this->Ability, 'badge_points_cost' );
	}


}
