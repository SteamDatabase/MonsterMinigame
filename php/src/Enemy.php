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
	public $DamageTaken = 0;

	public function __construct( $Id, $Type, $Level )
	{
		$this->Id = $Id;
		$this->Type = $Type;
		// TODO: Check if health works
		// TODO: Tower and MiniBoss respawns, see GetRespawnTime()
		// TODO: TreasureMob has Lifetime and Chance, needs to be remove after x time?
		if( $this->GetType() === Enums\EEnemyType::Mob ) 
		{
			$Variance = $this->GetHpMultiplierVariance();
			$LowestHp = Util::PredictValue( $Level, $this->GetTuningHp() * ( $this->GetHpMultiplier() - $Variance), $this->GetHpExponent() );
			$HighestHp = Util::PredictValue( $Level, $this->GetTuningHp() * ( $this->GetHpMultiplier() + $Variance), $this->GetHpExponent() );
			$this->MaxHp = rand( $LowestHp, $HighestHp );
		} 
		else 
		{
			$this->MaxHp = Util::PredictValue( $Level, $this->GetTuningHp() * $this->GetHpMultiplier(), $this->GetHpExponent() );
		}
		$this->Hp = $this->MaxHp;
		$this->Dps = floor( Util::PredictValue( $Level, $this->GetTuningDps() * $this->GetDpsMultiplier(), $this->GetDpsExponent() ));
		$this->Timer = null; // TODO: deal with this
		$this->Gold = Util::PredictValue( $Level, $this->GetTuninGold() * $this->GetGoldMultiplier(), $this->GetGoldExponent(), true );
		l( "Created new enemy [Id=$this->Id, Type=$this->Type, Hp=$this->Hp, MaxHp=$this->MaxHp, Dps=$this->Dps, Timer=$this->Timer, Gold=$this->Gold]" );
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
		if( $this->GetTimer() !== null ) 
		{
			$ReturnArray[ 'timer' ] = $this->GetTimer();
		}
		return $ReturnArray;
	}

	public function IsDead()
	{
		return $this->GetHp() <= 0;
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
		switch( $this->Type ) 
		{
			case Enums\EEnemyType::Tower:
				return 'Tower';
			case Enums\EEnemyType::Mob:
				return 'Mob';
			case Enums\EEnemyType::Boss:
				return 'Boss';
			case Enums\EEnemyType::MiniBoss:
				return 'MiniBoss';
			case Enums\EEnemyType::TreasureMob:
				return 'Treasure_Mob';
			case Enums\EEnemyType::Max:
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

	public function GetHpDifference()
	{
		return $this->DamageTaken - $this->Hp;
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

	public function GetRespawnTime()
	{
		return $this->GetRespawnTime( 'respawn_time' );
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

	public function GetLifetime()
	{
		return $this->GetTuningData( 'lifetime' );
	}

	public function getChance()
	{
		return $this->GetTuningData( 'chance' );
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
		$TypeName = strtolower( $this->GetTypeName() );
		$TuningData = Server::GetTuningData( $TypeName );
		if( $Key === null ) 
		{
			return $TuningData;
		} 
		else if( !array_key_exists( $Key, $TuningData ) ) 
		{
			return null;
		}
		return $TuningData[ $Key ];
	}
}
?>
