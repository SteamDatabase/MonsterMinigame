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
	private $Position;
	private $Type;
	public $Hp;
	private $MaxHp;
	private $Dps;
	private $Timer;
	private $TimerDisabled = false;
	private $Gold;
	public $DamageTaken = 0;
	public $DpsDamageTaken = 0;
	public $ClickDamageTaken = 0;
	public $AbilityDamageTaken = 0;

	public function __construct( $NumPlayers, $Id, $Position, $Type, $Level )
	{
		if( $NumPlayers === 0 )
		{
			$NumPlayers = 1;
		}
		$this->Id = $Id;
		$this->Position = $Position;
		$this->Type = $Type;
		$this->MaxHp = self::GetHpAtLevel( $Type, $Level, $NumPlayers );
		$this->ResetTimer();
		$this->Hp = $this->MaxHp;
		$this->Dps = self::GetDpsAtLevel( $Type, $Level );
		$this->Gold = self::GetGoldAtLevel( $Type, $Level );
		Server::GetLogger()->debug( "Created new enemy [Id=$this->Id, Type=$this->Type, Hp=$this->Hp, MaxHp=$this->MaxHp, Dps=$this->Dps, Timer=$this->Timer, Gold=$this->Gold]" );
	}

	private static function GetHpAtLevel( $Type, $Level, $RoomSize )
	{
		$TuningData = self::GetTuningData( self::GetEnemyTypeName( $Type ) );
		return ceil( Util::PredictValue(
			$TuningData[ 'hp_exponent' ],
			$TuningData[ 'hp' ],
			$Level * ( $RoomSize / 1500 ) // TODO: Move constant somewhere else
		) );
	}

	private static function GetDpsAtLevel( $Type, $Level )
	{
		return self::GetValueAtLevel( 'dps', $Type, $Level );
	}

	private static function GetGoldAtLevel( $Type, $Level )
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
			'id' => $this->GetId(),
			'type' => $this->GetType(),
			'hp' => $this->GetHp(),
			'max_hp' => $this->GetMaxHp(),
			'dps' => $this->GetDps(),
			'gold' => $this->GetGold()
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

	public function GetPosition()
	{
		return $this->Position;
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

	public function GetDpsHpDifference()
	{
		return $this->Hp > 0 ? $this->DpsDamageTaken - $this->Hp : $this->Hp * -1;
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
		$SpawnChance = self::GetTuningData( self::GetEnemyTypeName( Enums\EEnemyType::TreasureMob ), 'chance' ) * 100;
		$RandPercent = mt_rand( 1, 100 );

		return $RandPercent <= $SpawnChance;
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
