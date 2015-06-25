<?php
namespace SteamDB\CTowerAttack\Player\TechTree;

use SteamDB\CTowerAttack\Enums;
use SteamDB\CTowerAttack\Server;
use SteamDB\CTowerAttack\Player\ActiveAbility;

class AbilityItem
{
	/*
		optional ETowerAttackAbility ability = 1;
		optional uint32 quantity = 2;
	*/

	private $Ability;
	private $Quantity;

	public function __construct( $Ability, $Quantity = 1 )
	{
		$this->Ability = $Ability;
		$this->Quantity = $Quantity;
	}

	public function ToArray()
	{
		return [
			'ability' => $this->Ability,
			'quantity' => $this->Quantity
		];
	}

	public function GetAbility()
	{
		return $this->Ability;
	}

	public function GetQuantity()
	{
		return $this->Quantity;
	}

	private function GetAbilityTuningData( $Key = null )
	{
		return self::GetTuningData( $this->Ability, $Key );
	}

	public static function GetName( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'name' );
	}

	public static function GetType( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'type' );
	}

	public static function GetMaxNumClicks( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'max_num_clicks' );
	}

	public static function GetGoldMultiplier( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'gold_multiplier' );
	}

	public static function GetMultiplier( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'multiplier' );
	}

	public static function GetMultiplierBoss( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'multiplier_boss' );
	}

	public static function IsInstant( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'instant' ) === 1;
	}

	public static function GetBadgePointCost( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'badge_points_cost' );
	}

	public static function GetDuration( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'duration' );
	}

	public static function GetCooldown( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'cooldown' );
	}

	public static function GetDescription( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'desc' );
	}

	public static function GetTuningData( $AbilityId = null, $Key = null )
	{
		$TuningData = Server::GetTuningData( 'abilities' );
		if( $AbilityId === null ) 
		{
			return $TuningData;
		} 
		else if( $Key === null ) 
		{
			return $TuningData[ $AbilityId ];
		} 
		else if( !isset( $TuningData[ $AbilityId ][ $Key ] ) ) 
		{
			return null;
		}
		return $TuningData[ $AbilityId ][ $Key ];
	}

	public static function GetAbilities( $Type = null)
	{
		if( $Type !== null )
		{
			$TypeAbilities = [];
			$Abilities = Enums\EAbility::GetList();
			foreach( $Abilities as $AbilityName => $AbilityId )
			{
				if( self::GetType( $AbilityId ) === $Type )
				{
					$TypeAbilities[] = $AbilityId;
				}
			}
			return $TypeAbilities;
		}
		else
		{
	        return Enums\EAbility::GetList();
		}
	}

	public static function GetRandomAbilityItem()
	{
		$ItemAbilities = self::GetAbilities( Enums\EAbilityType::Item );
		return $ItemAbilities[ array_rand( $ItemAbilities ) ];
	}

	public static function HandleAbility( $Game, $Lane, $Player, $Ability, $Deactivate = false )
	{
		$AbilityMultiplier = self::GetMultiplier( $Ability->GetAbility() );

		switch( $Ability->GetAbility() )
		{
			case Enums\EAbility::Offensive_HighDamageOneTarget:
				if( !$Deactivate )
				{
					$Enemy = $Lane->GetEnemy( $Player->GetTarget() );
					if( $Enemy !== null && !$Enemy->IsDead() )
					{
						$Damage = $Enemy->GetMaxHp();
						if( $Enemy->GetType() === Enums\EEnemyType::Boss )
						{
							$Damage *= self::GetMultiplierBoss( Enums\EAbility::Offensive_HighDamageOneTarget );
						}
						else
						{
							$Damage *= self::GetMultiplier( Enums\EAbility::Offensive_HighDamageOneTarget );
						}
						$Player->Stats->AbilityDamageDealt += $Damage;
						$Enemy->DecreaseHp( $Damage );
					}
				}
				break;
			case Enums\EAbility::Offensive_DamageAllTargets:
				if( !$Deactivate )
				{
					$Enemies = $Lane->GetAliveEnemies();
					foreach( $Enemies as $Enemy )
					{
						$Damage = $Enemy->GetMaxHp() * $AbilityMultiplier;
						$Player->Stats->AbilityDamageDealt += $Damage;
						$Enemy->DecreaseHp( $Damage );
					}
				}
				break;
			case Enums\EAbility::Offensive_DOTAllTargets:
				if( !$Deactivate )
				{
					$Enemies = $Lane->GetAliveEnemies();
					foreach( $Enemies as $Enemy )
					{
						$Damage = $Enemy->GetMaxHp() * $AbilityMultiplier;
						$Player->Stats->AbilityDamageDealt += $Damage;
						$Enemy->DecreaseHp( $Damage );
					}
				}
				break;
			case Enums\EAbility::Item_Resurrection:
				if( !$Deactivate )
				{
					$PlayersInLane = $Game->GetPlayersInLane( $Lane->GetLaneId() );
					foreach( $PlayersInLane as $PlayerInLane )
					{
						if( $PlayerInLane->IsDead() )
						{
							$PlayerInLane->Respawn();
						}
					}
				}
				break;
			case Enums\EAbility::Item_KillTower:
				if( !$Deactivate )
				{
					$Enemy = $Lane->GetEnemy( $Player->GetTarget() );
					if( $Enemy->GetType() === Enums\EEnemyType::Tower )
					{
						$Enemy->SetHp( 1 );
					}
				}
				break;
			case Enums\EAbility::Item_KillMob:
				if( !$Deactivate )
				{
					$Enemy = $Lane->GetEnemy( $Player->GetTarget() );

					if( $Enemy === null )
					{
						break;
					}

					if( $Enemy->GetType() === Enums\EEnemyType::Mob )
					{
						$Enemy->SetHp( 1 );
					}
					else if( $Enemy->GetType() === Enums\EEnemyType::MiniBoss ) # TODO: Boss or MiniBoss?
					{
						$MaxPercentage = $AbilityMultiplier;
						$Percentage = $MaxPercentage + ( lcg_value() * ( abs( $MaxPercentage - 0.01 ) ) ); # 1% - 5%
						$Damage = $Enemy->GetMaxHp() * $Percentage;
						$Player->Stats->AbilityDamageDealt += $Damage;
						$Enemy->DecreaseHp( $Damage );
					}
				}
				break;
			case Enums\EAbility::Item_IncreaseCritPercentagePermanently:
				if( !$Deactivate )
				{
					$Player->GetTechTree()->IncreaseCritPercentage( $AbilityMultiplier );
					$Player->GetTechTree()->RecalulateUpgrades();
					$Lane->AddActivePlayerAbility
					( 
						new ActiveAbility
						( 
							$Game->Time,
							Enums\EAbility::Support_IncreaseCritPercentage, 
							$Player->PlayerName,
							$Lane->HasActivePlayerAbilityDecreaseCooldowns()
						) 
					);
				}
				break;
			case Enums\EAbility::Item_IncreaseHPPermanently:
				if( !$Deactivate )
				{
					$Player->GetTechTree()->IncreaseHpMultiplier( $AbilityMultiplier );
					$Player->GetTechTree()->RecalulateUpgrades();
					$Lane->AddActivePlayerAbility
					( 
						new ActiveAbility
						( 
							$Game->Time,
							Enums\EAbility::Support_Heal, 
							$Player->PlayerName,
							$Lane->HasActivePlayerAbilityDecreaseCooldowns()
						) 
					);
				}
				break;
			case Enums\EAbility::Item_GoldForDamage:
				if( !$Deactivate )
				{
					$Enemy = $Lane->GetEnemy( $Player->GetTarget() );

					if( $Enemy === null )
					{
						break;
					}

					$MaxPercentage = $AbilityMultiplier;
					$Percentage = $MaxPercentage + ( lcg_value() * ( abs( $MaxPercentage - 0.01 ) ) ); # 1% - 10%
					$Player->DecreaseGold( $Player->GetGold() * $MaxPercentage ); # 10%
					$Damage = $Enemy->GetMaxHp() * $Percentage;
					$Player->Stats->AbilityDamageDealt += $Damage;
					$Enemy->DecreaseHp( $Damage );
				}
				break;
			case Enums\EAbility::Item_GiveGold:
				if( !$Deactivate )
				{
					$Player->IncreaseGold( $AbilityMultiplier );
					$Lane->AddActivePlayerAbility( 
						new ActiveAbility
						(
							$Game->Time,
							Enums\EAbility::Support_IncreaseGoldDropped, 
							$Player->PlayerName,
							$Lane->HasActivePlayerAbilityDecreaseCooldowns() 
						) 
					);
				}
				break;
			case Enums\EAbility::Item_GiveRandomItem:
				if( !$Deactivate )
				{
					$PlayersInLane = $Game->GetPlayersInLane( $Lane->GetLaneId() );
					foreach( $PlayersInLane as $PlayerInLane )
					{
						if( !$PlayerInLane->IsDead() )
						{
							$PlayerInLane->AddAbilityItem( self::GetRandomAbilityItem() );
						}
					}
				}
				break;
			case Enums\EAbility::Item_SkipLevels:
				if( !$Deactivate )
				{
					$Game->WormholeCount++;

					$PlayersInLane = $Game->GetPlayersInLane( $Lane->GetLaneId() );
					$AbilityGold = self::GetGoldMultiplier( Enums\EAbility::Item_SkipLevels );

					foreach( $PlayersInLane as $PlayerInLane )
					{
						$PlayerInLane->IncreaseGold( $AbilityGold ); # TODO: Is gold stackable as well? Is it applied AFTER or instant?
					}
				}
				break;
			break;
		}
	}
}
