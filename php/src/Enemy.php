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

	public function __construct( $Id, $Type, $Level, $Dps = null, $Gold = null, $Hp = null )
	{
		$this->Id = $Id;
		$this->Type = $Type;
		// TODO: TreasureMob has Lifetime and Chance, needs to be remove after x time?
		// TODO: Figure out if valve floored the value or just rounded
		$this->MaxHp = $Hp !== null ? $Hp : self::GetHpAtLevel( $Type, $Level );
		$this->ResetTimer();
		$this->Hp = $this->MaxHp;
		$this->Dps = $Dps !== null ? $Dps : self::GetDpsAtLevel( $Type, $Level );
		$this->Gold = $Gold !== null ? $Gold : self::GetGoldAtLevel( $Type, $Level );
		l( "Created new enemy [Id=$this->Id, Type=$this->Type, Hp=$this->Hp, MaxHp=$this->MaxHp, Dps=$this->Dps, Timer=$this->Timer, Gold=$this->Gold]" );
	}

	public static function GetHpAtLevel( $Type, $Level )
	{
		$TuningData = self::GetTuningData( self::GetEnemyTypeName( $Type ) );
		switch( $Type ) 
		{
			case Enums\EEnemyType::Mob:
			case Enums\EEnemyType::MiniBoss:
			case Enums\EEnemyType::TreasureMob:
				$MinHp = self::GetValueAtLevel( 'hp', $Type, $Level, false ); # TODO: floor it?
				if( $Type === Enums\EEnemyType::Mob )
				{
					# @Contex: This is really dirty, but it works...
					# TODO: move values to tuningData.json?
					$MaxHp = $MinHp * ( 2.83 + 0.85 );
					$MultiplierVarianceMin = 0.195;
					$MultiplierVarianceMax = 1;
					$MultiplierVariance = ( $MultiplierVarianceMin + ( lcg_value() * ( abs( $MultiplierVarianceMax - $MultiplierVarianceMin ) ) ) );
					return floor($MaxHp * $MultiplierVariance);
				}
				else
				{
					# @Contex: This is really dirty, but it works...
					# TODO: move values to tuningData.json?
					$MidHip = $MinHp * 1.84; # TODO: move to tuningData.json?
					$MaxHp = $MinHp * 2.83; # TODO: move to tuningData.json?
					$HpArray = [ $MinHp, $MidHip, $MaxHp ];
					return floor( $HpArray[ array_rand( $HpArray ) ] );
				}
			default:
				return self::GetValueAtLevel( 'hp', $Type, $Level );
		}
	}

	public static function GetDpsAtLevel( $Type, $Level )
	{
		$TuningData = self::GetTuningData( self::GetEnemyTypeName( $Type ) );
		$MaxDps = Util::PredictValue(
			$TuningData[ 'dps_exponent' ], 
			$TuningData[ 'dps' ], 
			$Level * $TuningData[ 'dps_multiplier' ]
		);
		$MidDps = Util::PredictValue(
			$TuningData[ 'dps_exponent' ] - 0.1, # TODO: Move 0.1 to tuningData?
			$TuningData[ 'dps' ], 
			$Level * $TuningData[ 'dps_multiplier' ]
		);
		$MinDps = Util::PredictValue(
			$TuningData[ 'dps_exponent' ] - ( 0.1 * 2 ), # TODO: Move 0.1 to tuningData?
			$TuningData[ 'dps' ], 
			$Level * $TuningData[ 'dps_multiplier' ]
		);
		$DpsArray = [ $MinDps, $MidDps, $MaxDps ];
		return floor( $DpsArray[ array_rand( $DpsArray ) ] );
	}

	public static function GetGoldAtLevel( $Type, $Level )
	{
		return self::GetValueAtLevel( 'gold', $Type, $Level );
	}

	private static function GetValueAtLevel( $Category, $Type, $Level, $FloorIt = true )
	{
		$TuningData = self::GetTuningData( self::GetEnemyTypeName( $Type ) );
		$Value = Util::PredictValue(
			$TuningData[ $Category . '_exponent' ], 
			$TuningData[ $Category ], 
			$Level * $TuningData[ $Category . '_multiplier' ]
		);
		return $FloorIt ? floor( $Value ) : $Value;
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
		if( $this->Hp < 0 )
		{
			$this->Hp = 0;
		}
	}

	public function IncreaseHp( $Hp )
	{
		$this->Hp += $Hp;
		if( $this->Hp > $this->GetMaxHp() )
		{
			$this->Hp = $this->GetMaxHp();
		}
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

	public function GetChance()
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
				return 'tower';
			case Enums\EEnemyType::Mob:
				return 'mob';
			case Enums\EEnemyType::Boss:
				return 'boss';
			case Enums\EEnemyType::MiniBoss:
				return 'miniboss';
			case Enums\EEnemyType::TreasureMob:
				return 'treasure_mob';
		}
	}

	public static function GetEnemyTypeId( $TypeName )
	{
		switch( $TypeName ) 
		{
			case 'tower':
				return Enums\EEnemyType::Tower;
			case 'mob':
				return Enums\EEnemyType::Mob;
			case 'boss':
				return Enums\EEnemyType::Boss;
			case 'miniboss':
				return Enums\EEnemyType::MiniBoss;
			case 'treasure_mob':
				return Enums\EEnemyType::TreasureMob;
		}
	}

}
