<?php
// function name same as file name
function fbpinit()
{
	global $configData;
	global $playerData;
	$playerIndex = -1;
	foreach($configData['players'] as $playername)
	{
		$playerIndex++;
		$player = array();
		$player['name'] = $playername;
		$player['loan'] = 0;
		$player['cash'] = $configData['Initial cash'];
		$player['homeMarket'] = $playerIndex;
		$player['marketAgents'] = array($playerIndex => 1);
		$playerData['teams'][] = $player;
	}
	$playerData['global']['round'] = 0;
}
?>
