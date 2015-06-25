<?php
namespace SteamDB\CTowerAttack;

use SteamDB\CTowerAttack\Enums;
use \SteamDB\CTowerAttack\Player\TechTree\AbilityItem;

class Lane
{
	/*
	repeated Enemy enemies = 1;
	optional double dps = 2;
	optional double gold_dropped = 3;
	repeated ActiveAbility active_player_abilities = 4;
	repeated uint32 player_hp_buckets = 5;
	optional ETowerAttackElement element = 6;
	// for faster lookup
	optional double active_player_ability_decrease_cooldowns = 7 [default = 1];
	optional double active_player_ability_gold_per_click = 8 [default = 0];
	*/
	public $Players = array();
	public $Enemies;
	public $Dps;
	public $ActivityLog = [];
	private $GoldDropped; #TODO: Increase GoldDropped?
	private $ActivePlayerAbilities;
	private $LaneId;

	public function __construct(
		$LaneId,
		array $Enemies,
		$Dps,
		$GoldDropped,
		array $ActivePlayerAbilities,
		array $ActivityLog,
		array $PlayerHpBuckets,
		$Element,
		$ActivePlayerAbilityDecreaseCooldowns,
		$ActivePlayerAbilityGoldPerClick
	) {
		$this->LaneId = $LaneId;
		$this->Enemies = $Enemies;
		$this->Dps = $Dps;
		$this->GoldDropped = $GoldDropped;
		$this->ActivePlayerAbilities = $ActivePlayerAbilities;
		$this->ActivityLog = $ActivityLog;
		$this->PlayerHpBuckets = $PlayerHpBuckets;
		$this->Element = $Element;
		$this->ActivePlayerAbilityDecreaseCooldowns = $ActivePlayerAbilityDecreaseCooldowns;
		$this->ActivePlayerAbilityGoldPerClick = $ActivePlayerAbilityGoldPerClick;
	}

	public function ToArray()
	{
		return array(
			'enemies' => $this->GetEnemiesArray(),
			'dps' => (double) $this->GetDps(),
			'gold_dropped' => (double) $this->GetGoldDropped(),
			'active_player_abilities' => $this->GetActivePlayerAbilitiesAsArray(),
			'activity_log' => $this->ActivityLog,
			'player_hp_buckets' => $this->GetPlayerHpBuckets(),
			'element' => (int) $this->GetElement(),
			'active_player_ability_decrease_cooldowns' => (double) $this->HasActivePlayerAbilityDecreaseCooldowns(),
			'active_player_ability_gold_per_click' => (double) $this->GetGoldPerClickMultiplier() #TODO: GetActivePlayerAbilityGoldPerClick()
		);
	}

	public function GetLaneId()
	{
		return $this->LaneId;
	}

	public function GetEnemy( $Key )
	{
		if( !isset( $this->Enemies[ $Key ] ) )
		{
			return null;
		}
		
		return $this->Enemies[ $Key ];
	}

	public function GetEnemies()
	{
		return $this->Enemies;
	}

	public function GetEnemiesArray()
	{
		$EnemyArray = array();
		foreach( $this->GetEnemies() as $Enemy )
		{
			$EnemyArray[] = $Enemy->ToArray();
		}
		return $EnemyArray;
	}

	public function GetDps()
	{
		return $this->Dps;
	}

	public function GetGoldDropped()
	{
		return $this->GoldDropped;
	}

	public function GetActivePlayerAbilities()
	{
		return $this->ActivePlayerAbilities;
	}

	public function GetActivePlayerAbilitiesAsArray()
	{
		$ActivePlayerAbilities = [];
		foreach( $this->ActivePlayerAbilities as $ActivePlayerAbility )
		{
			if ( !isset( $ActivePlayerAbilities[ $ActivePlayerAbility->GetAbility() ] ) )
			{
				$ActivePlayerAbilities[ $ActivePlayerAbility->GetAbility() ] = [
					'ability' => $ActivePlayerAbility->GetAbility(),
					'quantity' => 1
				];
			}
			else
			{
				$ActivePlayerAbilities[ $ActivePlayerAbility->GetAbility() ][ 'quantity' ]++;
			}
		}
		return array_values( $ActivePlayerAbilities );
	}

	public function AddActivePlayerAbility( \SteamDB\CTowerAttack\Player\ActiveAbility $ActiveAbility )
	{
		$this->ActivePlayerAbilities[] = $ActiveAbility;
		$this->ActivityLog[] = $ActiveAbility->ToArray();
	}

	public function CheckActivePlayerAbilities( $Game, $SecondsPassed )
	{
		$SecondPassed = $SecondsPassed !== false && $SecondsPassed > 0;
		$HealingPercentage = 0;
		$ClearCooldowns = false;
		foreach( $this->ActivePlayerAbilities as $Key => $ActiveAbility )
		{
			if( $ActiveAbility->isDone() ) 
			{
				// TODO: @Contex: Remove whatever effects the ability had
				// TODO: @Contex: Do active abilities carry on over to the next lane? The logic below would fail if a player switches a lane..
				AbilityItem::HandleAbility( $Game, $this, null, $ActiveAbility, true );
				unset( $this->ActivePlayerAbilities[ $Key ] );
			} 
			else if( $SecondPassed )
			{
				switch( $ActiveAbility->GetAbility() )
				{
					case Enums\EAbility::Support_Heal:
						$HealingPercentage += AbilityItem::GetMultiplier( $ActiveAbility->GetAbility() );
						break;
					case Enums\EAbility::Item_ClearCooldowns:
						$ClearCooldowns = true;
						break;
				}
			}
		}

		$PlayersInLane = $Game->GetPlayersInLane( $this->GetLaneId() );
		# TODO: Check if $HealingPercentage is 0.1% or 10%, 0.1% would make more sense if it stacks..
		if( $SecondPassed )
		{
			foreach( $PlayersInLane as $PlayerInLane )
			{
				// Check "Medics" & heal
				if( $HealingPercentage > 0 && !$PlayerInLane->IsDead() )
				{
					# TODO: Check if $HealingPercentage is 0.1% or 10%, 0.1% would make more sense if it stacks..
					$PlayerInLane->IncreaseHp( $PlayerInLane->GetTechTree()->GetMaxHp() * $HealingPercentage * $SecondsPassed ); # TODO: GetHp() or GetTechTree()->GetMaxHp()?
				}
				// Clear player cooldowns
				if( $ClearCooldowns )
				{
					$PlayerInLane->ClearActiveAbilities();
				}
			}
		}
	}

