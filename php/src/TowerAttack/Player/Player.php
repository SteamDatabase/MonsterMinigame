<?php
namespace SteamDB\CTowerAttack\Player;

use SteamDB\CTowerAttack\Enums;
use SteamDB\CTowerAttack\Server;
use SteamDB\CTowerAttack\Game;
use SteamDB\CTowerAttack\Util;
use SteamDB\CTowerAttack\Player\TechTree\Upgrade;
use SteamDB\CTowerAttack\Player\TechTree\AbilityItem;

class Player
{
	const MAX_CLICKS = 20; #TODO: Move to tuningData
	const ACTIVE_PERIOD = 120; #TODO: Move to tuningData
	const LOOT_TIME = 5; #TODO: Move to tuningData

	/*
	optional double hp = 1;
	optional uint32 current_lane = 2;
	optional uint32 target = 3;
	optional uint32 time_died = 4;
	optional double gold = 5;
	optional uint64 active_abilities_bitfield = 6;
	repeated ActiveAbility active_abilities = 7;
	optional double crit_damage = 8;
	repeated Loot loot = 9;
	*/

	public $LastActive;
	public $Hp;
	public $TimeDied = 0;
	public $LaneDamageBuffer = [];
	public $AbilityLastUsed = [];
	public $Stats;
	public $CritDamage;
	public $PlayerName;
	public $AccountId;
	private $LastLoot = null;
	private $CurrentLane = 1;
	private $Target = 0;
	private $Gold = 100000000; # TODO: Set to 0
	private $ActiveAbilitiesBitfield = 0;
	private $ActiveAbilities = [];
	private $Loot = [];
	private $TechTree;

	public function __construct( $AccountId, $PlayerName )
	{
		$this->AccountId = $AccountId;
		$this->PlayerName = $PlayerName;
		$this->Hp = self::GetTuningData( 'hp' );
		$this->Stats = new Stats;
		$this->TechTree = new TechTree\TechTree;
		$this->LaneDamageBuffer = [
			0 => 0,
			1 => 0,
			2 => 0
		];
	}

	public function IsActive( $Time )
	{
		return $Time < $this->LastActive + self::ACTIVE_PERIOD;
	}

	public function ToArray()
	{
		$Array = array(
			'hp' => (double) $this->GetHp(),
			'current_lane' => (int) $this->GetCurrentLane(),
			'target' => (int) $this->GetTarget(),
			'time_died' => (int) $this->GetTimeDied(),
			'gold' => (double) $this->GetGold(),
			'active_abilities' => $this->GetActiveAbilitiesToArray(),
			'active_abilities_bitfield' => (int) $this->GetActiveAbilitiesBitfield(),
			'crit_damage' => (double) $this->GetCritDamage(),
			'stats' => $this->Stats->ToArray()
		);
		if( !empty( $this->GetLoot() ) )
		{
			$Array[ 'loot' ] = $this->GetLoot();
		}
		return $Array;
	}

