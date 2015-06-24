<?php
namespace SteamDB\CTowerAttack\Player;

use SteamDB\CTowerAttack\Enums;
use SteamDB\CTowerAttack\Server;
use SteamDB\CTowerAttack\Game;
use SteamDB\CTowerAttack\Player\TechTree\Upgrade;
use SteamDB\CTowerAttack\Player\TechTree\AbilityItem;

class Player
{
	const MAX_CLICKS = 20;
	const ACTIVE_PERIOD = 120; // seconds
	
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
	private $CurrentLane = 1;
	private $Target = 0;
	private $Gold = 10;
	private $ActiveAbilitiesBitfield = 0;
	private $ActiveAbilities = [];
	private $Loot = [];
	private $TechTree;

	public function __construct( $AccountId, $PlayerName )
	{
		$this->LastActive = time();
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

		// TODO
		#$this->AddAbilityItem( Enums\EAbility::Item_GoldPerClick, 3 );
		#$this->AddAbilityItem( Enums\EAbility::Item_SkipLevels, 1 );

		// support abilities
		$this->AddAbilityItem( Enums\EAbility::Support_IncreaseDamage );
		$this->AddAbilityItem( Enums\EAbility::Support_Heal );
		$this->AddAbilityItem( Enums\EAbility::Support_IncreaseGoldDropped );
		$this->AddAbilityItem( Enums\EAbility::Support_DecreaseCooldowns );

		// offensive abilities
		$this->AddAbilityItem( Enums\EAbility::Offensive_HighDamageOneTarget );
		$this->AddAbilityItem( Enums\EAbility::Offensive_DamageAllTargets );
		$this->AddAbilityItem( Enums\EAbility::Offensive_DOTAllTargets );

		// item
		$this->AddAbilityItem( Enums\EAbility::Item_Resurrection );
		$this->AddAbilityItem( Enums\EAbility::Item_KillTower );
		$this->AddAbilityItem( Enums\EAbility::Item_KillMob );
		$this->AddAbilityItem( Enums\EAbility::Item_MaxElementalDamage );
		$this->AddAbilityItem( Enums\EAbility::Item_GoldPerClick );
		$this->AddAbilityItem( Enums\EAbility::Item_IncreaseCritPercentagePermanently );
		$this->AddAbilityItem( Enums\EAbility::Item_IncreaseHPPermanently );
		$this->AddAbilityItem( Enums\EAbility::Item_GoldForDamage );
		$this->AddAbilityItem( Enums\EAbility::Item_Invulnerability );
		$this->AddAbilityItem( Enums\EAbility::Item_GiveGold );
		$this->AddAbilityItem( Enums\EAbility::Item_StealHealth );
		$this->AddAbilityItem( Enums\EAbility::Item_ReflectDamage );
		$this->AddAbilityItem( Enums\EAbility::Item_GiveRandomItem );
		$this->AddAbilityItem( Enums\EAbility::Item_SkipLevels );
		$this->AddAbilityItem( Enums\EAbility::Item_ClearCooldowns );
	}

	public function IsActive()
	{
		return time() < $this->LastActive + self::ACTIVE_PERIOD;
	}

