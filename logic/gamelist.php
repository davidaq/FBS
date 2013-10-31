<?php
if(R('end')) {
    $DB->query('delete from `%%game` where `id` = {$end}');
    $DB->query('delete from `%%gamestatus` where `game` = {$end}');
    $_SESSION['info'] = 'Simulation ended';
    return redirect(PAGE);
}
$DB->query('select `g`.*,`u`.username,`u`.email from `%%game` g,`%%user` u where g.`hostuser` = u.`id` AND (`hostuser` = ' . USERID . ' OR `hostuser` in (select `id` from `%%usertree` where `top` = ' . USERID . ')) order by `id` desc');
return $DB->flush();
