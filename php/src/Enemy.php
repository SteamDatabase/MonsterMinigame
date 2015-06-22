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
	private $TimerDisabled = false;
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

		// Deal with respawn/alive timer
		$this->ResetTimer();
		$this->Hp = $this->MaxHp;
		$this->Dps = floor( Util::PredictValue( $Level, $this->GetTuningDps() * $this->GetDpsMultiplier(), $this->GetDpsExponent() ));
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
		if( $this->HasTimer() ) 
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
		return self::GetEnemyTypeName( $this->GetType() );
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

	public function ResetHp()
	{
		$this->Hp = $this->GetMaxHp();
	}

	public function GetHpDifference()
	{
		return $this->Hp > 0 ? $this->DamageTaken - $this->Hp : $this->Hp * -1;
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

	public function SetTimer( $Seconds )
	{
		$this->Timer = 0;
	}

	public function ResetTimer()
	{
		switch( $this->Type ) 
		{
			case Enums\EEnemyType::Tower:
			case Enums\EEnemyType::MiniBoss:
				$this->Timer = $this->GetRespawnTime();
				break;
			case Enums\EEnemyType::TreasureMob:
				$this->Timer = $this->GetLifetime();
				break;
		}
	}

	public function HasTimer()
	{
		return $this->Timer !== null;
	}

	public function IsTimerEnabled()
	{
		return $this->TimerDisabled === false;
	}

	public function IsTimerDisabled()
	{
		return $this->TimerDisabled === true;
	}

	public function HasTimerRanOut( $Seconds = 0 )
	{
		if( $this->TimerDisabled )
		{
			return false;
		}
		$Seconds = $Seconds === false ? 0 : $Seconds;
		$this->DecreaseTimer( $Seconds );
		return $this->Timer <= 0;
	}

	public function DecreaseTimer( $Seconds )
	{
		$this->Timer -= $Seconds;
	}

	public function DisableTimer()
	{
		$this->TimerDisabled = true;
	}

	public function GetGold()
	{
		return $this->Gold;
	}

	public function SetGold( $Amount )
	{
		$this->Gold = $Amount;
	}

	public function GetRespawnTime()
	{
		return $this->GetEnemyTuningData( 'respawn_time' );
	}

	public function GetTuningHp()
	{
		return $this->GetEnemyTuningData( 'hp' );
	}

	public function GetTuningDps()
	{
		return $this->GetEnemyTuningData( 'dps' );
	}

	public function GetTuninGold()
	{
		return $this->GetEnemyTuningData( 'gold' );
	}

	public function GetHpMultiplier()
	{
		return $this->GetEnemyTuningData( 'hp_multiplier' );
	}

	public function GetHpMultiplierVariance()
	{
		return $this->GetEnemyTuningData( 'hp_multiplier_variance' );
	}

	public function GetHpExponent()
	{
		return $this->GetEnemyTuningData( 'hp_exponent' );
	}

	public function GetDpsMultiplier()
	{
		return $this->GetEnemyTuningData( 'dps_multiplier' );
	}

	public function GetDpsExponent()
	{
		return $this->GetEnemyTuningData( 'dps_exponent' );
	}

	public function GetLifetime()
	{
		return $this->GetEnemyTuningData( 'lifetime' );
	}

	public function getChance()
	{
		return $this->GetEnemyTuningData( 'chance' );
	}

	public function GetGoldMultiplier()
	{
		return $this->GetEnemyTuningData( 'gold_multiplier' );
	}

	public function GetGoldExponent()
	{
		return $this->GetEnemyTuningData( 'gold_exponent' );
	}

	private function GetEnemyTuningData( $Key = null )
	{
		return self::GetTuningData( $this->GetTypeName(), $Key );
	}
	
	public static function SpawnTreasureMob()
	{
		$SpawnChance = self::GetTuningData( self::GetEnemyTypeName( Enums\EEnemyType::TreasureMob ), 'chance' );
		$RandPercent = rand( 1, 100 );
		return $RandPercent < $SpawnChance;
	}

	public static function GetTuningData( $TypeName = null, $Key = null )
	{
		if( $TypeName !== null)
		{
			$TypeName = strtolower( $TypeName );
		}
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

	public static function GetEnemyTypeName( $Type )
	{
		switch( $Type ) 
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

}
