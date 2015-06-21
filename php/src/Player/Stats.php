<?php
namespace SteamDB\CTowerAttack\Player;

class Stats
{
	public $GoldRecieved = 0;
	public $GoldUsed = 0;
	public $NumClicks = 0;
	public $DamageDealt = 0;
	public $DamageTaken = 0;
	public $TimesDied = 0;

	public function ToArray()
	{
		return [
			'gold_recieved' => (double) $this->GoldRecieved,
			'gold_used' => (double) $this->GoldUsed,
			'num_clicks' => (int) $this->NumClicks,
			'damage_dealt' => (double) $this->DamageDealt,
			'damage_taken' =>(double) $this->DamageTaken,
			'times_died' => (double) $this->TimesDied
		];
	}
}
