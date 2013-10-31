<?php
session_start();
date_default_timezone_set('UTC');
header('content-type: text/html; charset=utf-8');
include 'config/config.php';
include 'config/define.php';
define('CWD', getcwd());
define('BASE', substr(CWD, strlen($_SERVER['DOCUMENT_ROOT'])));
define('PAGE', substr($_SERVER['SCRIPT_NAME'],1 + strlen(BASE)));
$globalDir = 'logic/global/';
foreach(scandir($globalDir) as $f) {
    if(strstr($f, '.php') == '.php')
        include_once $globalDir . $f;
}
function redirect($url) {
    return array('_action'=>'redirect','url'=>$url);
}
$logicScript = 'logic/' . PAGE;
if(file_exists($logicScript)) {
    $result = include $logicScript;
} else
    $result = NULL;

unset($RS);
$RS = NULL;

if(is_array($result) && isset($result['_action'])) {
    if($result['_action'] == 'redirect') {
        header('location: ' . $result['url']);
        die();
    }
}

function fillForms() {
    global $R;
    $vars = $R->dump(true);
    return <<<JavaScript
    <script type="text/javascript">
        $(function() {
            var vars = {$vars};
            function seek(key) {
                key = key.replace(/_/g, '.');
                key = key.toLowerCase().split('.');
                var res = vars;
                for(i in key) {
                    res = res[key[i]];
                    if(!res)
                        break;
                }
                return res;
            }
            function fill() {
                var name = $(this).attr('name');
                if(!name)
                    return;
                var val = seek(name);
                var type = $(this).attr('type');
                if(type == 'checkbox') {
                    val = val ? true : false;
                    $(this).attr('checked', val);
                } else if(type == 'radio') {
                    $(this).attr('checked', $(this).val() == val);
                } else {
                    if(val)
                        $(this).val(val);
                }
            }
            $('input').each(fill);
            $('textarea').each(fill);
            $('select').each(function() {
                var name = $(this).attr('name');
                if(!name)
                    return;
                var val = seek(name);
                $(this).find('option').each(function() {
                    $(this).attr('selected', $(this).val() == val);
                });
            });
        });
    </script>
JavaScript;
}
