<?php
session_start();

$configData = $_SESSION['_runConfig'];
$playerData = &$_SESSION['_runPlayers'];
if(!$playerData) {
	$playerData = array(
		'teams' => array(),
		'global' => array()
	);
}
foreach($playerData['teams'] as $k=>$p) {
	if(!isset($p['record']))
		$p['record'] = array();
	foreach($p['saved'] as $key=>$val) {
		$p['record'][$key] = $val;
	}
	$playerData['teams'][$k] = $p;
}
$processList = explode(';;', $_SESSION['_runProc']);

foreach($processList as $f) {
	$f = 'fbp' . $f;
	include $f . '.php';
	eval($f.'();');
}

$saveFields = array('componentsStored', 'productsStored');

foreach($playerData['teams'] as $k=>$p) {
	if(!isset($p['saved']))
		$p['saved'] = array();
	foreach($saveFields as $key) {
		$p['saved'][$key] = $p['record'][$key];
	}
	$playerData['teams'][$k] = $p;
}

header('location: ../' . $_SESSION['_runEnd']);
?>