	public function ToArray()
	{
		return array(
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
	}

	public function HandleAbilityUsage( $Game, $RequestedAbilities )
	{
		foreach( $RequestedAbilities as $RequestedAbility ) 
		{
			if( 
				( 
					isset( $this->AbilityLastUsed[ $RequestedAbility[ 'ability' ] ] ) 
					&& 
					$this->AbilityLastUsed[ $RequestedAbility[ 'ability' ] ] + 1 > time() 
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

			$AllowedAbilityTypes =
			[
				Enums\EAbilityType::Support,
				Enums\EAbilityType::Offensive,
				Enums\EAbilityType::Item
			];

			if( in_array( AbilityItem::GetType( $RequestedAbility[ 'ability' ] ), $AllowedAbilityTypes ) )
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

					// Elementals
					$Damage *= $this->GetTechTree()->GetExtraDamageMultipliers( $Lane->GetElement() );
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
					$Enemy->DamageTaken += $Damage;
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
					$Lane = $Game->GetLane( $this->GetCurrentLane() );
					$Lane->RemovePlayer( $this );
					$this->SetLane( $RequestedAbility[ 'new_lane' ] );
					$NewLane = $Game->GetLane( $this->GetCurrentLane() );
					$NewLane->AddPlayer( $this );
					break;
				case Enums\EAbility::Respawn:
					if( $this->IsDead() && $this->CanRespawn() )
					{
						$this->Respawn();
					}
					break;
				case Enums\EAbility::ChangeTarget:
					$this->SetTarget( $RequestedAbility[ 'new_target' ] );
					break;
				default:
					// Handle unknown ability?
					break;
			}

			$this->AbilityLastUsed[ $RequestedAbility[ 'ability' ] ] = time();
		}
	}

	public function HandleUpgrade( $Game, $Upgrades )
	{
		$HpUpgrade = false;
		foreach( $Upgrades as $UpgradeId ) {
			$Upgrade = $this->GetTechTree()->GetUpgrade( $UpgradeId );
			if(
				( $Upgrade->GetCostForNextLevel() > $this->GetGold() ) // Not enough gold
			||  ( $Upgrade->IsLevelOneUpgrade() && $Upgrade->GetLevel() >= 1) // One level upgrades
			||  ( Upgrade::HasRequiredUpgrade( $UpgradeId ) && $this->GetTechTree()->GetUpgrade( Upgrade::GetRequiredUpgrade( $UpgradeId ) )->GetLevel() < Upgrade::GetRequiredLevel( $UpgradeId ) ) // Does not have the required upgrade & level
			) 
			{
				continue;
			}
			$this->DecreaseGold( $Upgrade->GetCostForNextLevel() );
			$Upgrade->IncreaseLevel();
			if( $Upgrade->IsElementalUpgrade() ) // Elemental upgrade
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
		// TODO: fix if percentage is below 0 or above 100
		$HpLevel = floor($this->GetHpPercentage() / 10) - 1;
		return $HpLevel <= 0 ? 0 : $HpLevel;
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

	public function CanRespawn( $IsAutomatic = false )
	{
		if ($IsAutomatic)
		{
			return time() > $this->TimeDied + self::GetRespawnTime();
		}
		else
		{
			return time() > $this->TimeDied + self::GetMinDeadTime();
		}
	}

	public function Respawn()
	{
		$this->Hp = $this->GetTechTree()->GetMaxHp();
		$this->TimeDied = 0;
	}

	public function Kill()
	{
		$this->TimeDied = time();
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
		$this->Stats->GoldUsed += $Amount;
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

	public function AddActiveAbility( $Ability )
	{
		$this->ActiveAbilitiesBitfield |= ( 1 << $Ability );
		$ActiveAbility = new ActiveAbility( $Ability, $this->PlayerName );
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
		else if ( $Ability === Enums\EAbility::Item_KillTower ) # TODO: Move this to HandleAbility?
		{
			$Enemy = $Game->GetLane( $this->GetCurrentLane() )->GetEnemy( $this->GetTarget() );
			if( $Enemy->GetType() !== Enums\EEnemyType::Tower )
			{
				return false;
			}
		}
		else if ( $Ability === Enums\EAbility::Item_KillMob ) # TODO: Move this to HandleAbility?
		{
			$Enemy = $Game->GetLane( $this->GetCurrentLane() )->GetEnemy( $this->GetTarget() );
			if( $Enemy->GetType() !== Enums\EEnemyType::Mob && $Enemy->GetType() !== Enums\EEnemyType::MiniBoss )
			{
				return false;
			}
		}

		// Ability executed succesfully!
		$ActiveAbility = $this->AddActiveAbility( $Ability );
		$this->GetTechTree()->RemoveAbilityItem( $Ability );
		$Game->GetLane( $this->GetCurrentLane() )->AddActivePlayerAbility( $ActiveAbility ); # TODO @Contex: Move this to HandleAbility?

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
			if( $ActiveAbility->IsCooledDown() )
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
		$RandPercent = rand( 1, 100 );
		return $RandPercent < $CritPercentage;
	}

	public function GetLoot()
	{
		return $this->Loot;
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
