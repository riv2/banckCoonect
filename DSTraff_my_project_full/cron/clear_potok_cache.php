<?php
$start = microtime(true);
require_once('protected/dump.php');
require_once('protected/conf.php');


mysqli_query($DB, "DELETE FROM `tmptrackers` WHERE id > 0");


$end           = (microtime(true) - $start);
$log           = array();
$log['script'] = 'clear_potok_cache';
$log['time']   = $end;

dumpLog($log, '-', 'cron.log');
