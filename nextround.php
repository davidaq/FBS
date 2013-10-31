<?php
include 'logic/entry.php';
if(!R('gid'))
	die();
if(R('cango')) {
	$return['ok'] = false;
	$DB->query('select * from `%%game` where `id` = {$gid}');
	$result = $DB->next();
	if(!$result || $result['hostuser'] != USERID)
		$return['message'] = 'You are not allowed to run this simulation';
	else {
		$DB->query('select * from `%%gamestatus` where game={$gid} order by `round` desc limit 1');
		$status = $DB->next();
		if(!$status)
			$return['message'] = 'Simulation database corrupt';
		else {
			$players = json_decode($status['data'], true);
			$players = $players['teams'];
			$negative = array();
			foreach($players as $p) {
				if(!isset($p['record']) || !isset($p['record']['loan']) || $p['cash'] + $p['record']['loan'] < 0) {
					$negative[] = $p['name'];
				}
			}
			if($negative) {
				$return['message'] = 'These teams lack fund for next round: ' . implode(', ', $negative);
			} else {
				$return['ok'] = true;
			}
		}
	}
	die(json_encode($return));
}
callcore(R('gid'), 'startround;;loan;;hire;;produce;;sales', 'afterrun.php?gid=' . R('gid'));
