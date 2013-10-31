<?php
if(!R('gid'))
    return redirect('index.php');
$DB->query('select * from `%%game` where `id` = {$gid}');
$result = $DB->next();
if(!$result && $result['hostuser'] != USERID)
    return redirect('index.php');

$DB->query('select `id` from `%%gamestatus` where game={$gid} order by `round` desc limit 1');
$status = $DB->next();

if(!$status)
    return redirect('index.php');

$status['rawdata'] = json_encode(endcore());
$DB->query('update `%%gamestatus` set `rawdata`={$rawdata},`needend`=0 where `id`={$id}', $status);
return redirect('report.php?gid='.R('gid').'&team='.urlencode(R('team')));