	public function HandleAbilityUsage( $Game, $RequestedAbilities )
	{
		foreach( $RequestedAbilities as $RequestedAbility )
		{
			if(
				(
					isset( $this->AbilityLastUsed[ $RequestedAbility[ 'ability' ] ] )
					&&
					$this->AbilityLastUsed[ $RequestedAbility[ 'ability' ] ] >= $Game->Time
				)
				||
				(
					$this->IsDead()
					&&
					$RequestedAbility[ 'ability' ] !== Enums\EAbility::Respawn
				)
			) {
				// Rate limit
				continue;
			}

			$this->AbilityLastUsed[ $RequestedAbility[ 'ability' ] ] = $Game->Time;

			$AllowedAbilityTypes =
			[
				Enums\EAbilityType::Support => true,
				Enums\EAbilityType::Offensive => true,
				Enums\EAbilityType::Item => true
			];

			if( isset( $AllowedAbilityTypes[ AbilityItem::GetType( $RequestedAbility[ 'ability' ] ) ] ) )
			{
				$this->UseAbility( $Game, $RequestedAbility[ 'ability' ] );
				continue;
			}

			# Handle rest of the abilities below
			// TODO: @Contex: move this to AbilityItem as well?

			switch( $RequestedAbility[ 'ability' ] )
			{
				case Enums\EAbility::Attack:
					$NumClicks = (int) $RequestedAbility[ 'num_clicks' ];

					if( $NumClicks > self::MAX_CLICKS )
					{
						$NumClicks = self::MAX_CLICKS;
					}
					else if( $NumClicks < 1 )
					{
						$NumClicks = 1;
					}

					$Damage = $NumClicks * $this->GetTechTree()->GetDamagePerClick();
					$Lane = $Game->GetLane( $this->GetCurrentLane() );

					// Abilities
					$Damage *= $Lane->GetDamageMultiplier();
					if( $Lane->HasActivePlayerAbilityMaxElementalDamage() )
					{
						$Damage *= $this->GetTechTree()->GetHighestElementalMultiplier();
					}

					// Elementals
					$Damage *= $this->GetTechTree()->GetExtraDamageMultipliers( $Lane->GetElement() );
					$this->CritDamage = 0;
					if( $this->IsCriticalHit( $Lane ) )
					{
						$Damage *= $this->GetTechTree()->GetDamageMultiplierCrit();
						$this->CritDamage = $Damage;
						$this->Stats->CritDamageDealt += $Damage;
					}

					$this->Stats->NumClicks += $NumClicks;
					$this->Stats->ClickDamageDealt += $Damage;
					$Game->NumClicks += $NumClicks;

					$this->LaneDamageBuffer[ $this->GetCurrentLane() ] += $Damage; # TODO: this logic isn't correct.. it shouldn't buffer the whole lane, FIX!

					$Enemy = $Lane->GetEnemy( $this->GetTarget() );

					if( $Enemy === null || $Enemy->IsDead() )
					{
						break;
					}

					$Enemy->ClickDamageTaken += $Damage;

					$GoldMultiplier = $Lane->GetGoldPerClickMultiplier();
					if( $GoldMultiplier > 0 )
					{
						$this->IncreaseGold( $GoldMultiplier * $NumClicks * $Enemy->GetGold() );
					}

					$StealHealthMultiplier = $Lane->GetStealHealthMultiplier();
					if( $StealHealthMultiplier > 0 )
					{
						$this->IncreaseHp( $StealHealthMultiplier * $NumClicks * $Damage );
					}

					break;
				case Enums\EAbility::ChangeLane:
					$NewLane = (int)$RequestedAbility[ 'new_lane' ];

					if( $NewLane < 0 || $NewLane > 2 )
					{
						break;
					}

					$Lane = $Game->GetLane( $this->GetCurrentLane() );
					$Lane->RemovePlayer( $this );

					$this->SetLane( $NewLane );

					$NewLane = $Game->GetLane( $this->GetCurrentLane() );
					$NewLane->AddPlayer( $this );

					break;
				case Enums\EAbility::Respawn:
					if( $this->IsDead() && $this->CanRespawn( $Game->Time ) )
					{
						$this->Respawn();
					}

					break;
				case Enums\EAbility::ChangeTarget:
					$NewTarget = (int)$RequestedAbility[ 'new_target' ];

					if( $NewTarget < 0 || $NewTarget > 3 )
					{
						break;
					}

					$this->SetTarget( $NewTarget );

					break;
				default:
					// Handle unknown ability?
					break;
			}
		}
	}

