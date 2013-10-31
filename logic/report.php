<?php
if(!R('gid'))
    return redirect('index.php');
$DB->query('select * from `%%game` where `id` = {$gid}');
$result = $DB->next();
if(!$result)
    return redirect('index.php');

$DB->query('select * from `%%gamestatus` where game={$gid} order by `round` desc limit 1');
$status = $DB->next();
if($status['needend'])
    callcore(R('gid'), 'end', 'endfbpend.php?gid='.R('gid').'&team='.urlencode(R('team')), true);

$status['rawdata'] = json_decode($status['rawdata'], true);
$t = $status['rawdata']['teams'];
$tname = base64_decode(R('team'));
$status['config'] = json_decode($status['config'], true);
$mc = count($status['config']['markets']);
$marketData = array(
    'agents' => array(),
    'share' => array(),
    'orders' => array(),
    'players' => array(),
    'price' => array()
);
foreach($t as $v) {
    if($v['name'] == $tname) {
        $ret = $v['record'];
        $ret = array_merge($v['record'], $status['rawdata']['global']);
        $ret['debt'] = $v['loan'];
        $ret['marketcount'] = $mc;
        $ret['config'] = $status['config'];
    }
    for($i = 0; $i < $mc; $i++) {
        if(isset($v['record']["agents_$i"]) && $v['record']["agents_$i"] > 0) {
            $pk = $v['name'] . '_' . $i;
            $marketData['agents'][$pk] = $v['record']["agents_$i"];
            if(!isset($marketData['agents'][$i])) 
                $marketData['agents'][$i] = 0;
            $marketData['agents'][$i] += $v['record']["agents_$i"];
            if(!isset($marketData['players'][$i])) 
                $marketData['players'][$i] = array();
            $marketData['players'][$i][] = $v['name'];
            $marketData['share'][$pk] = $v['record']["marketShare_$i"];
            $marketData['orders'][$pk] = $v['record']["marketOrder_$i"];
        }
    }
    $marketData['price'][$v['name']] = $v['record']['price'];
}
$ret['marketData'] = $marketData;
$ret['team_name'] = $tname;
return $ret;
