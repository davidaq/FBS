<?php
function fbpdirectsales() {
	global $configData;
	global $playerData;
	foreach($playerData['teams'] as $kp => $player) {
		$stored = 1 * $player['record']['productsStored'];
		$sell = 1 * $player['record']['sellStorage'];
		$buy = 1 * $player['record']['buyStorage'];
		$stored += $buy;
		if($sell > $stored) {
			$sell = $stored;
		}
		$player['record']['productsStored'] = $stored - $sell;
		$player['cash'] += $player['record']['sellStorageIncome'];
		$player['cash'] -= $player['record']['buyStorageCost'];
        $playerData['teams'][$kp] = $player;
	}
}
