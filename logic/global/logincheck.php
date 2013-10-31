<?php
include_once 'logic/global/db.php';
if(PAGE != 'login.php' && PAGE != 'report.php') {
    if(isset($_SESSION[S_LOGIN])) {
        $data = array(
            'id' => $_SESSION[S_LOGIN]
        );
        $DB->query('SELECT `top`,`email`,`username` from `%%user` where `id` = {$id}', $data);
        if($res = $DB->next()) {
            define('USERNAME', $res['username']);
            define('USEREMAIL', $res['email']);
            define('USERID', $data['id']);
            define('USERISADMIN', 0 == $res['top']);
        } else
            header('location: login.php');
    } else
        header('location: login.php');
}
