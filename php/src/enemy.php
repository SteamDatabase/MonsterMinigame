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
		$this->Hp = $Hp;
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
			'id' => (int) $this->GetId(),
			'type' => (int) $this->GetType(),
			'hp' => (double) $this->GetHp(),
			'max_hp' => (double) $this->GetMaxHp(),
			'dps' => (double) $this->GetDps(),
			'gold' => (double) $this->GetGold()
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

	public function GetTypeName()
	{
		switch( $this->Type ) {
			case \ETowerAttackEnemyType::Tower:
				return 'Tower';
			case \ETowerAttackEnemyType::Mob:
				return 'Mob';
			case \ETowerAttackEnemyType::Boss:
				return 'Boss';
			case \ETowerAttackEnemyType::MiniBoss:
				return 'MiniBoss';
			case \ETowerAttackEnemyType::TreasureMob:
				return 'Treasure_Mob';
			case \ETowerAttackEnemyType::Max:
				return 'Max';
		}
	}

	public function GetHp()
	{
		return $this->Hp;
	}

	public function DecreaseHp( $Hp )
	{
		$this->Hp -= $Hp;
	}

	public function IncreaseHp( $Hp )
	{
		$this->Hp += $Hp;
	}

	public function SetHp( $Hp )
	{
		$this->Hp = $Hp;
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

	public function GetTuningHp()
	{
		return $this->GetTuningData( 'hp' );
	}

	public function GetTuningDps()
	{
		return $this->GetTuningData( 'dps' );
	}

	public function GetTuninGold()
	{
		return $this->GetTuningData( 'gold' );
	}

	public function GetHpMultiplier()
	{
		return $this->GetTuningData( 'hp_multiplier' );
	}

	public function GetHpMultiplierVariance()
	{
		return $this->GetTuningData( 'hp_multiplier_variance' );
	}

	public function GetHpExponent()
	{
		return $this->GetTuningData( 'hp_exponent' );
	}

	public function GetDpsMultiplier()
	{
		return $this->GetTuningData( 'dps_multiplier' );
	}

	public function GetDpsExponent()
	{
		return $this->GetTuningData( 'dps_exponent' );
	}

	public function GetGoldMultiplier()
	{
		return $this->GetTuningData( 'gold_multiplier' );
	}

	public function GetGoldExponent()
	{
		return $this->GetTuningData( 'gold_exponent' );
	}

	private function GetTuningData( $Key = null )
	{	
		$Upgrades = \SteamDB\CTowerAttack\Server::GetTuningData( strtolower( $this->GetTypeName() ) );
		if ($Key === null) {
			return $Upgrades[ $this->GetUpgradeId() ];
		} else if (!array_key_exists( $Key, $Upgrades[ $this->GetUpgradeId() ] )) {
			return null;
		}
		return $Upgrades[ $this->GetUpgradeId() ][ $Key ];
	}
}
?>