	public function HandleUpgrade( $Game, $Upgrades )
	{
		$HpUpgrade = false;
		foreach( $Upgrades as $UpgradeId )
		{
			$Upgrade = $this->GetTechTree()->GetUpgrade( $UpgradeId );
			if(
				( $Upgrade->GetCostForNextLevel() > $this->GetGold() ) // Not enough gold
			||  ( Upgrade::IsLevelOneUpgrade( $UpgradeId ) && $Upgrade->GetLevel() >= 1) // One level upgrades
			||  ( Upgrade::HasRequiredUpgrade( $UpgradeId ) && $this->GetTechTree()->GetUpgrade( Upgrade::GetRequiredUpgrade( $UpgradeId ) )->GetLevel() < Upgrade::GetRequiredLevel( $UpgradeId ) ) // Does not have the required upgrade & level
			)
			{
				continue;
			}
			$this->DecreaseGold( $Upgrade->GetCostForNextLevel() );
			$Upgrade->IncreaseLevel();
			if( Upgrade::IsElementalUpgrade( $UpgradeId ) ) // Elemental upgrade
			{
				$ElementalUpgrades = $this->GetTechTree()->GetElementalUpgrades();
				$TotalLevel = 0;
				foreach( $ElementalUpgrades as $ElementalUpgrade )
				{
					$TotalLevel += $ElementalUpgrade->GetLevel();
				}
				// Loop again to set the next level cost for each elemental
				foreach( $ElementalUpgrades as $ElementalUpgrade )
				{
					$ElementalUpgrade->SetPredictedCostForNextLevel( $TotalLevel );
				}
			}
			else if( $UpgradeId === Enums\EUpgrade::DPS_AutoFireCanon )
			{
				$this->getTechTree()->BaseDps = Upgrade::GetInitialValue( $UpgradeId );
				$this->getTechTree()->Dps = $this->getTechTree()->BaseDps;
			}
			else if( Upgrade::GetType( $UpgradeId ) === Enums\EUpgradeType::HitPoints )
			{
				$HpUpgrade = true;
			}
			else if( Upgrade::GetType( $UpgradeId ) === Enums\EUpgradeType::PurchaseAbility )
			{
				$this->GetTechTree()->AddAbilityItem( Upgrade::GetAbility( $UpgradeId ) );
			}
		}
		$this->GetTechTree()->RecalulateUpgrades();
		if( $HpUpgrade )
		{
			$this->Hp = $this->getTechTree()->GetMaxHp();
		}
	}

	public function HandleBadgePoints( $Game, $Abilities )
	{
		if( $this->GetTechTree()->GetBadgePoints() === 0 )
		{
			# Player cannot afford any ability, skip!
			return;
		}

		foreach( $Abilities as $AbilityId )
		{
			if( $this->GetTechTree()->GetBadgePoints() < AbilityItem::GetBadgePointCost( $AbilityId ) )
			{
				# Player cannot afford ability, skip!
				continue;
			}

			$this->GetTechTree()->DecreaseBadgePoints( AbilityItem::GetBadgePointCost( $AbilityId ) );
			$this->GetTechTree()->AddAbilityItem( $AbilityId );
		}
	}

	public static function GetRespawnTime()
	{
		return self::GetTuningData( 'respawn_time' );
	}

	public static function GetMinDeadTime()
	{
		return self::GetTuningData( 'min_dead_time' );
	}

	public static function GetGoldMultiplierWhileDead()
	{
		return self::GetTuningData( 'gold_multiplier_while_dead' );
	}

	public function IsDead()
	{
		return $this->GetHp() <= 0;
	}

	public function GetTechTree()
	{
		return $this->TechTree;
	}

	public function GetAccountId()
	{
		return $this->AccountId;
	}

	public function GetHp()
	{
		return $this->Hp;
	}

	public function IncreaseHp( $Amount )
	{
		$this->Hp += $Amount;
		if( $this->Hp > $this->GetTechTree()->GetMaxHp() )
		{
			$this->Hp = $this->GetTechTree()->GetMaxHp();
		}
	}

	public function GetHpPercentage()
	{
		return ( $this->GetHp() / $this->GetTechTree()->GetMaxHp() ) * 100;
	}

	public function GetHpLevel()
	{
		$HpLevel = floor($this->GetHpPercentage() / 10);
		return $HpLevel <= 0 ? 0 : ( $HpLevel > 9 ? 9 : $HpLevel );
	}

	public function GetCurrentLane()
	{
		return $this->CurrentLane;
	}

	public function SetLane( $Lane )
	{
		$this->CurrentLane = $Lane;
	}

	public function GetTarget()
	{
		return $this->Target;
	}

	public function SetTarget( $Target )
	{
		$this->Target = $Target;
	}

	public function GetTimeDied()
	{
		return $this->TimeDied;
	}

