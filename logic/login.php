<?php
if(isset($_SESSION[S_LOGIN]))
    unset($_SESSION[S_LOGIN]);
if(R('submit') && R('login')) {
    $DB->query('SELECT `id`,`password` FROM `%%user` where `username` = {$login.username}');
    RS_('login.remember');
    RS_('login.username');
    RS_('login.password');
    if($res = $DB->next()) {
        if(md5(SEED . R('login.password')) == $res['password']) {
            $_SESSION[S_LOGIN] = $res['id'];
            if(R('login.remember')) {
                RS('login.remember');
                RS('login.username');
                RS('login.password');
            }
            return redirect('index.php');
        } else
            return 'password';
    }
    return 'username';
}
