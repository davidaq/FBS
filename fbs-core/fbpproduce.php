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
		
		//	    
	    $player['record']['componentsProduced'] = $components;
	    $componentCost = $components * $comMatCost;
	    $player['record']['componentsMaterialCost'] = $componentCost;
	    $components += $player['record']['componentsStored'];
	    $prdComCap = $ep * floor($hours / $hoursInProd);
	    $prdMatCap = floor($components / $comPerPrd);
	    $order = $player['record']['productsOrdered'];
	    $products = ($prdComCap > $prdMatCap) ? $prdMatCap : $prdComCap;
	    $products = ($products > $order) ? $order : $products;
		// TODO add money constraint to production
		
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
	    $player['record']['productsMaterialCost'] = $prodMatCost;
	    // TODO money check for $player['record']['qualityCost']
	    
	    //
	    $qCost = $player['record']['qualityCost'];
	    $player['cash'] -= $qCost + $componentCost + $compStorageCost + $prodMatCost;
	    $player['record']['qualityCostPerProduct'] = $productNum > 0 ? $qCost/$productNum : 0;
		$playerData['teams'][$k] = $player;
	}
}		
?>
