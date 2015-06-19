<?php
namespace SteamDB\CTowerAttack;

class ActiveAbility
{
	/*
	optional uint32 accountid_caster = 1;
	optional uint32 ability = 2;
	optional uint32 timestamp_done = 3;
	optional double multiplier = 4;
	*/
	private $AccountIdCaster;
	private $Ability;
	private $TimestampDone;
	private $Multiplier;

	public function __construct( $AccountIdCaster, $Ability, $TimestampDone, $Multiplier )
	{
		$this->AccountIdCaster = $AccountIdCaster;
		$this->Ability = $Ability;
		$this->TimestampDone = $TimestampDone;
		$this->Multiplier = $Multiplier;
	}

	public function GetAccountIdCaster()
	{
		return $this->AccountIdCaster;
	}

	public function GetAbility()
	{
		return $this->Ability;
	}

	public function GetTimestampDone()
	{
		return $this->TimestampDone;
	}

	public function GetMultiplier()
	{
		return $this->Multiplier;
	}
}
?>