<?php
function refreshTree($userid) {
    global $DB;
    $data['id'] = $userid;
    $DB->query('delete from `%%usertree` where `id` = {$id}', $data);
    while($userid != 0) {
        $DB->query('select `top` from `%%user` where id = {$id}', array('id'=>$userid));
        $r = $DB->next();
        $data['top'] = $r['top'];
        $DB->query('insert into `%%usertree` set `id` = {$id}, `top` = {$top}', $data);
        $userid = $r['top'];
    }
}
if(R('username')) {
    if($DB->query('insert into `%%user` (`username`,`email`,`top`,`password`)values({$username},"","'.USERID.'",md5("'.SEED.'123456"))'))
        $info = 'Created user <b>#' . $DB->insertId() . ' ' . R('username') . '</b>';
    else
        $info = 'Can not create user with username <b>' . R('username') . '</b>, maybe username already occupied';
    refreshTree($DB->insertId());
    $_SESSION['info'] = $info;
    return redirect(PAGE);
}
if(R('id')) {
    $DB->query('select `id`,`username`,`email`,`top` from `%%user` where `id`={$id}');
    $r = $DB->next();
    $DB->query('select `id` from `%%usertree` where `id`={$id} and `top`= ' . USERID);
    if($r && $DB->next()) {
        $info = "<b>#$r[id] $r[username] [$r[email]]</b> ";
        if(R('del')) {
            if($DB->query('delete from `%%user` where `id`={$id}')) {
                $DB->query('delete from `%%usertree` where `id`={$id}');
                $data['id'] = R('id');
                $data['top'] = $r['top'];
                $DB->query('update `%%usertree` set `top` = {$top} where `top`={$id}', $data);
                $DB->query('update `%%user` set `top` = {$top} where `top`={$id}', $data);
                $info .= 'deleted';
            }
        } else if(R('rst')) {
            if($DB->query('update `%%user` set `password` = md5("' . SEED . '123456") where `id`={$id}'))
                $info .= 'password reset to 123456';
        }
        $_SESSION['info'] = $info;
        return redirect(PAGE);
    }
}
$DB->query('select a.`id` id,a.`username` username,a.`email` email,b.`id` topid,b.`username` topname,b.`email` topemail from `%%user` a,`%%user` b where b.`id` = a.`top` and exists(select * from `%%usertree` c where `a`.`id` = `c`.`id` and `c`.`top` = ' . USERID . ') order by `id`');
$list = $DB->flush();

$DB->query('SELECT count(*) sum from `%%user` where `top`=' . USERID);
$count = $DB->next();
$count = $count['sum'];
return array(
    'list' => $list,
    'remain' => USERISADMIN ? 999 : 5 - $count
);
?>
