<?php
namespace SteamDB\CTowerAttack;

class Enemy
{
	/*
	optional uint64 id = 1;
	optional ETowerAttackEnemyType type = 2;
	optional double hp = 3;
	optional double max_hp = 4;
	optional double dps = 5;
	optional double timer = 6;
	optional double gold = 7;
	*/
	private $Id;
	private $Type;
	private $Hp;
	private $MaxHp;
	private $Dps;
	private $Timer;
	private $Gold;
	
	public function __construct( $Id, $Type, $Hp, $MaxHp, $Dps, $Timer, $Gold )
	{
		$this->Id = $Id;
		$this->Type = $Type;
		$this->MaxHp = $MaxHp;
		$this->Dps = $Dps;
		$this->Timer = $Timer;
		$this->Gold = $Gold;
		l( "Created new enemy [Id=$Id, Type=$Type, Hp=$Hp, MaxHp=$MaxHp, Dps=$Dps, Timer=$Timer, Gold=$Gold]" );
	}

	public function ToArray()
	{
		$ReturnArray = array(
			'id' => $this->GetId(),
			'type' => $this->GetType(),
			'hp' => $this->GetHp(),
			'max_hp' => $this->GetMaxHp(),
			'dps' => $this->GetDps(),
			'gold' => $this->GetGold()
		);
		if ($this->GetTimer() !== null) {
			$ReturnArray['timer'] = $this->GetTimer();
		}
		return $ReturnArray;
	}

	public function GetId()
	{
		return $this->Id;
	}

	public function GetType()
	{
		return $this->Type;
	}

	public function GetHp()
	{
		return $this->Type;
	}

	public function GetMaxHp()
	{
		return $this->MaxHp;
	}

	public function GetDps()
	{
		return $this->Dps;
	}

	public function GetTimer()
	{
		return $this->Timer;
	}

	public function GetGold()
	{
		return $this->Gold;
	}
}
?>