	public function GetPlayerHpBuckets()
	{
		return $this->PlayerHpBuckets;
	}

	public function GetElement()
	{
		return $this->Element;
	}

	public function GetActivePlayerAbilityGoldPerClick()
	{
		return $this->ActivePlayerAbilityGoldPerClick;
	}

	public function GetPlayers()
	{
		return $this->Players;
	}

	public function AddPlayer( $Player )
	{
		$this->Players[ $Player->GetAccountId() ] = 1;
	}

	public function RemovePlayer( $Player )
	{
		unset( $this->Players[ $Player->GetAccountId() ] );
	}

	public function GiveGoldToPlayers( $Game, $Amount )
	{
		foreach( $this->Players as $AccountId => $Set ) 
		{
			$Player = $Game->GetPlayer( $AccountId );
			$Player->IncreaseGold( $Amount );
		}
	}

	public function GetAliveEnemy()
	{
		foreach( $this->Enemies as $Enemy )
		{
			if( !$Enemy->isDead() )
			{
				return $Enemy;
			}
		}
		return null;
	}

	public function GetAliveEnemies( $EnemyType = null )
	{
		$AliveEnumies = [];
		foreach( $this->Enemies as $Enemy )
		{
			if( !$Enemy->isDead() && ( $EnemyType !== null ? $EnemyType === $Enemy->GetType() : true ) )
			{
				$AliveEnumies[] = $Enemy;
			}
		}
		return $AliveEnumies;
	}

	public function GetDeadEnemies( $EnemyType = null )
	{
		$DeadEnemies = [];
		foreach( $this->Enemies as $Enemy )
		{
			if( $Enemy->isDead() && ( $EnemyType !== null ? $EnemyType === $Enemy->GetType() : true ) )
			{
				$DeadEnemies[] = $Enemy;
			}
		}
		return $DeadEnemies;
	}

	public function UpdateHpBuckets( $Players )
	{
		$this->PlayerHpBuckets = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		foreach( $Players as $Player )
		{
			$this->PlayerHpBuckets[ $Player->GetHpLevel() ]++;
		}
	}

	public function GetDamageMultiplier()
	{
		$DamageMultiplier = $this->GetActivePlayerAbilityMultipler( Enums\EAbility::Support_IncreaseDamage );
		return $DamageMultiplier !== 0 ? $DamageMultiplier : 1;
	}

	public function GetCritClickDamageAddition()
	{
		return $this->GetActivePlayerAbilityMultipler( Enums\EAbility::Support_IncreaseCritPercentage );
	}

	public function GetGoldPerClickMultiplier()
	{
		$GoldMultiplier = $this->GetActivePlayerAbilityMultipler( Enums\EAbility::Item_GoldPerClick );
		return $GoldMultiplier !== 0 ? 1 + $GoldMultiplier : 0;
	}

	public function GetEnemyGoldMultiplier()
	{
		$EnemyGoldMultiplier = $this->GetActivePlayerAbilityMultipler( Enums\EAbility::Support_IncreaseGoldDropped );
		return $EnemyGoldMultiplier !== 0 ? 1 + $EnemyGoldMultiplier : 0;
	}

	public function GetReflectDamageMultiplier()
	{
		# TODO: Check if $ReflectDamageMultiplier is 0.5% or 50%, 0.5% would make more sense if it stacks..
		return $this->GetActivePlayerAbilityMultipler( Enums\EAbility::Item_ReflectDamage ) / 10;
	}

	public function GetStealHealthMultiplier()
	{
		$StealHealthMultiplier = $this->GetActivePlayerAbilityMultipler( Enums\EAbility::Item_StealHealth );
		return $StealHealthMultiplier !== 0 ? 1 + $StealHealthMultiplier : 0;
	}

	public function HasActivePlayerAbilityDecreaseCooldowns()
	{
		return $this->GetActivePlayerAbilityMultipler( Enums\EAbility::Support_DecreaseCooldowns ) > 0;
	}

	public function HasActivePlayerAbilityMaxElementalDamage()
	{
		return $this->GetActivePlayerAbilityMultipler( Enums\EAbility::Item_MaxElementalDamage ) > 0;
	}

	private function GetActivePlayerAbilityMultipler( $AbilityId )
	{
		# TODO: @Contex: Create an additional array that can cache the result? Instead of looping every single time...
		$Multiplier = 0;
		foreach( $this->ActivePlayerAbilities as $ActivePlayerAbility )
		{
			if( $ActivePlayerAbility->GetAbility() === $AbilityId )
			{
				$Multiplier += AbilityItem::GetMultiplier( $ActivePlayerAbility->GetAbility() );
			}
		}
		return $Multiplier;
	}
}
