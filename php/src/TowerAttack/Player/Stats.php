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
			'gold_recieved' => $this->GoldRecieved,
			'gold_used' => $this->GoldUsed,
			'num_clicks' => $this->NumClicks,
			'crit_damage_dealt' => $this->CritDamageDealt,
			'click_damage_dealt' => $this->ClickDamageDealt,
			'dps_damage_dealt' => $this->DpsDamageDealt,
			'ability_damage_dealt' => $this->AbilityDamageDealt,
			'damage_taken' =>$this->DamageTaken,
			'times_died' => $this->TimesDied
		];
	}
}
