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

	public static function GetMultiplier( $AbilityId )
	{
		return self::GetTuningData( $AbilityId, 'multiplier' );
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
			case Enums\EAbility::Support_IncreaseGoldDropped:
				// TODO: Add ability logic
				break;
			case Enums\EAbility::Support_DecreaseCooldowns:
				// TODO: Add ability logic
				break;
			case Enums\EAbility::Offensive_HighDamageOneTarget:
				// TODO: Add ability logic
				break;
			case Enums\EAbility::Offensive_DamageAllTargets:
				// TODO: Add ability logic
				break;
			case Enums\EAbility::Offensive_DOTAllTargets:
				// TODO: Add ability logic
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
					if( $Enemy->GetType() === Enums\EEnemyType::Mob )
					{
						$Enemy->SetHp( 1 );
					}
					else if( $Enemy->GetType() === Enums\EEnemyType::MiniBoss )
					{
						$MaxPercentage = self::GetMultiplier( Enums\EAbility::Item_KillMob );
						$Percentage = $BasePercentage + ( lcg_value() * ( abs( $MaxPercentage - 0.01 ) ) ); # 1% - 5%
						$Damage = $Enemy->GetMaxHp() * $Percentage;
						$Player->Stats->AbilityDamageDealt += $Damage;
						$Enemy->DamageTaken += $Damage; # TODO: Should we set DamageTaken or just set the HP?
					}
				}
				break;
				break;
			case Enums\EAbility::Item_MaxElementalDamage:
				// TODO: Add ability logic
				break;
			case Enums\EAbility::Item_GoldPerClick:
				// TODO: DELETE WHOLE CASE
				if( !$Deactivate )
				{
					$Player->IncreaseGold( 100000000 );
				}
				break;
			case Enums\EAbility::Item_IncreaseCritPercentagePermanently:
				if( !$Deactivate )
				{
					$Player->GetTechTree()->IncreaseCritPercentage( $AbilityMultiplier );
					$Player->GetTechTree()->RecalulateUpgrades();
					$Lane->AddActivePlayerAbility( new ActiveAbility( Enums\EAbility::Support_IncreaseCritPercentage, $Player->PlayerName ) );
				}
				break;
			case Enums\EAbility::Item_IncreaseHPPermanently:
				if( !$Deactivate )
				{
					$Player->GetTechTree()->IncreaseHpMultiplier( $AbilityMultiplier );
					$Player->GetTechTree()->RecalulateUpgrades();
					$Lane->AddActivePlayerAbility( new ActiveAbility( Enums\EAbility::Support_Heal, $Player->PlayerName ) );
				}
				break;
			case Enums\EAbility::Item_GoldForDamage:
				if( !$Deactivate )
				{
					$MaxPercentage = self::GetMultiplier( Enums\EAbility::Item_GoldForDamage );
					$Percentage = $BasePercentage + ( lcg_value() * ( abs( $MaxPercentage - 0.01 ) ) ); # 1% - 10%
					$Player->DecreaseGold( $Player->GetGold() * $MaxPercentage ); # 10%
					$Enemy = $Lane->GetEnemy( $Player->GetTarget() );
					$Damage = $Enemy->GetMaxHp() * $Percentage;
					$Player->Stats->AbilityDamageDealt += $Damage;
					$Enemy->DamageTaken += $Damage; # TODO: Should we set DamageTaken or just set the HP?
				}
				break;
			case Enums\EAbility::Item_GiveGold:
				if( !$Deactivate )
				{
					$Player->IncreaseGold( self::GetMultiplier( Enums\EAbility::Item_GiveGold ) );
					$Lane->AddActivePlayerAbility( new ActiveAbility( Enums\EAbility::Support_IncreaseGoldDropped, $Player->PlayerName ) );
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
				// TODO: Add ability logic
				// TODO: stackable? check if player has ability? etc
				// TODO: debugging
				if( $Deactivate )
				{
				}
				else
				{
					l( 'Skipping level' );
					$Game->GenerateNewLevel();
				}
				break;
			break;
			default:
			case Enums\EAbility::Item_Invulnerability:
			case Enums\EAbility::Item_StealHealth:
			case Enums\EAbility::Support_Heal:
			case Enums\EAbility::Support_IncreaseDamage:
			case Enums\EAbility::Support_IncreaseCritPercentage:
			case Enums\EAbility::Item_ClearCooldowns:
			case Enums\EAbility::Item_ReflectDamage:
				# Delete?
				break;
		}
	}
}
