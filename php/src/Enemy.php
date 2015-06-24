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
		// TODO: TreasureMob has Lifetime and Chance, needs to be remove after x time?
		// TODO: Figure out if valve floored the value or just rounded
		$this->MaxHp = self::GetHpAtLevel( $Type, $Level );
		// Deal with respawn/alive timer
		// TODO: dps is wrong and does not match valve's data
		$this->ResetTimer();
		$this->Hp = $this->MaxHp;
		$this->Dps = self::GetDpsAtLevel( $Type, $Level );
		$this->Gold = self::GetGoldAtLevel( $Type, $Level );
		l( "Created new enemy [Id=$this->Id, Type=$this->Type, Hp=$this->Hp, MaxHp=$this->MaxHp, Dps=$this->Dps, Timer=$this->Timer, Gold=$this->Gold]" );
	}

	public static function GetHpAtLevel( $Type, $Level )
	{
		$TuningData = self::GetTuningData( self::GetEnemyTypeName( $Type ) );
		if( $Type === Enums\EEnemyType::MiniBoss ) 
		{
			$LowestHp = self::GetValueAtLevel( 'hp', $Type, $Level, false ); # TODO: floor it?
			$MiddleHp = $LowestHp * 1.84; # TODO: move to tuningData.json?
			$HighestHp = $LowestHp * 2.83; # TODO: move to tuningData.json?
			$HpArray = [ $LowestHp, $MiddleHp, $HighestHp ];
			return floor( $HpArray[ array_rand( $HpArray ) ] );
		} 
		else 
		{
			return self::GetValueAtLevel( 'hp', $Type, $Level );
		}
	}

	public static function GetDpsAtLevel( $Type, $Level )
	{
		$Level += 1; # @Contex: wtf Volvo, why?
		$TuningData = self::GetTuningData( self::GetEnemyTypeName( $Type ) );
		$HighestDps = Util::PredictValue(
			$TuningData[ 'dps_exponent' ], 
			$TuningData[ 'dps' ], 
			$Level * $TuningData[ 'dps_multiplier' ]
		);
		$MiddleDps = Util::PredictValue(
			$TuningData[ 'dps_exponent' ] - 0.1, # TODO: Move 0.1 to tuningData?
			$TuningData[ 'dps' ], 
			$Level * $TuningData[ 'dps_multiplier' ]
		);
		$LowestDps = Util::PredictValue(
			$TuningData[ 'dps_exponent' ] - ( 0.1 * 2 ), # TODO: Move 0.1 to tuningData?
			$TuningData[ 'dps' ], 
			$Level * $TuningData[ 'dps_multiplier' ]
		);
		$DpsArray = [ $LowestDps, $MiddleDps, $HighestDps ];
		return floor( $DpsArray[ array_rand( $DpsArray ) ] );
	}

	public static function GetGoldAtLevel( $Type, $Level )
	{
		$Level += 1;
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
