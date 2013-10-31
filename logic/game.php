<?php
if(!R('gid'))
    return redirect('index.php');
$DB->query('select * from `%%game` where `id` = {$gid}');
$result = $DB->next();
if(!$result)
    return redirect('index.php');

// Rewind Action
if(R('rewind') && $result['hostuser'] == USERID) {
    $DB->query('select count(*) `sum` from `%%gamestatus` where game={$gid}');
    $n = $DB->next();
    if(!$n || $n['sum'] < 2) {
        $_SESSION['info'] = 'There is no previous status to rewind to';
    } else {
        if($DB->query('delete from `%%gamestatus` where `game`={$gid} order by `round` desc limit 1'))
            $_SESSION['info'] = 'Status rewinded to previous round';
        else
            $_SESSION['info'] = $DB->lastSql();
    }
    return redirect(PAGE . '?gid=' . R('gid'));
}
// ]-

$DB->query('select * from `%%gamestatus` where game={$gid} order by `round` desc limit 1');
$status = $DB->next();
if(!$status)
    return redirect('index.php');
if($result['round'] != $status['round']) {
    $DB->query('update `%%game` set `round`={$round} where `id`={$game}', $status);
}
$dataRecord = json_decode($status['data'], true);
if(!$dataRecord) {
    callcore(R('gid'), 'init', 'afterinit.php?gid=' . R('gid'));
}
$teams = $dataRecord['teams'];

// Save Decision Action
if(R('decision') && $result['hostuser'] == USERID) {
    $record = R('decision');
    $name = $record['name'];
    unset($record['name']);
    foreach($teams as $k=>$f) {
        if($f['name'] == $name) {
            $teams[$k]['record'] = $record;
            $dataRecord['teams'] = $teams;
            $DB->query('update `%%gamestatus` set `data`={$data} where `id`={$sid}', array('data'=>json_encode($dataRecord),'sid'=>$status['id']));
            break;
        }
    }
    die('ok');
}
// ]-

$records = array();
foreach($teams as $k=>$f) {
    $teams[$k]['property'] = $f['cash'] - $f['loan'];
    if(isset($f['record']))
        $records[$f['name']] = $f['record'];
}
function cmp_team($b, $a) {
    $d = $a['property'] - $b['property'];
    if($d == 0)
        return 0;
    else
        return $d > 0 ? 1 : -1;
}
usort($teams, 'cmp_team');

$status['config'] = json_decode($status['config'], true);

$ret = array(
    'title' => $result['title'],
    'round' => $status['round'],
    'hostuser' => $result['hostuser'],
    'teams' => $teams,
    'records' => $records,
    'markets' => $status['config']['markets']
);
return $ret;
