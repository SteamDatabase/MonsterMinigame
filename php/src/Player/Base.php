<?php
namespace SteamDB\CTowerAttack\Player;

class Base
{
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

	private $AccountId;
	private $Hp;
	private $CurrentLane;
	private $Target;
	private $TimeDied;
	private $Gold;
	private $ActiveAbilitiesBitfield;
	private $ActiveAbilities = array();
	private $CritDamage;
	private $Loot;
	private $TechTree;

	public function __construct( $AccountId )
	{
		$this->AccountId = $AccountId;
		$this->Hp = $this->GetTuningData( 'hp' );
		$this->CurrentLane = 1;
		$this->Target = 0;
		$this->TimeDied = 0;
		$this->Gold = 1000; // TODO: Start gold?
		$this->ActiveAbilitiesBitfield = 0;
		$this->ActiveAbilities = array();
		$this->CritDamage = 0; // TODO
		$this->Loot = array(); // TODO
		$this->TechTree = new TechTree\Base;
	}

	public function ToArray()
	{
		return array(
			'hp' => (double) $this->GetHp(),
			'current_lane' => (int) $this->GetCurrentLane(),
			'target' => (int) $this->GetTarget(),
			'time_died' => (int) $this->GetTimeDied(),
			'gold' => (double) $this->GetGold(),
			'active_abilities_bitfield' => (int) $this->GetActiveAbilitiesBitfield(),
			'crit_damage' => (double) $this->GetCritDamage()
		);
	}

	public function HandleAbilityUsage( $Game, $RequestedAbilities )
	{
		foreach( $RequestedAbilities as $RequestedAbility ) {
			switch( $RequestedAbility['ability'] ) {
				case \ETowerAttackAbility::Attack:
					$NumClicks = $RequestedAbility[ 'num_clicks' ];
					$Lane = $Game->GetLane( $this->GetCurrentLane() );
					$Enemy = $Lane->GetEnemy( $this->GetTarget() );
					$Damage = $NumClicks * $this->GetTechTree()->GetDamagePerClick();
					$Enemy->DecreaseHp( $Damage );
					if( $Enemy->IsDead() ) {
						$Lane->GiveGoldToPlayers( $Game, $Enemy->GetGold() );
					}
					$DeadLanes = 0;
					foreach( $Game->GetLanes() as $Lane ) {
						$Enemies = $Lane->GetEnemies();
						$DeadEnemies = 0;
						foreach( $Enemies as $Enemy ) {
							if( $Enemy->IsDead() ) {
								$DeadEnemies++;
							}
						}
						if( $DeadEnemies === count( $Enemies ) ) {
							$DeadLanes++;
						}
					}
					if( $DeadLanes === 3 ) {
						$Game->GenerateNewLevel();
					}
					break;
				case \ETowerAttackAbility::ChangeLane:
					$Lane = $Game->GetLane( $this->GetCurrentLane() );
					$Lane->RemovePlayer( $this );
					$this->SetLane( $RequestedAbility[ 'new_lane' ] );
					$NewLane = $Game->GetLane( $this->GetCurrentLane() );
					$NewLane->AddPlayer( $this );
					break;
				case \ETowerAttackAbility::Respawn:
					// TODO: logic pls
					break;
				case \ETowerAttackAbility::ChangeTarget:
					$this->SetTarget( $RequestedAbility[ 'new_target' ] );
					break;
				default:
					// Handle unknown ability?
					break;
			}
		}
	}

	public function HandleUpgrade( $Game, $Upgrades )
	{
		foreach( $Upgrades as $UpgradeId ) {
			$Upgrade = $this->GetTechTree()->GetUpgrade( $UpgradeId );
			if(
				( $Upgrade->GetCostForNextLevel() > $this->GetGold() ) // Not enough gold
			||  ( $Upgrade->IsLevelOneUpgrade() && $Upgrade->GetLevel() >= 1) // One level upgrades
			||  ( $Upgrade->HasRequiredUpgrade() && $this->GetTechTree()->GetUpgrade($Upgrade->GetRequiredUpgrade())->GetLevel() < $Upgrade->GetRequiredLevel()) // Does not have the required upgrade & level
			) {
				continue;
			}
			$this->DecreaseGold( $Upgrade->GetCostForNextLevel() );
			$Upgrade->IncreaseLevel();
			if( $Upgrade->IsElementalUpgrade() ) { // Elemental upgrade
				$ElementalUpgrades = $this->GetTechTree()->GetElementalUpgrades();
				$TotalLevel = 0;
				foreach( $ElementalUpgrades as $ElementalUpgrade ) {
					$TotalLevel += $ElementalUpgrade->GetLevel();
				}
				// Loop again to set the next level cost for each elemental
				foreach( $ElementalUpgrades as $ElementalUpgrade ) {
					$ElementalUpgrade->SetPredictedCostForNextLevel( $TotalLevel );
				}
			}
		}
		$this->GetTechTree()->RecalulateUpgrades();
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
		return $this->Hp * $this->GetTechTree()->GetHpMultiplier();
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

	public function GetGold()
	{
		return $this->Gold;
	}

	public function IncreaseGold( $Amount )
	{
		$this->Gold += $Amount;
	}

	public function DecreaseGold( $Amount )
	{
		$this->Gold -= $Amount;
	}

	public function GetActiveAbilitiesBitfield()
	{
		return $this->ActiveAbilitiesBitfield;
	}

	public function GetActiveAbilities()
	{
		return $this->ActiveAbilities;
	}

	public function GetCritDamage()
	{
		return $this->CritDamage;
	}

	public function GetLoot()
	{
		return $this->Loot;
	}

	private function GetTuningData( $Key = null )
	{
		$TuningData = \SteamDB\CTowerAttack\Server::GetTuningData( 'player' );
		if ($Key === null) {
			return $TuningData;
		} else if (!array_key_exists( $Key, $TuningData)) {
			return null;
		}
		return $TuningData[ $Key ];
	}
}
?>
