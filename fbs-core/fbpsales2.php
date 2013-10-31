<?php

class PlayerFactor
{
	public $price;
	public $power;
	public $quality;
}


class MarketStatus {
    public $_minPriceFac;
	public $_maxPriceFac;
    public $_minQualityFac;
	public $_maxQualityFac;
    public $_minPowerFac;
	public $_maxPowerFac;
    public $_total;
	public function __construct()
	{
		$this->_minPriceFac = 0;
		$this->_maxPriceFac = 0;
		$this->_minQualityFac = 0;
		$this->_maxQualityFac = 0;
		$this->_minPowerFac = 0;
		$this->_maxPowerFac = 0;
		$this->_total = 0;
	}

    public function apply($factor) 
	{
        $this->_total += $factor;
    }
    public function normalize(&$factor) 
	{
        if($this->_total != 0)
		{
            $factor /= $this->_total;
		}
        else
		{
            $factor = 0;
		}
    }
    public function applyPlayerFactor(PlayerFactor $factor) 
	{
        $this->_apply($factor->price, $this->_minPriceFac, $this->_maxPriceFac);
        $this->_apply($factor->quality, $this->_minQualityFac, $this->_maxQualityFac);
        $this->_apply($factor->power, $this->_minPowerFac, $this->_maxPowerFac);
    }
    public function normalizePlayerFactor(PlayerFactor $factor) 
	{
        $this->_normalize($factor->price, $this->_minPriceFac, $this->_maxPriceFac);
        $this->_normalize($factor->quality, $this->_minQualityFac, $this->_maxQualityFac);
        $this->_normalize($factor->power, $this->_minPowerFac, $this->_maxPowerFac);
    }