	public function CanRespawn( $Time, $IsAutomatic = false )
	{
		if ($IsAutomatic)
		{
			return $Time > $this->TimeDied + self::GetRespawnTime();
		}
		else
		{
			return $Time > $this->TimeDied + self::GetMinDeadTime();
		}
	}

	public function Respawn()
	{
		$this->Hp = $this->GetTechTree()->GetMaxHp();
		$this->TimeDied = 0;
	}

	public function Kill( $Time )
	{
		$this->TimeDied = $Time;
		$this->Hp = 0;
		$this->Stats->TimesDied++;
	}

	public function GetGold()
	{
		return $this->Gold;
	}

	public function IncreaseGold( $Amount )
	{
		$this->Gold += $Amount;
		$this->Stats->GoldRecieved += $Amount;
	}

	public function DecreaseGold( $Amount )
	{
		$this->Gold -= $Amount;
		if( $this->Gold < 0 )
		{
			$this->Gold = 0;
		}
		$this->Stats->GoldUsed += $Amount; # TODO: Check statement above, amount won't be the same
	}

	public function IsInvulnerable()
	{
		return $this->HasActiveAbility( Enums\EAbility::Item_Invulnerability );
	}

	public function AddAbilityItem( $Ability, $Quantity = 1 )
	{
		$this->GetTechTree()->AddAbilityItem( $Ability, $Quantity );
	}

	public function GetActiveAbilitiesBitfield()
	{
		return $this->ActiveAbilitiesBitfield;
	}

	public function GetActiveAbilities()
	{
		return $this->ActiveAbilities;
	}

	public function GetActiveAbilitiesToArray()
	{
		$ActiveAbilities = [];
		foreach( $this->ActiveAbilities as $ActiveAbility )
		{
			$ActiveAbilities[] = $ActiveAbility->ToArray();
		}
		return $ActiveAbilities;
	}

	public function AddActiveAbility( $Time, $Ability, $DecreaseCooldown = false )
	{
		$this->ActiveAbilitiesBitfield |= ( 1 << $Ability );
		$ActiveAbility = new ActiveAbility( $Time, $Ability, $this->PlayerName, $DecreaseCooldown );
		$this->ActiveAbilities[ $Ability ] = $ActiveAbility;
		return $ActiveAbility;
	}

	public function RemoveActiveAbility( $Ability )
	{
		$this->ActiveAbilitiesBitfield &= ~( 1 << $Ability );
		unset( $this->ActiveAbilities[ $Ability ] );
	}

	public function HasActiveAbility( $AbilityId )
	{
		return isset( $this->ActiveAbilities[ $AbilityId ] );
	}

	public function GetActiveAbility( $AbilityId )
	{
		return $this->ActiveAbilities[ $AbilityId ];
	}

	public function ClearActiveAbilities()
	{
		foreach( $this->ActiveAbilities as $ActiveAbility )
		{
			$this->RemoveActiveAbility( $ActiveAbility->GetAbility() );
		}
	}

