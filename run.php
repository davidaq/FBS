<?php
if(isset($_SESSION['_run']))
    die('busy');
$_SESSION['_run'] = 1;
include 'logic/entry.php';
if(!R('gid'))
    die();
$DB->query('select * from `%%game` where `id` = {$gid}');
$result = $DB->next();
if(!$result || $result['hostuser'] != USERID)
    die();
$DB->query('select * from `%%gamestatus` where game={$gid} order by `round` desc limit 1');
$status = $DB->next();
if(!$status)
    die();
if($_SESSION['_runRaw'])
    $_SESSION['_runPlayers'] = json_decode($status['rawdata'], true);
else
    $_SESSION['_runPlayers'] = json_decode($status['data'], true);
$_SESSION['_runConfig'] = json_decode($status['config'], true);
header('location: fbs-core/_run.php');
