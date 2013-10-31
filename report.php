<?php
include 'logic/entry.php';
function getVal($key) {
    global $result;
    if(isset($result[$key]))
        return $result[$key];
    elseif(isset($result['config'][$key]))
        return $result['config'][$key];
    return 0;
}
$mc = getVal('marketcount');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf8"/>
		<title>Team: {:getVal('team_name')} Round: {:getVal('round')}</title>
		<style type="text/css">
		body {
			font-family: arial;
			font-size: 1.1em;
			margin: 0 auto;
			width: 1000px;
		}
		table td {
			border: 1px solid #000;
			border-top: 0;
			border-right: 0;
			text-align: right;
			vertical-align: top;
			padding: 5px;
		}
		table {
			border: 1px solid #000;
			border-bottom: 0;
			border-left: 0;
			width: 100%;
			margin: 10px auto;
		}
		.pad {
			border: 0;
		}
		.head td {
			background: #E9DECF;
			font-weight: bold;
			text-align: center;
		}
		.hl {
			background: #E9DECF;
		}
		h2 {
			margin-top: 40px;
		}
		</style>
		<script type="text/javascript" src="static/js/jquery.min.js"></script>
		<script type="text/javascript">
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
		$(function() {
			$('td').each(function() {
                var item = $(this);
                if(item[0].hasAttribute('filter') && item.html() == item.attr('filter')) {
                    item.closest('tr').hide();
                }
                if(item[0].hasAttribute('percent')) {
                    item.html((Math.ceil(item.html() * 10000) / 100) + '%');
                } else {
                    var val = item.html();
                    item.html(numberWithCommas(val));
                }
            });
		});
		</script>
	</head>
	<body>



<h1>Team: {:getVal('team_name')} &nbsp;&nbsp;&nbsp;&nbsp; Round: {:getVal('round')}</h1>
<h2>Key Metrics</h2>
<table border=0 cellspacing=0>
	<tr class="head">
		<td>Cash before decision</td>
		<td>Net Cashflow (Profit/Loss)</td>
		<td>Cash for next round</td>
		<td>Debt</td>
		<td>Ranking</td>
	</tr>
	<tr>
		<td>{:getVal('cashBeforeDecision')}</td>
		<td>{:getVal('netCashflow')}</td>
		<td>{:getVal('cashAvailableForNextRound')}</td>
		<td>{:getVal('debt')}</td>
		<td>{:getVal('ranking')}</td>
	</tr>
</table>
<h2>Finance</h2>
<table border=0 cellspacing=0>
	<tr class="head">
		<td>Cash Outflow</td>
		<td>RMB</td>
		<td>Cash Inflow</td>
		<td>RMB</td>
	</tr>
	<tr>
		<td>HR cost</td>
		<td>{:getVal('HRCost')}</td>
		<td>Sales revenue</td>
		<td>{:getVal('salesProfit')}</td>
	</tr>
	<tr>
		<td>Production cost</td>
		<td>{:getVal('productionCost')}</td>
		<td>Project bonus revenue</td>
		<td>{:getVal('bonus')}</td>
	</tr>
	<tr>
		<td>Sales cost</td>
		<td>{:getVal('salesCost')}</td>
		<td rowspan="2" colspan="2"></td>
	</tr>
	<tr>
		<td>Marketing cost</td>
		<td>{:getVal('marketingCost')}</td>
	</tr>
	<tr>
		<td>Loan payback</td>
		<td>{:getVal('payback')}</td>
		<td>Loan</td>
		<td>{:getVal('loan')}</td>
	</tr>
	<tr class="hl">
		<td>Total cash outflow</td>
		<td>{:getVal('totalCashOutflow')}</td>
		<td>Total cash inflow</td>
		<td>{:getVal('totalCashInflow')}</td>
	</tr>
</table>
<h2>HR (Human Resources)</h2>
<table border=0 cellspacing=0>
	<tr class="head">
		<td>Employees</td>
		<td>ordered</td>
		<td>hired</td>
		<td>left</td>
		<td>available</td>
		<td>working</td>
		<td>idle</td>
		<td>average salary</td>
		<td>salary</td>
		<td>total cost</td>
	</tr>
	<tr>
		<td>workers</td>
		<td>{:getVal('workersOrdered')}</td>
		<td>{:getVal('workersHired')}</td>
		<td>{:getVal('workersLeft')}</td>
		<td>{:getVal('workersAvailable')}</td>
		<td>{:getVal('workersWorking')}</td>
		<td>{:getVal('workersIdle')}</td>
		<td>{:getVal('workersAverageSalary')}</td>
		<td>{:getVal('workersSalary')}</td>
		<td>{:getVal('workersTotalCost')}</td>
	</tr>
	<tr>
		<td>engineers</td>
		<td>{:getVal('engineersOrdered')}</td>
		<td>{:getVal('engineersHired')}</td>
		<td>{:getVal('engineersLeft')}</td>
		<td>{:getVal('engineersAvailable')}</td>
		<td>{:getVal('engineersWorking')}</td>
		<td>{:getVal('engineersIdle')}</td>
		<td>{:getVal('engineersAverageSalary')}</td>
		<td>{:getVal('engineersSalary')}</td>
		<td>{:getVal('engineersTotalCost')}</td>
	</tr>
	<tr>
		<td class="pad" colspan="9"></td>
		<td class="hl">{:getVal('HRCost')}</td>
	</tr>
