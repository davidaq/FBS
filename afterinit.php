<?php
include 'logic/entry.php';
if(!R('gid'))
    die();
$DB->query('select * from `%%game` where `id` = {$gid}');
$result = $DB->next();
if(!$result || $result['hostuser'] != USERID)
    die();

$DB->query('select `id` from `%%gamestatus` where `game`={$gid} order by `round` desc limit 1');
$res = $DB->next();
$res['data'] = json_encode(endcore());
$DB->query('update `%%gamestatus` set `data`={$data} where `id`={$id}', $res);
header('location: game.php?gid=' . R('gid'));
