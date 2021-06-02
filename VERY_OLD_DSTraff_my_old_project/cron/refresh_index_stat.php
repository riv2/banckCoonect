<?php
$start = microtime(true);
require_once('protected/dump.php');
require_once('protected/conf.php');

$buffer           = "/var/www/tds/var/tmp/stat_index.tmp";
$buffer_admin     = "/var/www/tds/var/tmp/stat_index_admin.tmp";
$buffer_all       = "/var/www/tds/var/tmp/stat_index_all.tmp";
$buffer_admin_all = "/var/www/tds/var/tmp/stat_index_admin_all.tmp";

$statistic = array();


for ($i = 0; $i <= 10; $i++) {
    $day = date("Y-m-d", strtotime("-$i day"));
    
    $result = mysqli_query($DB_aggregator, "SELECT count(view) as views FROM `storage_1` WHERE view > 0 AND rule NOT LIKE '%_a%' AND created like '$day%'");
    $result = mysqli_fetch_assoc($result);
    if ($result['views'] != null) {
        $statistic['views'][] = $result['views'];
    } else {
        $statistic['views'][] = 0;
    }
    
    $result = mysqli_query($DB_aggregator, "SELECT count(click) as clicks FROM `storage_1` WHERE click > 0 AND rule NOT LIKE '%_a%' AND created like '$day%'");
    $result = mysqli_fetch_assoc($result);
    if ($result['clicks'] != null) {
        $statistic['clicks'][] = $result['clicks'];
    } else {
        $statistic['clicks'][] = 0;
    }
    
    $result = mysqli_query($DB_aggregator, "SELECT count(redirect) as redirects FROM `storage_1` WHERE redirect > 0 AND rule NOT LIKE '%_a%' AND created like '$day%'");
    $result = mysqli_fetch_assoc($result);
    if ($result['redirects'] != null) {
        $statistic['redirects'][] = $result['redirects'];
    } else {
        $statistic['redirects'][] = 0;
    }
    
    $result = mysqli_query($DB_aggregator, "SELECT count(frame) as frames FROM `storage_1` WHERE frame > 0 AND rule NOT LIKE '%_a%' AND created like '$day%'");
    $result = mysqli_fetch_assoc($result);
    if ($result['frames'] != null) {
        $statistic['frames'][] = $result['frames'];
    } else {
        $statistic['frames'][] = 0;
    }
    
    $result = mysqli_query($DB_aggregator, "SELECT count(direct) as directs FROM `storage_1` WHERE direct > 0 AND created like '$day%'");
    $result = mysqli_fetch_assoc($result);
    if ($result['directs'] != null) {
        $statistic['directs'][] = $result['directs'];
    } else {
        $statistic['directs'][] = 0;
    }
    
    
    $result = mysqli_query($DB_aggregator, "SELECT count(click) as web_click FROM `storage_1` WHERE click > 0 AND rule LIKE '%_a%' AND created like '$day%'");
    $result = mysqli_fetch_assoc($result);
    if ($result['web_click'] != null) {
        $statistic['web_click'][] = $result['web_click'];
    } else {
        $statistic['web_click'][] = 0;
    }
    
    $result = mysqli_query($DB_aggregator, "SELECT count(view) as web_view FROM `storage_1` WHERE view > 0 AND rule LIKE '%_a%' AND created like '$day%'");
    $result = mysqli_fetch_assoc($result);
    if ($result['web_view'] != null) {
        $statistic['web_view'][] = $result['web_view'];
    } else {
        $statistic['web_view'][] = 0;
    }
    
    $result = mysqli_query($DB_aggregator, "SELECT count(redirect) as wap_redirect FROM `storage_1` WHERE redirect > 0 AND rule LIKE '%_a%' AND created like '$day%'");
    $result = mysqli_fetch_assoc($result);
    if ($result['wap_redirect'] != null) {
        $statistic['wap_redirect'][] = $result['wap_redirect'];
    } else {
        $statistic['wap_redirect'][] = 0;
    }
    
    $result = mysqli_query($DB_aggregator, "SELECT count(redirect) as wap_redirect_b FROM `storage_1` WHERE redirect > 0 AND rule LIKE '%_a%_b%' AND created like '$day%'");
    $result = mysqli_fetch_assoc($result);
    if ($result['wap_redirect_b'] != null) {
        $statistic['wap_redirect_b'][] = $result['wap_redirect_b'];
    } else {
        $statistic['wap_redirect_b'][] = 0;
    }
    
    $result = mysqli_query($DB_aggregator, "SELECT count(frame) as wap_frame FROM `storage_1` WHERE frame > 0 AND rule LIKE '%_a%' AND created like '$day%'");
    $result = mysqli_fetch_assoc($result);
    if ($result['wap_frame'] != null) {
        $statistic['wap_frame'][] = $result['wap_frame'];
    } else {
        $statistic['wap_frame'][] = 0;
    }
    
    $result = mysqli_query($DB_aggregator, "SELECT count(frame) as wap_frame_b FROM `storage_1` WHERE frame > 0 AND rule LIKE '%_a%_%' AND created like '$day%'");
    $result = mysqli_fetch_assoc($result);
    if ($result['wap_frame_b'] != null) {
        $statistic['wap_frame_b'][] = $result['wap_frame_b'];
    } else {
        $statistic['wap_frame_b'][] = 0;
    }
    
}

