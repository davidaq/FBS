<?php
function fbpsales() {
	global $configData;
	global $playerData;
	$marketSize = floor($configData['Penetration'] * $configData["Market population"]);
	$addAgentCost = $configData['Adding agent cost'];
	$removeAgentCost = $configData['Removing agent cost'];	
	$reportCost = $configData['Market report cost per market quarter'];
	$consultantPrice = $configData['Consultant cost per 30 minutes'];
	$prdStrCost = $configData['Product storage cost per quarter'];
    $mc = count($configData['markets']);

	$maximumAutoLoan = $configData['Maximum auto loan'] * 1;
	foreach($playerData['teams'] as $kp => $player) {
		if($player['cash'] - $player['record']['salesSupport'] < -$maximumAutoLoan) {
	       $player['record']['salesSupport'] = $player['cash'] + $maximumAutoLoan;
	       if($player['record']['salesSupport'] < 0) {
	       	  $player['record']['salesSupport'] = 0;
	       }
        }
        $player['cash'] -= $player['record']['salesSupport'];
        // count agents before add & remove
        $total = 0;
        for($i = 0; $i < $mc; $i++) {
			if(!isset($player['marketAgents'][$i]))
				$player['marketAgents'][$i] = 0;
            $player['record']['agents_'.$i] = $player['record']['agentsBeforeDecision_'.$i] = $player['marketAgents'][$i];
			$player['record']['agentsChangeCost_'.$i] = 0;
			$total += $player['marketAgents'][$i];
            $player['record']["agentsChangeCost_$i"] = 0;
        }
        $player['record']['agentsBeforeDecision_total'] = $total;

        // add agents
        $addCount = 0;
        $budget = $player['cash'] + $maximumAutoLoan;
        if(isset($player['record']['saleAgentAdded'])) {
            foreach($player['record']['saleAgentAdded'] as $ks=>$i) {
                $budget -= $addAgentCost;
                if($budget < 0) {
                    $budget += $addAgentCost;
                    unset($player['record']['saleAgentAdded'][$ks]);
                } else {
                    $player['marketAgents'][$i]++;
                    $player['record']["agentsAdd_$i"] = 1;
                    $player['record']["agentsChangeCost_$i"] += $addAgentCost;
                    $player['record']["agents_$i"] = $player['marketAgents'][$i];
                    $addCount++;
                }
            }
        }
        $addCost = $addCount * $addAgentCost;
        // remove agents
        $removeCount = 0;
        if(isset($player['record']['saleAgentRemoved'])) {
            foreach($player['record']['saleAgentRemoved'] as $i) {
                $budget -= $removeAgentCost;
                if($budget < 0) {
                    $budget += $removeAgentCost;
                    unset($player['record']['saleAgentRemoved'][$ks]);
                } elseif($player['marketAgents'][$i] - ($player['homeMarket'] == $i ? 1 : 0) > 0) {
                    $player['marketAgents'][$i]--;
                    $player['record']["agentsRemove_$i"] = 1;
                    $player['record']["agentsChangeCost_$i"] += $removeAgentCost;
                    $player['record']["agents_$i"] = $player['marketAgents'][$i];
                    $removeCount++;
                }
            }
        }
		$removeCost = $removeCount * $removeAgentCost;
		$player['record']['agentsAdd_total'] = $addCount;
		$player['record']['agentsRemove_total'] = $removeCount;
		$player['record']['saleAgentsCost'] = $addCost + $removeCost;
		$player['record']['salesCost'] = $addCost + $removeCost + $player['record']['salesSupport'];
        $total += $addCount - $removeCount;
		$player['record']['agents_total'] = $total;
        $player['record']['salesSupportPerAgent'] = floor(100 * ($player['record']['adBonus'] + $player['record']['salesSupport']) / $total) / 100;
        for($i = 0; $i < $mc; $i++) {
            $player['record']["salesSupport_$i"] = $player['record']['salesSupportPerAgent'] * $player['marketAgents'][$i];
        }
		$player['cash'] -= $addCost + $removeCost;
        $playerData['teams'][$kp] = $player;
    }
    
    // generate initial factors
    $factors = array();
    $init = array();
	foreach($playerData['teams'] as $player) {
        $init[$player['name']] = 0;
    }
    for($i = 0; $i < $mc; $i++) {
        $factors[] = array(
            'price' => $init,
            'quality' => $init,
            'power' => $init,
            'share' => $init
        );
    }

	$threshold1 = $configData['priceThreshold1'] * 1;
	$threshold2 = $configData['priceThreshold2'] * 1;
	
	$marketPC = array();
	
    // calculate raw factors
	foreach($playerData['teams'] as $player) {
        $tname = $player['name'];
        for($i = 0; $i < $mc; $i++) {
        	if(!isset($factors[$i]['pif'])) {
        		$factors[$i]['pif'] = 0;
        	}
            if($player['marketAgents'][$i] > 0) {
            	if(!isset($marketPC[$i]))
            		$marketPC[$i] = 0;
            	$marketPC[$i]++;
            	if($player['record']['price'] > $threshold2) {
            		$t = 1;
            	} else {
            		$t = ($player['record']['price'] - $threshold1) / ($threshold2 - $threshold1);
            	}
            	if($factors[$i]['pif'] < $t) {
            		$factors[$i]['pif'] = $t;
            	}
            	$t = $threshold2 - $player['record']['price'];
                $factors[$i]['price'][$tname] = $t > 0 ? $t : 0;
                $factors[$i]['quality'][$tname] = $player['record']['qualityCostPerProduct'];
                $factors[$i]['power'][$tname] = $player['marketAgents'][$i] * $player['record']['salesSupportPerAgent'];
            }
        }
    }


    $priceInfluence = $configData['priceInfluence'] * 1;
    $qualityInfluence = $configData['qualityInfluence'] * 1;
    $salesPowerInfluence = $configData['salesPowerInfluence'] * 1;

    // fix factors
	foreach($playerData['teams'] as $player) {
        $tname = $player['name'];
        for($i = 0; $i < $mc; $i++) {
            $fc = &$factors[$i];
            
            if($player['record']['price'] > 0 && $player['record']['price'] < $threshold2 && $player['marketAgents'][$i] > 0) {
            } else {
            	$fc['price'][$tname] = 0;
            	$fc['quality'][$tname] = 0;
            	$fc['power'][$tname] = 0;
            }
        }
    }
    
    // normalize factors
    foreach($factors as $k=>$v) {
    	if(!isset($marketPC[$k]))
    		$marketPC[$k] = 0;
    	$factors[$k]['pif'] = $priceInfluence + (1 - $priceInfluence) * $factors[$k]['pif'];
    	$t = 1- $factors[$k]['pif'];
    	$factors[$k]['qif'] = $t * $qualityInfluence / ($qualityInfluence + $salesPowerInfluence);
    	$factors[$k]['sif'] = $t * $salesPowerInfluence / ($qualityInfluence + $salesPowerInfluence);
        $factors[$k]['price'] = normalize($v['price']);
        $factors[$k]['quality'] = normalize($v['quality']);
        $factors[$k]['power'] = normalize($v['power']);
        $sumPr = sum($factors[$k]['price']);
        $sumQu = sum($factors[$k]['quality']);
        $sumSp = sum($factors[$k]['power']);
    	foreach($playerData['teams'] as $player) {
    		$tname = $player['name'];
        	$factors[$k]['share'][$tname] = $factors[$k]['pif'] * (($sumPr > 0) ? ($factors[$k]['price'][$tname] / $sumPr) : 0)
													+ $factors[$k]['qif'] * (($sumQu > 0) ? ($factors[$k]['quality'][$tname] / $sumQu) : 0)
													+ $factors[$k]['sif'] * (($sumSp > 0) ? ($factors[$k]['power'][$tname] / $sumSp) : 0);
		}
    }
    $interestRate = $configData['Interest per quarter'] + 1;
	
    // calculate market share and do sales
    foreach($playerData['teams'] as $kp=>$player) {
        $tname = $player['name'];
        $totalOrder = 0;
        for($i = 0; $i < $mc; $i++) {
            if($player['marketAgents'][$i] > 0) {
            	$t = 1 / $marketPC[$i];
                $player['record']["marketShare_$i"] = $factors[$i]['share'][$tname];
                $order = floor($player['record']["marketShare_$i"] * $marketSize);
				$totalOrder += $order;
                $player['record']["marketOrder_$i"] = $order;
            } else {
                $player['record']["marketShare_$i"] = 0;
                $player['record']["marketOrder_$i"] = 0;
            }
        }
		$player['record']['totalReceivedOrders'] = $totalOrder;
		$stored = $player['record']['productsStored'];
		$satisfied = ($stored > $totalOrder) ? $totalOrder : $stored;
		$player['record']['totalSatisfiedOrders'] = $satisfied;
		$player['record']['totalUnsatisfiedOrders'] = ($satisfied < $totalOrder) ? ($totalOrder - $satisfied) : 0;
		$stored -= $satisfied;
		$player['record']['productsStored'] = $stored;
		$player['record']['productsStorageTotalCost'] = $stored * $prdStrCost;
		$player['record']['productsTotalCost'] = $player['record']['qualityCost'] * 1 + $player['record']['productsMaterialCost'] * 1 + $stored * $prdStrCost;
		$player['cash'] -= $stored * $prdStrCost;
		$profit = $satisfied * $player['record']['price'];
		$player['cash'] += $profit;
		$player['record']['salesProfit'] = $profit;	
        // marketing
		$orderedReport = 0;
		if(count($player['record']['marketsOrderedReport']) != 0)
		{
			$orderedReport = count($player['record']['marketsOrderedReport']);
		}
		$player['record']['marketReportOrdered'] = $orderedReport;
		$marketingCost = $reportCost * $orderedReport;
		$player['record']['marketReportCost'] = $marketingCost;
        $player['record']['consultantCost'] = 0;
		if($player['record']['hireConsultant']) 
		{
			$marketingCost += $consultantPrice;
			$player['record']['consultantCost'] = $consultantPrice;
		}
		$marketingCost += $player['record']['adBonusCost'];
		$player['cash'] -= $marketingCost;
		$player['record']['marketingCost'] = $marketingCost;

        $player['loan'] = ceil($interestRate * $player['loan']);

        $playerData['teams'][$kp] = $player;
    }
}

function normalize($array) {
    $max = max($array);
    $min = min($array);
    
    if($min > 0)
    	$min = 0;
    else
    	$min -= $max;
    
    foreach($array as $k=>$v) {
    	if($v != 0) {
		    if($max == $min)
		        $array[$k] = 1;
		    else
		        $array[$k] = ($v - $min) / ($max - $min);
		    for($i = 0; $i < 10; $i++) {
			    $array[$k] = (($array[$k] + 0.5) * ($array[$k] + 0.5) - 0.5 * 0.5) / 2;
		    }
        }
    }
    return $array;
}

function sum($array) {
    $ret = 0;
    foreach($array as $v) {
        $ret += $v;
    }
    return $ret;
}
