<?php
if(R('gid') && R('cfg')) {
    $ret = -1;
    $conf = json_encode(R('cfg'));
    if(R('gid') > 0) {
        $DB->query('select `hostuser` from `%%game` where `id`={$gid}');
        $result = $DB->next();
        if(!$result || $result['hostuser'] != USERID)
            die('-1');
        if($DB->query('update `%%game` set `title` = {$title} where `id` = {$gid}')) {
            $DB->query('select `id` from `%%gamestatus` where `game`={$gid} order by `round` desc limit 1');
            $data = $DB->next();
            $data['config'] = $conf;
            $DB->query('update `%%gamestatus` set `config` = {$config} where `id`={$id}', $data);
        }
        $ret = R('gid');
    } else {
        if($DB->query('insert into `%%game` (`hostuser`,`title`,`round`)values(' . USERID . ',{$title},0)')) {
            $ret = $DB->insertId();
            $data = array(
                'gid' => $ret,
                'config' => $conf
            );
            $DB->query('insert into `%%gamestatus` (`game`,`data`,`config`,`round`)values({$gid},"",{$config},0)', $data);
        }
    }
    die('' . $ret);
}
$data = array();
if(R('gid') > 0) {
    $DB->query('select * from `%%game` where `id`={$gid}');
    $result = $DB->next();
    $data['title'] = $result['title'];
    $DB->query('select * from `%%gamestatus` where `game`={$gid} order by `round` desc limit 1');
    $result = $DB->next();
    $data['cfg'] = $result['config'];
}
return array(
    'gid' => R('gid')?R('gid'):-1,
    'data' => $data
);
