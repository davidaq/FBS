<?php
function fbpbonus()
{
	global $configData;
	global $playerData;
	foreach($playerData['teams'] as $kp => $player)
	{
		if(array_key_exists('bonus', $player['record']))
		{
			$player['cash'] += $player['record']['bonus'];
		}
		$playerData['teams'][$kp] = $player;
	}
}
?>