</table>
<h2>Production</h2>
<table border=0 cellspacing=0>
	<tr class="head">
		<td>Production</td>
		<td>ordered</td>
		<td>produced</td>
		<td>used</td>
		<td>stored</td>
		<td>material cost</td>
		<td>total cost</td>
	</tr>
	<tr>
		<td>components</td>
		<td></td>
		<td>{:getVal('componentsProduced')}</td>
		<td>{:getVal('componentsUsed')}</td>
		<td>{:getVal('componentsStored')}</td>
		<td>{:getVal('componentsMaterialCost')}</td>
		<td>{:getVal('componentsTotalCost')}</td>
	</tr>
	<tr>
		<td>products</td>
		<td>{:getVal('productsOrdered')}</td>
		<td>{:getVal('productsProduced')}</td>
		<td>{:getVal('totalSatisfiedOrders')}</td>
		<td>{:getVal('productsStored')}</td>
		<td>{:getVal('productsMaterialCost')}</td>
		<td>{:getVal('productsTotalCost')}</td>
	</tr>
	<tr>
		<td class="pad" colspan="6"></td>
		<td class="hl">{:getVal('productionCost')}</td>
	</tr>
</table>
<h3>Storage</h3>
<table cellspacing="0" border="0">
	<tr class="head">
		<td>Warehouse</td>
		<td>before decision</td>
		<td>(+ / -)</td>
		<td>total items</td>
		<td>storage cost/unit</td>
		<td>total cost</td>
	</tr>
	<tr>
		<td>components</td>
		<td>{:getVal('componentsStorageBeforeDecision')}</td>
		<td>{:getVal('componentsStorageChange')}</td>
		<td>{:getVal('componentsStored')}</td>
		<td>{:getVal('componentsUnitStorageCost')}</td>
		<td>{:getVal('componentsStorageTotalCost')}</td>
	</tr>
	<tr>
		<td>products</td>
		<td>{:getVal('productsStorageBeforeDecision')}</td>
		<td>{:getVal('productsStorageChange')}</td>
		<td>{:getVal('productsStored')}</td>
		<td>{:getVal('productsUnitStorageCost')}</td>
		<td>{:getVal('productsStorageTotalCost')}</td>
	</tr>
	<tr>
		<td class="pad" colspan="5"></td>
		<td class="hl">{:getVal('storageTotalCost')}</td>
	</tr>
</table>
<h3>Quality &amp; R&amp;D</h3>
<table cellspacing="0" border="0">
	<tr class="head">
		<td>ordered products</td>
		<td>quality cost per product</td>
		<td>R&amp;D/quality cost</td>
	</tr>
	<tr>
		<td>{:getVal('productsOrdered')}</td>
		<td>{:getVal('qualityCostPerProduct')}</td>
		<td class="hl">{:getVal('qualityCost')}</td>
	</tr>
