<?php
if ($_SERVER['SERVER_NAME'] == 'www.socjpeg.ru') {
	header("Location: http://socjpeg.ru/");
	exit;
}

$start = microtime(true);
error_reporting(0);
define ("APP", "1.0.0");
require_once ('core/dump.php');
require_once ('core/sessions.php');
require_once ('core/db/db.php');
require_once ('core/files.php');
require_once ('core/etc/functions.php');
require_once ('core/etc/agr.php');

$session = new Session();
$path = 'templates/error';

if (isset($_REQUEST['route'])) {
$route = trim($_REQUEST['route']);
$routeArgs = explode ('/',$route);
$path = $routeArgs[0];
unset($routeArgs[0]);
$args = $routeArgs;

if ($path == 'croper') {
	$path = 'templates/main';
	$template = 'templates/crop.php';
}

if ($path == 'view') {
	$path = 'templates/main';
	$template = 'templates/view.php';
}

if ($path == 'login') {
	$path = 'templates/main';
	$template = 'templates/login.php';
}

} else {
	$path = 'templates/main';
	$template = 'templates/upload.php';
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/core/' . $path . '.php')) {
	$controller = $_SERVER['DOCUMENT_ROOT'] . '/core/' . $path . '.php';
	require_once ($controller);
} else {
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/core/templates/error.php');
}

$Time = round(microtime(true) - $start,2).' sek';
$Kbytes = (memory_get_usage() / 1024);
$Kbytes = round($Kbytes,2);
$Mbytes = ($Kbytes / 1024);
$Mbytes = round($Mbytes,3);
$Memory = $Kbytes.' kb, '.$Mbytes.' mb';

require_once ('core/etc/stat.php');
