<?php
function fbploan()
{
	global $configData;
	global $playerData;
	foreach($playerData['teams'] as $kp => $player)
	{
		if(!isset($player['record']['loan']))
			$player['record']['loan'] = 0;
		$loan = $player['record']['loan'];
		if($player['cash'] + $loan < 0)
		{
			$loan = -$player['cash'];
			$player['record']['loan'] = $loan;
		}
		if($loan < 0)
		{
			$player['record']['payback'] = -$loan;
			$player['record']['loan'] = 0;
			if($player['loan'] < -$loan)
			{	
				$loan = -$player['loan'];
			}	
		}
		$player['loan'] += $loan;
		$player['cash'] += $loan;
		
		$playerData['teams'][$kp] = $player;
	}
}
?>
