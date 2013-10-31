<?php
if(!R('gid') || !R('team'))
    return redirect('index.php');

$DB->query('select * from `%%game` where `id` = {$gid}');
$result = $DB->next();
if(!$result || $result['hostuser'] != USERID)
    return redirect('index.php');

$DB->query('select * from `%%gamestatus` where game={$gid} order by `round` desc limit 1');
$status = $DB->next();

$status['rawdata'] = json_decode($status['rawdata'], true);

foreach($status['rawdata']['teams'] as $k=>$v) {
    if($v['name'] == R('team')) {
        if(!isset($v['record']['bonus']))
            $v['record']['bonus'] = 0;
        if(R('act') == 'get') {
            return array(
                'bonus' => $v['record']['bonus'],
                'cash' => $v['cash']
            );
        } elseif(R('act') == 'save') {
            $v['record']['bonus'] = R('bonus');
            $v['cash'] = R('cash');
            $status['rawdata']['teams'][$k] = $v;
            $status['rawdata'] = json_encode($status['rawdata']);
            $status['data'] = json_decode($status['data'], true);
            $status['data']['teams'][$k]['cash'] = $v['cash'];
            $status['data'] = json_encode($status['data']);
            $DB->query('update `%%gamestatus` set `data`={$data},`rawdata`={$rawdata},`needend`=1 where `id`={$id}', $status);
            return array('result'=>true);
        }
    }
}

