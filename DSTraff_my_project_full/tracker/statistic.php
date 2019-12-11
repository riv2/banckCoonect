<?php
header('Content-Type:text/javascript;charset=utf-8');
error_reporting(0);
$start = microtime(true);
require_once('protected/dump.php');
require_once('bot.php');

if (!isset($_SERVER['HTTP_REFERER'])) {
    dumpLog(' 8!isset HTTP_REFERER', $_SERVER['REMOTE_ADDR'], 'stat_error.log');
    die();
}

$bot = whobot();
if ($bot != 0) {
    dumpLog('index ' . $bot, $_SERVER['REMOTE_ADDR'], 'bots.log');
    die();
}

$logContent = $_SERVER['REMOTE_ADDR'] . ' | ' . $_SERVER['REQUEST_URI'] . ' | ' . date('d-m-Y h:i:s');
dumpLog ($logContent,$_SERVER['REMOTE_ADDR'],'stat_requests.log');
dumpLog ($_POST, '_POST', 'stat_post.log');
dumpLog ($_REQUEST, '_REQUEST', 'stat_post.log');
if (!isset($_REQUEST['trackerHash'])) {
    dumpLog('14 !isset trackerHash', $_SERVER['REMOTE_ADDR'], 'stat_error.log');
	dumpLog($_REQUEST, $_SERVER['REMOTE_ADDR'], 'stat_error_req.log');
	dumpLog($_SERVER, $_SERVER['REMOTE_ADDR'], 'stat_error_req.log');
    die();
} else {
    $trackerHash = $_REQUEST['trackerHash'];
    if (strlen($trackerHash) != 32) {
        dumpLog('17 trackerHash != 32', $_SERVER['REMOTE_ADDR'], 'stat_error.log');
        die();
    }
}

$storage    = $_REQUEST['storage'];
$uniq       = $_REQUEST['uniq'];
$type       = $_REQUEST['type'];
$uniqHash   = (isset($_REQUEST['uniqHash'])) ? $_REQUEST['uniqHash'] : false;
if (!$uniqHash) {
	    dumpLog('39 !uniqHash', $_SERVER['REMOTE_ADDR'], 'stat_error.log');
        die();
}
$ownerUid   = $_REQUEST['ownerUid'];
$ownerLogin = $_REQUEST['ownerLogin'];
$trackerId  = $_REQUEST['trackerId'];
$rule       = $_REQUEST['rule'];

$created    = date("Y-m-d H:i:s");

$tracker_rule = $trackerHash . $rule;

require_once('protected/conf.php');

$tracker_validate = mysqli_query($DB, "SELECT id FROM `trackers` WHERE id = '" . $trackerId . "' and active = 'Y' and moderated = 'Y'");
$tracker_validate = mysqli_fetch_assoc($tracker_validate);
if ($tracker_validate == null) {
    dumpLog('40 $tracker_validate == null', $_SERVER['REMOTE_ADDR'], 'stat_error.log');
    die();
}

mysqli_query($DB_aggregator, "INSERT INTO cummon (`id`, `trackerId`, `platform`, `ownerUid`, `ownerLogin`, `tracker_rule`, `view`, `click`, `redirect`, `frame`, `uview`, `uclick`, `uredirect`, `uframe`, `direct`, `udirect`, `timestamp`, `created`) VALUES (NULL, '$trackerId', '".$_REQUEST['phpplatform']."', '$ownerUid', '$ownerLogin', '$tracker_rule', '0', '0', '0', '0', '0', '0', '0', '0','0','0', CURRENT_TIMESTAMP, '$created');");
mysqli_query($DB_aggregator, "UPDATE cummon SET $type = $type + 1, `platform` = '".$_REQUEST['phpplatform']."' WHERE tracker_rule = '$tracker_rule'");

$viewed     = '0';
$clicked    = '0';
$redirected = '0';
$framed     = '0';
$directed   = '0';

if ($type == 'uredirect' or $type == 'redirect') {
    $viewed     = '0';
    $clicked    = '0';
    $redirected = '1';
    $framed     = '0';
    $directed   = '0';
}
if ($type == 'uclick' or $type == 'click') {
    $viewed     = '0';
    $clicked    = '1';
    $redirected = '0';
    $framed     = '0';
    $directed   = '0';
}
if ($type == 'uview' or $type == 'view') {
    $viewed     = '1';
    $clicked    = '0';
    $redirected = '0';
    $framed     = '0';
    $directed   = '0';
}
if ($type == 'uframe' or $type == 'frame') {
    $viewed     = '0';
    $clicked    = '0';
    $redirected = '0';
    $framed     = '1';
    $directed   = '0';
}
if ($type == 'udirect' or $type == 'direct') {
    $viewed     = '0';
    $clicked    = '0';
    $redirected = '0';
    $framed     = '0';
    $directed   = '1';
}

$result = mysqli_query($DB_aggregator, "INSERT INTO `" . $storage . "` (`id`, `trackerId`     , `trackerHash`     , `ownerUid`     , `ownerLogin`     , `uniqHash`    , `rule`  ,  `phpplatform`          , `phpcity`          , `phpcountry`          , `phpip`          , `phpbrowser`          , `phpreferer`          , `phpos`       , `phplanguage`       , `phprefererdomain`          , `view`         , `click`        , `redirect`      , `frame`, `direct`          , `timestamp`      , `created`)
				                                       		VALUES (NULL, '" . $trackerId . "', '" . $trackerHash . "', '" . $ownerUid . "', '" . $ownerLogin . "','" . $uniqHash . "', '" . $rule . "', '" . $_REQUEST['phpplatform'] . "', '" . $_REQUEST['phpcity'] . "', '" . $_REQUEST['phpcountry'] . "', '" . $_REQUEST['phpip'] . "', '" . $_REQUEST['phpbrowser'] . "', '" . $_REQUEST['phpreferer'] . "',     '" . $_REQUEST['phpos'] . "',     '" . $_REQUEST['phplanguage'] . "', '" . $_REQUEST['phprefererdomain'] . "', '" . $viewed . "'  , '" . $clicked . "' , '" . $redirected . "' , '" . $framed . "'   , '" . $directed . "'   , CURRENT_TIMESTAMP, '" . $created . "');");

mysqli_query($DB_aggregator, "UPDATE `" . $storage . "` SET $type = $type + 1 WHERE uniqHash = '" . $uniqHash . "'");



$logs                 = array();
$logs['type']         = $type;
$logs['p']            = $_REQUEST;
$logs['tracker_rule'] = $tracker_rule;

//dumpLog ($logs,$_SERVER['REMOTE_ADDR'],'stat_add.log'); 

$end           = (microtime(true) - $start);
$log           = array();
$log['script'] = 'statistic';
$log['time']   = $end;

dumpLog($log, $_SERVER['REMOTE_ADDR'], 'stat_timer.log');
