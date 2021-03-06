<?php
function fbphire()
{
	global $configData;
	global $playerData;


	$workersAverageSalary = 0;
	$count = 0;
	$workersSalaryMin = hexdec('7fffffff');
	$workersSalaryMax = 0;
	
	// Maximum auto loan (presuming it to be a positive number)
	$maximumAutoLoan = $configData['Maximum auto loan'] * 1;
	foreach($playerData['teams'] as $k => $player)
	{

		$workersOrdered = $player['record']['workersOrdered'];
		$workersSalary = $player['record']['workersSalary'] * 3;
		$workersSalaryMax = max($workersSalary, $workersSalaryMax);
		$workersSalaryMin = min($workersSalary, $workersSalaryMin);
		// TODO calculate if money is enough for workers
		//	if not reduce $player['record']['workersOrdered'] directly
		$cashAfterwards = $player['cash'] - $workersOrdered * $workersSalary;
		if($cashAfterwards < -$maximumAutoLoan)
		{
				$workersOrdered = floor(($player['cash'] + $maximumAutoLoan) / $workersSalary);
				// floor is to make sure that $workersOrdered is an integer which guarentee $cashAfterwards >= -$maximumAutoLoan.
				// And...just in case $workersOrdered is smaller than zero? (In the case someone stupid set $maximumAutoLoan to negative
				// or maybe $player['cash'] happened to be negative not as promised)
				if($workersOrdered < 0)
				{
					$workersOrdered = 0;
				}
				// Assign it back.
				$player['record']['workersOrdered'] = $workersOrdered;
		}				
		$player['record']['workersTotalCost'] = $workersOrdered * $workersSalary;		
		$player['cash'] -= $workersOrdered * $workersSalary;
		$count += $workersOrdered;
		if($count > 0)
			$workersAverageSalary += floor(($workersSalary - $workersAverageSalary) * $workersOrdered / $count);
        $playerData['teams'][$k] = $player;
	}
    $workersSalaryMax = floor($workersSalaryMax / 3);
    $workersSalaryMin = floor($workersSalaryMin / 3);
    $workersAverageSalary = floor($workersAverageSalary / 3);
	$playerData['global']['workersAverageSalary'] = $workersAverageSalary;
    if($workersSalaryMax == $workersSalaryMin)
    {
        $workersAverageSalaryD = 0.5;
    }
    else
    {
        $workersAverageSalaryD = $workersAverageSalary / $workersSalaryMax;
    }

    foreach($playerData['teams'] as $k => $player)
    {
        $workersOrdered = $player['record']['workersOrdered'];
        $workersSalary = $player['record']['workersSalary'];

        if($workersSalaryMax == $workersSalaryMin)
        {
            $workersSalaryD = 0.5;
        }
        else
        {
            $workersSalaryD = $workersSalary / $workersAverageSalary;
        }
        $workersHired = $workersAverageSalary == 0 ? 0 : ($workersSalary >= $workersAverageSalary ?
                    $workersOrdered :  $workersOrdered * $workersSalaryD);
        $workersHired = floor($workersHired);
		$player['record']['workersHired'] = $workersHired;
        $player['record']['workersLeft'] = $workersOrdered - $workersHired;
        $player['record']["workersAvailable"] = $workersHired;
        $playerData['teams'][$k] = $player;
    }	

    $engineersAverageSalary = 0; 
	$count = 0;
    $engineersSalaryMin = hexdec('7fffffff');
    $engineersSalaryMax = 0;
    foreach($playerData['teams'] as $k => $player)
    {
		        $engineersOrdered = $player['record']['engineersOrdered'];
        $engineersSalary = $player['record']['engineersSalary'] * 3;
        $engineersSalaryMax = max($engineersSalaryMax, $engineersSalary);
        $engineersSalaryMin = min($engineersSalaryMin, $engineersSalary);
        // TODO calculate if money is enough for engineers
		//	if not reduce $player['record']['engineersOrdered'] directly
		
		// This part parallels the workers counterpart.
		$cashAfterwards = $player['cash'] - $engineersOrdered * $engineersSalary;
		if($cashAfterwards < -$maximumAutoLoan)
		{
				$engineersOrdered = floor(($player['cash'] + $maximumAutoLoan) / $engineersSalary);
				if($engineersOrdered < 0)
				{
					$engineersOrdered = 0;
				}
				$player['record']['engineersOrdered'] = $engineersOrdered;
		}
		// ---------------

        $player['record']['engineersTotalCost'] = $engineersOrdered * $engineersSalary;        
        
        
        
        $player['cash'] -= $engineersOrdered * $engineersSalary;
        $count += $engineersOrdered;
        if($count > 0)
		{
            $engineersAverageSalary += ($engineersSalary - $engineersAverageSalary) * $engineersOrdered / $count;
		}
        $playerData['teams'][$k] = $player;
    }
    $engineersSalaryMin = floor($engineersSalaryMin / 3);
    $engineersSalaryMax = floor($engineersSalaryMax / 3);
    $engineersAverageSalary = floor($engineersAverageSalary / 3);
    $playerData['global']['engineersAverageSalary'] = $engineersAverageSalary;

    if($engineersSalaryMax == $engineersSalaryMin)
    {
        $engineersAverageSalaryD = 0.5;
    }
    else
    {
        $engineersAverageSalaryD = $engineersAverageSalary / $engineersSalaryMax;
    }
    foreach($playerData['teams'] as $k => $player)
    {
        $engineersOrdered = $player['record']['engineersOrdered'];
        $engineersSalary = $player['record']['engineersSalary'];
        
        if($engineersSalaryMax == $engineersSalaryMin)
        {
            $engineersSalaryD = 0.5;
        }
        else
        {
            $engineersSalaryD = $engineersSalary / $engineersAverageSalary;
        }
        $engineersHired = $engineersAverageSalary == 0 ? 0 : ($engineersSalary >= $engineersAverageSalary ?
                                                                $engineersOrdered : $engineersOrdered * $engineersSalaryD);
        $engineersHired = floor($engineersHired);
        $player['record']['engineersHired'] = $engineersHired;
        $player['record']['engineersLeft'] = $engineersOrdered - $engineersHired;
        $player['record']['engineersAvailable'] = $engineersHired;
        $playerData['teams'][$k] = $player;
    }
}


?>
