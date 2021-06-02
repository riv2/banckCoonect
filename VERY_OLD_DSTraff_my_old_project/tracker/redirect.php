<?php
require_once('protected/dump.php');
if (!isset($_GET['site'])) {
    die();
}
$site            = $_GET['site'];
$hash            = $_GET['hash'];
$_SERVER['hash'] = $hash;

dumpLog($_SERVER, ' redirect.php ', 'Redirect_SERVER.log');
dumpLog($hash, ' redirect ', 'Redirect_stat.log');
dumpLog(' to ' . $site . ' from ' . $_SERVER['HTTP_REFERER'], 'redirect', 'Redirect_script.log');

header("Location: $site");
exit;