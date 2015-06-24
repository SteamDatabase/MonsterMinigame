<?php
namespace SteamDB\CTowerAttack\Player;

class Stats
{
	public $GoldRecieved = 0;
	public $GoldUsed = 0;
	public $NumClicks = 0;
	public $CritDamageDealt = 0;
	public $ClickDamageDealt = 0;
	public $DpsDamageDealt = 0;
	public $AbilityDamageDealt = 0;
	public $DamageTaken = 0;
	public $TimesDied = 0;

	public function ToArray()
	{
		return [
			'gold_recieved' => (double) $this->GoldRecieved,
			'gold_used' => (double) $this->GoldUsed,
			'num_clicks' => (int) $this->NumClicks,
			'crit_damage_dealt' => (double) $this->CritDamageDealt,
			'click_damage_dealt' => (double) $this->ClickDamageDealt,
			'dps_damage_dealt' => (double) $this->DpsDamageDealt,
			'ability_damage_dealt' => (double) $this->AbilityDamageDealt,
			'damage_taken' =>(double) $this->DamageTaken,
			'times_died' => (double) $this->TimesDied
		];
	}
}
