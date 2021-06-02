<?php
$start = microtime(true);
require_once('protected/dump.php');
require_once('protected/conf.php');

$i   = 1;
$day = date("Y-m-d", strtotime("-$i day"));

mysqli_query($DB, "DELETE FROM `sessions` WHERE timestamp < '$day 00:00:00'");

$end           = (microtime(true) - $start);
$log           = array();
$log['script'] = 'clear_old_session';
$log['time']   = $end;

dumpLog($log, '-', 'cron.log');