<?php
include 'logic/entry.php';
if(!R('gid'))
    die();

$DB->query('select * from `%%game` where `id` = {$gid}');
$result = $DB->next();
if(!$result || $result['hostuser'] != USERID)
    die();

$DB->query('select `id`,`round`,`config`,`game` from `%%gamestatus` where `game`={$gid} order by `round` desc limit 1');
$res = $DB->next();
$result = endcore();
$res['rawdata'] = json_encode($result);

foreach($result['teams'] as $k=>$v) {
    unset($result['teams'][$k]['record']);
}
$res['data'] = json_encode($result);
$res['round']++;
$DB->query('insert into `%%gamestatus` (`game`,`data`,`round`,`config`,`rawdata`)values({$game},{$data},{$round},{$config},{$rawdata})', $res);
header('location: game.php?gid=' . R('gid'));
