<?php
function fbpproduce()
{
	global $configData;
	global $playerData;
	$workersPerTeam = $configData['Workers in a component'];
	$engineersPerTeam = $configData['Engineers in a product'];
	$comMatCost = $configData['Material cost per component'];
	$comPerPrd = $configData['Components in a product'];
	$prdMatCost = $configData['Material cost per product'];
	$comStrCost = $configData['Component storage cost per quarter'];
	$hours = $configData['Month in a quarter'] * $configData['Working hours in a month'];
	$hoursInComp = $configData['Hours in a component'];
	$hoursInProd = $configData['Hours in a product'];
	
	// Maximum auto loan (presuming it to be a positive number)
	$maximumAutoLoan = $configData['Maximum auto loan'] * 1;
	foreach($playerData['teams'] as $k => $player) 
	{
	    $workers = $player['record']['workersHired'];
	    $engineers = $player['record']['engineersHired'];
	    $wp = floor($workers / $workersPerTeam);
	    $ep = floor($engineers / $engineersPerTeam);
	    $player['record']['workersWorking'] = $wp * $workersPerTeam;
	    $player['record']['workersIdle'] = $workers % $workersPerTeam;
	    $player['record']['engineersWorking'] = $ep * $engineersPerTeam;
	    $player['record']['engineersIdle'] = $engineers % $engineersPerTeam;
	    $components = $wp * floor($hours / $hoursInComp);
        // TODO add money constraint to components
        $cashAfterwards = $player['cash'] - $components * $comMatCost;
        if($cashAfterwards < - $maximumAutoLoan) {
            $components = 	floor( ($player['cash'] + $maximumAutoLoan) / $comMatCost);
            if($components < 0) {
                $components = 0;
            }
        }

        //	    
	    $player['record']['componentsProduced'] = $components;
	    $componentCost = $components * $comMatCost;
	    $player['record']['componentsMaterialCost'] = $componentCost;

        $player['cash'] -= $componentCost;	    

        $components += $player['record']['componentsStored'];
        $prdComCap = $ep * floor($hours / $hoursInProd);
        $prdMatCap = floor($components / $comPerPrd);
	    $order = $player['record']['productsOrdered'];
	    $products = ($prdComCap > $prdMatCap) ? $prdMatCap : $prdComCap;
	    $products = ($products > $order) ? $order : $products;
        // TODO add money constraint to production
        $cashAfterwards = $player['cash'] - $products * $prdMatCost;
        if($cashAfterwards < -$maximumAutoLoan) {
            $products = floor(($player['cash'] + $maximumAutoLoan) / $prdMatCost);
            if($products < 0) {
                $products = 0;
            }
        }
        //	    
	    $components -= $products * $comPerPrd;
	    $player['record']['componentsUsed'] = $products * $comPerPrd;
	    $player['record']['componentsStored'] = $components;
	    $compStorageCost = $components * $comStrCost;
	    $player['record']['componentsStorageTotalCost'] = $compStorageCost;
	    $player['record']['componentsTotalCost'] = $componentCost + $compStorageCost;
	    $player['record']['productsProduced'] = $products;
	    $productNum = $order + $configData['Quality storage factor'] * $player['record']['productsStored'];
	    $player['record']['productsStored'] = $products + $player['record']['productsStored'];	    
	    $prodMatCost = $products * $prdMatCost;
	    $player['cash'] -= $prodMatCost;
	    $player['record']['productsMaterialCost'] = $prodMatCost;
	    // TODO money check for $player['record']['qualityCost']
	    if($player['cash'] - $player['record']['qualityCost'] < -$maximumAutoLoan) {
	       $player['record']['qualityCost'] = $player['cash'] + $maximumAutoLoan;
	       if($player['record']['qualityCost'] < 0) {
	       	  $player['record']['qualityCost'] = 0;
	       }
	    }
	    
	    //
	    $qCost = $player['record']['qualityCost'];
	    $player['cash'] -= $qCost + $compStorageCost;
	    $player['record']['qualityCostPerProduct'] = $productNum > 0 ? $qCost/$productNum : 0;
		$playerData['teams'][$k] = $player;
	}
}		
?>
