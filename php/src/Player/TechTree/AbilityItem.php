<?php
namespace SteamDB\CTowerAttack\Player\TechTree;

use SteamDB\CTowerAttack\Enums;
use SteamDB\CTowerAttack\Server;

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

	public static function HandleAbility( $Game, $Lane, $Player, $Ability, $Deactivate = false )
	{
		$AbilityMultiplier = self::GetMultiplier( $Ability->GetAbility() );

		switch( $Ability->GetAbility() )
		{
			case Enums\EAbility::Support_IncreaseDamage:
				# Delete?
				break;
			case Enums\EAbility::Support_IncreaseCritPercentage:
				# Delete?
				break;
			case Enums\EAbility::Support_Heal:
				// TODO: Add ability logic
				break;
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
				// TODO: Add ability logic
				break;
			case Enums\EAbility::Item_KillMob:
				// TODO: Add ability logic
				break;
			case Enums\EAbility::Item_MaxElementalDamage:
				// TODO: Add ability logic
				break;
			case Enums\EAbility::Item_GoldPerClick:
				// TODO: Add ability logic
				if( $Deactivate )
				{

				}
				else
				{
					$Player->IncreaseGold( 100000000 );
				}
				break;
			case Enums\EAbility::Item_IncreaseCritPercentagePermanently:
				// TODO: Add ability logic
				break;
			case Enums\EAbility::Item_IncreaseHPPermanently:
				// TODO: Add ability logic
				break;
			case Enums\EAbility::Item_GoldForDamage:
				// TODO: Add ability logic
				break;
			case Enums\EAbility::Item_Invulnerability:
				// TODO: Add ability logic
				break;
			case Enums\EAbility::Item_GiveGold:
				// TODO: Add ability logic
				break;
			case Enums\EAbility::Item_StealHealth:
				// TODO: Add ability logic
				break;
			case Enums\EAbility::Item_ReflectDamage:
				// TODO: Add ability logic
				break;
			case Enums\EAbility::Item_GiveRandomItem:
				// TODO: Add ability logic
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
			case Enums\EAbility::Item_ClearCooldowns:
				// TODO: Add ability logic
			break;
		}
	}
}