$result = mysqli_query($DB_aggregator, "SELECT count(click) as web_click FROM `storage_1` WHERE click > 0 AND rule LIKE '%_a%'");
$result = mysqli_fetch_assoc($result);
if ($result['web_click'] != null) {
    $web_click = $result['web_click'];
} else {
    $web_click = 0;
}

$result = mysqli_query($DB_aggregator, "SELECT count(view) as web_view FROM `storage_1` WHERE view > 0 AND rule LIKE '%_a%'");
$result = mysqli_fetch_assoc($result);
if ($result['web_view'] != null) {
    $web_view = $result['web_view'];
} else {
    $web_view = 0;
}

$result = mysqli_query($DB_aggregator, "SELECT count(redirect) as wap_redirect FROM `storage_1` WHERE redirect > 0 AND rule LIKE '%_a%'");
$result = mysqli_fetch_assoc($result);
if ($result['wap_redirect'] != null) {
    $wap_redirect = $result['wap_redirect'];
} else {
    $wap_redirect = 0;
}

$result = mysqli_query($DB_aggregator, "SELECT count(redirect) as wap_redirect_b FROM `storage_1` WHERE redirect > 0 AND rule LIKE '%_a%_b%'");
$result = mysqli_fetch_assoc($result);
if ($result['wap_redirect_b'] != null) {
    $wap_redirect_b = $result['wap_redirect_b'];
} else {
    $wap_redirect_b = 0;
}

$result = mysqli_query($DB_aggregator, "SELECT count(frame) as wap_frame FROM `storage_1` WHERE frame > 0 AND rule LIKE '%_a%'");
$result = mysqli_fetch_assoc($result);
if ($result['wap_frame'] != null) {
    $wap_frame = $result['wap_frame'];
} else {
    $wap_frame = 0;
}

$result = mysqli_query($DB_aggregator, "SELECT count(frame) as wap_frame_b FROM `storage_1` WHERE frame > 0 AND rule LIKE '%_a%_b%'");
$result = mysqli_fetch_assoc($result);
if ($result['wap_frame_b'] != null) {
    $wap_frame_b = $result['wap_frame_b'];
} else {
    $wap_frame_b = 0;
}


$count = $wap_frame + $wap_redirect + $web_click + $web_view + $wap_frame_b + $wap_redirect_b;

$stat_admin_all = '<b>Всего Админ трафика</b><br>' . ' Показы:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ' . $web_view . '<br>' . ' Клики:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ' . $web_click . '<br>' . ' Редиректы: ' . $wap_redirect . '<br>' . ' Бонус Ред:&nbsp;&nbsp;&nbsp; ' . $wap_redirect_b . '<br>' . ' Фреймы:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ' . $wap_frame . '<br>' . ' Бонус Фрм:&nbsp;&nbsp; ' . $wap_frame_b . '<br>' . ' Всего:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ' . $count . '<br>';

$result = mysqli_query($DB_aggregator, "SELECT count(view) as view FROM `storage_1` WHERE view > 0 AND rule NOT LIKE '%_a%'");
$result = mysqli_fetch_assoc($result);
if ($result['view'] != null) {
    $view = $result['view'];
} else {
    $view = 0;
}

$result = mysqli_query($DB_aggregator, "SELECT count(click) as click FROM `storage_1` WHERE click > 0 AND rule NOT LIKE '%_a%'");
$result = mysqli_fetch_assoc($result);
if ($result['click'] != null) {
    $click = $result['click'];
} else {
    $click = 0;
}

$result = mysqli_query($DB_aggregator, "SELECT count(redirect) as redirect FROM `storage_1` WHERE redirect > 0 AND rule NOT LIKE '%_a%'");
$result = mysqli_fetch_assoc($result);
if ($result['redirect'] != null) {
    $redirect = $result['redirect'];
} else {
    $redirect = 0;
}

$result = mysqli_query($DB_aggregator, "SELECT count(frame) as frame FROM `storage_1` WHERE frame > 0 AND rule NOT LIKE '%_a%'");
$result = mysqli_fetch_assoc($result);
if ($result['frame'] != null) {
    $frame = $result['frame'];
} else {
    $frame = 0;
}