</table>
<h2>Sales</h2>
<table cellspacing="0" border="0">
	<tr class="head">
		<td>Market</td>
		<td>market share</td>
		<td>total orders</td>
		<td>received ordered</td>
	</tr>
	<?php for($i = 0; $i < $mc; $i++) {if(getVal('agents_'.$i)==0)continue;?>
	<tr>
		<td><?php echo $result['config']['markets'][$i]; ?></td>
		<td percent><?php echo getVal('marketShare_'.$i); ?></td>
		<td><?php echo getVal('marketSize'); ?></td>
		<td><?php echo getVal('marketOrder_'.$i); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td class="pad" colspan="3"></td>
		<td class="hl">{:getVal('totalReceivedOrders')}</td>
	</tr>
</table>
<h3>Revenue</h3>
<table cellspacing="0" border="0">
	<tr class="head">
		<td>received orders</td>
		<td>satisfied orders</td>
		<td>unsatisfied orders</td>
		<td>price</td>
		<td>sales revenue</td>
	</tr>
	<tr>
		<td>{:getVal('totalReceivedOrders')}</td>
		<td>{:getVal('totalSatisfiedOrders')}</td>
		<td>{:getVal('totalUnsatisfiedOrders')}</td>
		<td>{:getVal('price')}</td>
		<td class="hl">{:getVal('salesProfit')}</td>
	</tr>
</table>
<h3>Sales Agent Cost</h3>
<table cellspacing="0" border="0">
	<tr class="head">
		<td>Market</td>
		<td>before decision</td>
		<td>( + )</td>
		<td>( - )</td>
		<td>total sales agent</td>
		<td>add / remove agent cost</td>
	</tr>
	<?php for($i = 0; $i < $mc; $i++) { ?>
	<tr>
		<td><?php echo $result['config']['markets'][$i]; ?></td>
		<td><?php echo getVal('agentsBeforeDecision_'.$i); ?></td>
		<td><?php echo getVal('agentsAdd_'.$i); ?></td>
        <td><?php echo getVal('agentsRemove_'.$i); ?></td>
		<td><?php echo getVal('agents_'.$i); ?></td>
		<td><?php echo getVal('agentsChangeCost_'.$i); ?></td>
	</tr>
	<?php } ?>
	<tr class="hl">
		<td>total</td>
		<td>{:getVal('agentsBeforeDecision_total')}</td>
		<td>{:getVal('agentsAdd_total')}</td>
		<td>{:getVal('agentsRemove_total')}</td>
		<td>{:getVal('agents_total')}</td>
		<td>{:getVal('saleAgentsCost')}</td>
	</tr>
</table>
<h3>Sale Agent Support / Power</h3>
<table cellspacing="0" border="0">
	<tr class="head">
		<td>Market</td>
		<td>total sales agents</td>
		<td>financial support / agent</td>
		<td>total cost</td>
	</tr>
	<?php for($i = 0; $i < $mc; $i++) { ?>
	<tr>
		<td><?php echo $result['config']['markets'][$i]; ?></td>
		<td filter="0"><?php echo getVal('agents_'.$i); ?></td>
		<td><?php echo getVal('salesSupportPerAgent'); ?></td>
        <td><?php echo getVal('salesSupport_'.$i); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td class="pad" colspan="3"></td>
		<td class="hl">{:getVal('salesSupport')}</td>
	</tr>
</table>
<h2>Marketing</h2>
<table cellspacing="0" border="0">
	<tr class="head">
		<td>market reports ordered</td>
		<td>market report price</td>
		<td>market report cost</td>
		<td>consultant cost</td>
		<td>total</td>
	</tr>
	<tr>
		<td>{:getVal('marketReportOrdered')}</td>
		<td>{:getVal('Market report cost per market quarter')}</td>
		<td>{:getVal('marketReportCost')}</td>
		<td>{:getVal('consultantCost')}</td>
		<td class="hl">{:getVal('marketingCost')}</td>
	</tr>
</table>
<?php
    $orders = getVal('Market population') * getVal('Penetration');
    function getMarketData($val, $market, $player = false) {
        global $result;
        if($player)
            $pk = $player . '_' . $market;
        else
            $pk = $market;
        return $result['marketData'][$val][$pk];
    }
?>
<?php if(isset($result['marketsOrderedReport']))foreach(getVal('marketsOrderedReport') as $i) { ?>
<br/>
<h1>Market Report: <?php echo $result['config']['markets'][$i]; ?></h1>
<table border=0 cellspacing=0>
	<tr class="head">
		<td>Market Population</td>
		<td>Market Penetration</td>
		<td>Total available orders</td>
		<td>Total sales Agents</td>
		<td>Total Players</td>
	</tr>
	<tr>
		<td>{:getVal('Market population')}</td>
		<td percent>{:getVal('Penetration')}</td>
		<td><?php echo $orders; ?></td>
		<td><?php echo getMarketData('agents', $i); ?></td>
		<td><?php echo count(getMarketData('players', $i)); ?></td>
	</tr>
</table>

<table border=0 cellspacing=0>
	<tr class="head">
		<td>Team</td>
		<td>Agents</td>
		<td>Price</td>
		<td>Marketshare</td>
		<td>Orders</td>
	</tr>
    <?php foreach(getMarketData('players', $i) as $p) { ?>
	<tr>
		<td filter="0"><?php echo $p; ?></td>
		<td><?php echo getMarketData('agents', $i, $p); ?></td>
		<td><?php echo getMarketData('price', $p); ?></td>
		<td percent><?php echo getMarketData('share', $i, $p); ?></td>
		<td><?php echo getMarketData('orders', $i, $p); ?></td>
	</tr>
    <?php } ?>
</table>
<?php } ?>


	</body>
</html>
