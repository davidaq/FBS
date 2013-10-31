<?php
include 'logic/entry.php';
$DB->query('SELECT `username` FROM `%%user`');
$ret = array();
foreach($DB->flush() as $f) {
    $ret[] = $f['username'];
}
header('Content-type: text/plaintext; charset=utf-8');
echo json_encode($ret);
?>