$result = mysqli_query($DB_aggregator, "SELECT count(direct) as direct FROM `storage_1` WHERE direct > 0 AND rule NOT LIKE '%_a%'");
$result = mysqli_fetch_assoc($result);
if ($result['direct'] != null) {
    $direct = $result['direct'];
} else {
    $direct = 0;
}

//$count = $view + $click + $redirect + $frame + $direct;
$time = time();
$view = $view;
$redirect = $redirect;
$count = $view + $redirect;


$stat_all = '<b>Всего трафика</b><br>' . ' <!-- Клики:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ' . $click . '<br> -->' . ' Редиректы: ' . $redirect . ' <br> <!--' . ' Фреймы:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ' . $frame . '<br> -->' . ' Показы:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ' . $view . '<br> <!-- ' . ' Переходы:&nbsp;&nbsp;&nbsp; ' . $direct . '<br> -->' . ' Всего:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ' . $count . '<br>';

ob_start();
?>
   
    <table id='graph_1' style = 'display:none;'>
				<caption>Количество отправленных запросов за последнии 10 дней.</caption>
				<thead>
					<tr>
								<?php
for ($i = -1; $i <= 11; $i++) {
    $day = date("d M", strtotime("-$i day"));
    echo "<th>$day</th>";
}
?>
					</tr>
				</thead>
					<tbody>
<?php /*			<tr>
						<th>Клики</th>
								<?php
foreach ($statistic['clicks'] as $value) {
	$value = percent_add($value);
    echo "<td>$value</td>";
}
?>
					</tr> */ ?>
					<tr>
						<th>Редиректы</th>
								<?php
foreach ($statistic['redirects'] as $value) {
	$value = percent_add($value);
    echo "<td>$value</td>";
}
?>
					</tr>
<?php /*					<tr>
						<th>Фреймы</th>
								<?php
foreach ($statistic['frames'] as $value) {
	$value = percent_add($value);
    echo "<td>$value</td>";
}
?>
					</tr> */ ?>
					<tr>
						<th>Показы</th>
								<?php
foreach ($statistic['views'] as $value) {
	$value = percent_add($value);
    echo "<td>$value</td>";
}
?>
					</tr>
<?php /*					<tr>
						<th>Переходы</th>
								<?php
foreach ($statistic['directs'] as $value) {
	$value = percent_add($value);
    echo "<td>$value</td>";
}
?>
					</tr> */ ?>
				</tbody>
			</table>
			
<?php
$stat = ob_get_contents();
ob_end_clean();
$fp = fopen($buffer, "w");
fwrite($fp, $stat);
fclose($fp);

$fp = fopen($buffer_all, "w");
fwrite($fp, $stat_all);
fclose($fp);


ob_start();
?>
   
    <table id='graph_2' style = 'display:none;'>
				<caption>Количество отправленных запросов по Админ траффику за последнии 10 дней.</caption>
				<thead>
					<tr>
								<?php
for ($i = -1; $i <= 11; $i++) {
    $day = date("d M", strtotime("-$i day"));
    echo "<th>$day</th>";
}
?>
					</tr>
				</thead>
					<tbody>
					<tr>
						<th>Показы</th>
								<?php
foreach ($statistic['web_view'] as $value) {
    echo "<td>$value</td>";
}
?>
					</tr>
					<tr>
						<th>Клики</th>
								<?php
foreach ($statistic['web_click'] as $value) {
    echo "<td>$value</td>";
}
?>
					</tr>
					<tr>
						<th>Редиректы</th>
								<?php
foreach ($statistic['wap_redirect'] as $value) {
    echo "<td>$value</td>";
}
?>
					</tr>
					<tr>
						<th>Бонус Ред</th>
								<?php
foreach ($statistic['wap_redirect_b'] as $value) {
    echo "<td>$value</td>";
}
?>
					</tr>
					<tr>
						<th>Фреймы</th>
								<?php
foreach ($statistic['wap_frame'] as $value) {
    echo "<td>$value</td>";
}
?>
					 </tr>
					 <tr>
						<th>Бонус Фрм</th>
								<?php
foreach ($statistic['wap_frame_b'] as $value) {
    echo "<td>$value</td>";
}
?>
					 </tr>
				</tbody>
			</table>



<?php
$admin_stat = ob_get_contents();
ob_end_clean();
$fp = fopen($buffer_admin, "w");
fwrite($fp, $admin_stat);
fclose($fp);

$fp = fopen($buffer_admin_all, "w");
fwrite($fp, $stat_admin_all);
fclose($fp);

$end           = (microtime(true) - $start);
$log           = array();
$log['script'] = 'refresh_index_stat';
$log['time']   = $end;

dumpLog($log, '-', 'cron.log');

function percent_add($int) {
	$percent = rand(1, 2);
	$cof = $int / 100;
	$percents = $cof * $percent;
	$out = $int + $percents;
	$out = (int)$out;
	$out = $out + 0;
	return $int;
}
