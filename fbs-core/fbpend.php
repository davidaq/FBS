<?php
	function fbpend()
	{
		global $configData;
		global $playerData;
   		$rank = array();
	    foreach($playerData['teams'] as $kp => $player)
		{
            $_budget = $player['cash'] - $player['loan'];
			if(!isset($rank[$_budget]))
			{
				$rank[$_budget] = array();
			}
            $rank[$_budget][] = $kp;
	        $player['record']['cashAvailableForNextRound'] = $player['cash'];
	        $player['record']['netCashflow'] = $player['cash'] - $player['record']['cashBeforeDecision'];
	        $player['record']['HRCost'] = $player['record']['workersTotalCost'] + $player['record']['engineersTotalCost'];
	        $player['record']['productionCost'] = $player['record']['componentsTotalCost'] + $player['record']['productsTotalCost'];
	        $player['record']['componentsStorageChange'] = $player['record']['componentsStored'] - $player['record']['componentsStorageBeforeDecision'];
	        $player['record']['productsStorageChange'] = $player['record']['productsStored'] - $player['record']['productsStorageBeforeDecision'];
	        $player['record']['storageTotalCost'] = $player['record']['componentsStorageTotalCost'] + $player['record']['productsStorageTotalCost'];
	        $player['record']['totalCashInflow'] = $player['record']['loan'] + $player['record']['salesProfit'] + $player['record']['bonus'];
	        $player['record']['totalCashOutflow'] = $player['record']['payback'] + $player['record']['HRCost'] + $player['record']['productionCost'] + $player['record']['marketingCost'] + $player['record']['salesCost'];
		    $playerData['teams'][$kp] = $player;
		}
	    $playerData['global']['componentsUnitStorageCost'] = $configData['Component storage cost per quarter'];
	    $playerData['global']['productsUnitStorageCost'] = $configData['Component storage cost per quarter'];
	    $playerData['global']['marketSize'] = floor($configData['Penetration'] * $configData['Market population']);
	    $ranking = 1;
	    krsort($rank);
	    foreach($rank as $list)
		{
            $cranking = $ranking;
	        foreach($list as $playerKey) 
			{
	            $playerData['teams'][$playerKey]['record']['ranking'] = $cranking;
	            $ranking++;
	        }
	    }
	}

?>