	public function UseAbility( $Game, $Ability )
	{
		if( !$this->GetTechTree()->HasAbilityItem( $Ability ) )
		{
			return false;
		}
		else if( $this->HasActiveAbility( $Ability ) && !$this->GetActiveAbility( $Ability )->IsCooledDown( $Game->Time ) )
		{
			return false;
		}
		else if ( $Ability === Enums\EAbility::Item_KillTower ) # TODO: Move this to HandleAbility?
		{
			$Enemy = $Game->GetLane( $this->GetCurrentLane() )->GetEnemy( $this->GetTarget() );
			if( $Enemy === null || $Enemy->GetType() !== Enums\EEnemyType::Tower || $Enemy->IsDead() )
			{
				return false;
			}
		}
		else if ( $Ability === Enums\EAbility::Item_KillMob ) # TODO: Move this to HandleAbility?
		{
			$Enemy = $Game->GetLane( $this->GetCurrentLane() )->GetEnemy( $this->GetTarget() );
			if(
				$Enemy === null
				||
				(
					$Enemy->GetType() !== Enums\EEnemyType::Mob
					&&
					$Enemy->GetType() !== Enums\EEnemyType::MiniBoss
				) # TODO: Boss or MiniBoss?
				||
				$Enemy->IsDead()
			) {
				return false;
			}
		}
		else if ( $Ability === Enums\EAbility::Offensive_HighDamageOneTarget ) # TODO: Move this to HandleAbility?
		{
			$Enemy = $Game->GetLane( $this->GetCurrentLane() )->GetEnemy( $this->GetTarget() );
			if( $Enemy === null || $Enemy->IsDead() )
			{
				return false;
			}
		}
		else if (
			(
				$Ability === Enums\EAbility::Offensive_DamageAllTargets
				||
				$Ability === Enums\EAbility::Offensive_DOTAllTargets
			)
			&&
			count( $Game->GetLane( $this->GetCurrentLane() )->GetAliveEnemies() ) === 0
		) { # TODO: Move this to HandleAbility?
			return false;
		}

		// Ability executed succesfully!
		$ActiveAbility = $this->AddActiveAbility
		(
			$Game->Time,
			$Ability,
			$Game->GetLane( $this->GetCurrentLane() )->HasActivePlayerAbilityDecreaseCooldowns()
		);
		$this->GetTechTree()->RemoveAbilityItem( $Ability );
		$Game->NumAbilitiesActivated++;
		if( AbilityItem::GetType( $Ability ) === Enums\EAbilityType::Item  )
		{
			$Game->NumAbilityItemsActivated++;
		}

		// Add wormhole in all three lanes
		if( $Ability === Enums\EAbility::Item_SkipLevels )
		{
			foreach( $Game->Lanes as $LaneId => $Lane )
			{
				$Lane->AddActivePlayerAbility( $ActiveAbility );
			}
		}
		else
		{
			$Game->GetLane( $this->GetCurrentLane() )->AddActivePlayerAbility( $ActiveAbility ); # TODO @Contex: Move this to HandleAbility?
		}

		AbilityItem::HandleAbility(
			$Game,
			$Game->GetLane( $this->GetCurrentLane() ),
			$this,
			$ActiveAbility
		);
	}

	public function CheckActiveAbilities( Game $Game )
	{
		foreach( $this->ActiveAbilities as $Key => $ActiveAbility )
		{
			if( $ActiveAbility->IsCooledDown( $Game->Time ) )
			{
				AbilityItem::HandleAbility(
					$Game,
					$Game->GetLane( $this->GetCurrentLane() ),
					$this,
					$ActiveAbility,
					true
				);
				$this->RemoveActiveAbility( $Key );
			}
		}
	}

	public function GetCritDamage()
	{
		return $this->CritDamage;
	}

	public function IsCriticalHit( $Lane )
	{
		$CritPercentage = $this->GetTechTree()->GetCritPercentage();
		$CritPercentage += $Lane->GetCritClickDamageAddition();
		$CritPercentage *= 100;
		$RandPercent = mt_rand( 1, 100 );
		return $RandPercent < $CritPercentage;
	}

	public function GetLoot()
	{
		return $this->Loot;
	}

	public function AddLoot( $Time, $AbilityId )
	{
		$this->LastLoot = $Time;
		$this->Loot[] = [
			'ability' => $AbilityId
		];
		$this->AddAbilityItem( $AbilityId );
	}

	public function ClearLoot( $Time )
	{
		if( $this->LastLoot !== null && ( $this->LastLoot + self::LOOT_TIME ) < $Time )
		{
			$this->Loot = [];
			$this->LastLoot = null;
		}
	}

	public function IsLootDropped( )
	{
		$DropPercentage = $this->GetTechTree()->GetBossLootDropPercentage() * 100;
		$RandPercent = mt_rand( 1, 100 );
		return $RandPercent < $DropPercentage;
	}

	public static function GetTuningData( $Key = null )
	{
		$TuningData = Server::GetTuningData( 'player' );
		if( $Key === null )
		{
			return $TuningData;
		}
		else if ( !array_key_exists( $Key, $TuningData ) )
		{
			return null;
		}
		return $TuningData[ $Key ];
	}
}
