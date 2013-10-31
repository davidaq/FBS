<?php
$c = file_get_contents('report.php');
$m = NULL;
while(preg_match('/\{:\@\$result\[\'(.+?)\'\]\}/', $c, $m)) {
    $c = str_replace($m[0], '{:getVal(\'' . $m[1] . '\')}', $c);
}
$fp = fopen('report.php', 'w');
fwrite($fp, $c);
fclose($fp);