    private static function _apply($val, &$min,  &$max) 
	{
        if($val > $max) 
		{
           	$max = $val;
        }
		elseif($val < $min) 
		{
            $min = $val;
        }
    }
    private static function _normalize(&$val, $min, $max) 
	{
        if($max != $min)
		{
            $val =  ($val - $min) / ($max - $min);
		}
        else
		{
            $val = 1;
		}
    }
}
function pair($a, $b)
{
	return $a * hexdec('10000') + $b;
}
function fbpsales()
{
	global $configData;
	global $playerData;
	$marketSize = floor($configData['Penetration'] * $configData["Market population"]);
	$addAgentCost = $configData['Adding agent cost'];
	$removeAgentCost = $configData['Removing agent cost'];	
	$marketStatus = array();
	$factors = array();	
	$playerIndex = -1;
	foreach($playerData['teams'] as $kp => $player) {
		$playerIndex++;
		// Add / Remove agent
		$addCost = 0;
		$removeCost = 0;
		$addCount = 0;
		$removeCount = 0;
		$total = 0;
		for($i = 0, $sz = count($playerData['teams']); $i < $sz; $i++) 
		{
			$s = "$i";
			if(!isset($player['marketAgents'][$i]))
				$player['marketAgents'][$i] = 0;
			$player['record']['agents_'.$s] = $player['record']['agentsBeforeDecision_'.$s] = $player['marketAgents'][$i];
			$player['record']['agentsChangeCost_'.$s] = 0;
			$total += $player['marketAgents'][$i];
		}
		$player['record']['agentsBeforeDecision_total'] = $total;
		if(isset($player['record']['saleAgentAdded']))
		{
			foreach($player['record']['saleAgentAdded'] as $s) 
			{
				$marketid = $s;
				if(array_key_exists($marketid, $player['marketAgents']))
				{
					$player['marketAgents'][$marketid] += 1;
				}
				else
				{
					$player['marketAgents'][$marketid] = 1;
				}
				$player['record']['agentsAdd_'."$s"] = 1;
				$player['record']['agentsChangeCost_'."$s"] = $addAgentCost;
				$player['record']['agents_'."$s"] = $player['marketAgents'][$marketid];
				$addCount++;
			}
		}
		$addCost = $addCount * $addAgentCost;
        if(isset($player['record']['saleAgentRemoved']))
        {
            foreach($player['record']['saleAgentRemoved'] as $s) 
            {
                $marketid = $s;
                if($player['homeMarket'] == $marketid && $player['marketAgents'][$marketid] == 1) 
                {
                    continue;
                }
                if(array_key_exists($marketid, $player['marketAgents'])) 
                {
                    $player['marketAgents'][$marketid] -= 1;
                    $player['record']['agentsRemove_'."$s"] = 1;
                    $player['record']['agentsChangeCost_'."$s"] = $player['record']['agentsChangeCost_'."$s"] + $removeAgentCost;
                    $player['record']['agents_'."$s"] = $player['marketAgents'][$marketid];
                    $removeCount++;
                }
            }
        }
		$removeCost = $removeCount * $removeAgentCost;
		$player['record']['agentsAdd_total'] = $addCount;
		$player['record']['agentsRemove_total'] = $removeCount;
		$player['record']['saleAgentsCost'] = $addCost + $removeCost;
		$player['record']['agents_total'] = $total - $removeCount + $addCount;
		$totalAgent = 0;
		foreach($player['marketAgents'] as $n)
		{
			$totalAgent += $n;
		}
		$factor = new PlayerFactor();
		$salesSupport = $player['record']['salesSupport'];
		$player['record']['salesCost'] = $addCost + $removeCost + $salesSupport;
		$player['cash'] -= $addCost + $removeCost + $salesSupport;
		$singlePower = ($totalAgent > 0) ? $salesSupport / $totalAgent : 0;
		$player['record']['salesSupportPerAgent'] = $singlePower;
		$factor->price = -$player['record']['price'];
		$factor->quality = $player['record']['qualityCostPerProduct'];
		foreach($player['marketAgents'] as $k => $n)
		{
			if($n > 0)
			{
				$factor2 = clone $factor;
				$factor2->power = $singlePower * $n;
				$player['record']["salesSupport_"."$k"] = $singlePower * $n;
				$marketStatus[$k] = new MarketStatus();
				$marketStatus[$k]->applyPlayerFactor($factor2);
				$factors[pair($playerIndex, $k)] = $factor2;
			}
		}
		$playerData['teams'][$kp] = $player;
	}
	$totalFactors = array();
    $totalFactorsMin = 100;
    $totalFactorsMax = 0;
	$playerIndex = -1;
	foreach($playerData['teams'] as $kp => $player)
	{
		$playerIndex++;
		foreach($player['marketAgents'] as $k => $n)
		{
			if($n > 0)
			{
				$key = pair($playerIndex, $k);
				$factor = $factors[$key];
				$marketStatus[$k]->normalizePlayerFactor($factor);
				$total = $factor->power * 0.6 + $factor->price * 0.2 + $factor->quality * 0.2;
				$marketStatus[$k]->apply($total);
				$totalFactors[$key] = $total;
                if($total > $totalFactorsMax)
                    $totalFactorsMax = $total;
                if($total < $totalFactorsMin)
                    $totalFactorsMin = $total;
			}
		}
		$playerData['teams'][$kp] = $player;
	}
	$factors = array();
	$playerIndex = -1;
	$reportCost = $configData['Market report cost per market quarter'];
	$consultantPrice = $configData['Consultant cost per 30 minutes'];
	$prdStrCost = $configData['Product storage cost per quarter'];
	$playerData['global']['marketReportPrice'] = $reportCost;
	foreach($playerData['teams'] as $kp => $player)
	{
		$playerIndex++;
		$totalOrder = 0;
		$price = $player['record']['price'];
		foreach($player['marketAgents'] as $k => $n)
		{
			if($n > 0)
			{
				$key = pair($playerIndex, $k);
				if($totalFactorsMax - $totalFactorsMin == 0)
					$factor = 1;
				else
					$factor = ($totalFactors[$key] - $totalFactorsMin) / ($totalFactorsMax - $totalFactorsMin);
				//$marketStatus[$k]->normalizePlayerFactor($factor);
				$totalFactors[$key] = $factor;
				$player['record']['marketShare_'.$k] = $factor;
				$order = $factor * $marketSize;
				$player['record']['marketOrder_'.$k] = $order;
				$totalOrder += $order;
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
		$player['record']['productsTotalCost'] = $player['record']['qualityCost'] + $player['record']['productsMaterialCost'] + $stored * $prdStrCost;
		$player['cash'] -= $stored * $prdStrCost;
		$profit = $satisfied * $price;
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
		if($player['record']['hireConsultant']) 
		{
			$marketingCost += $consultantPrice;
			$player['record']['consultantCost'] = $consultantPrice;
		}
		$player['cash'] -= $marketingCost;
		$player['record']['marketingCost'] = $marketingCost;
		$playerData['teams'][$kp] = $player;
	}
}
?>
