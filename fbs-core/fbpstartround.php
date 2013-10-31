<?php
function fbpstartround()
{
	global $configData;
	global $playerData;
	foreach($playerData['teams'] as $kp => $player)
	{
		$player['record']['cashBeforeDecision'] = $player['cash'];
		if(!isset($player['record']['componentsStored']))
			$player['record']['componentsStored'] = 0;
		$player['record']['componentsStorageBeforeDecision'] = $player['record']['componentsStored'];
		if(!isset($player['record']['productsStored']))
			$player['record']['productsStored'] = 0;
		$player['record']['productsStorageBeforeDecision'] = $player['record']['productsStored'];
		$playerData['teams'][$kp] = $player;
	}
	if(!isset($playerData['global']['round']))
		$playerData['global']['round'] = 0;
	$playerData['global']['round'] += 1;
}
?>
