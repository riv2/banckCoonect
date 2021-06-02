<?php
class statisticController
{
    
    public function indexAction($request)
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        $trackerId_1 = '';
        if (isset($_POST['trackers_1'])) {
            $trackerId_1 = prepareStr($_POST['trackers_1']);
            if (!filter_var($trackerId_1, FILTER_VALIDATE_INT) and $trackerId_1 != '') {
                header("Location: " . Site);
                exit;
            }
        }
        
        require_once(Viewer);
        $view = new viewer();
        
        $request['category'] = 'statistic';
        $request['login']    = AuthLogin;
        $request['Site']     = Site;
        $request['LANG']     = LANG;
        if (isset($_POST['goDate'])) {
            $goDate = prepareStr($_POST['goDate']);
        } else {
            $goDate = date("Y-m-d");
        }
        $request['toDay']   = $goDate;
        $goDate_buff        = explode('-', $goDate);
        $today              = time();
        $goDate_buff        = mktime('0', '0', '0', $goDate_buff[1], $goDate_buff[2], $goDate_buff[0]);
        $deff               = $today - $goDate_buff;
        $deff               = (int) ($deff / 86400);
        $request['cal_min'] = date("Ymd", strtotime("-30 day"));
        $request['cal_max'] = date("Ymd");
        
        $rulesId_1 = '';
        if (isset($_POST['rules_1'])) {
            $rulesId_1 = prepareStr($_POST['rules_1']);
        }
        
        $type = 'index';
        if ($trackerId_1 != '') {
            $type = 'getRules';
        }
        if ($rulesId_1 != '') {
            $type = 'getRuleInfo';
        }
        
        $DB            = new DB();
        $DB_aggregator = new DB('aggregator');
        
        if (ADMIN == 'true') {
            $tarackers = $DB->query("SELECT name,id,uid,login FROM `trackers` WHERE 1 = '1'");
        } else {
            $tarackers = $DB->query("SELECT name,id FROM `trackers` WHERE uid = '" . AuthID . "'");
        }
        if (!$tarackers) {
            $tarackers = 'none';
        }
        
        $request['trackers']           = $tarackers;
        $request['selected_tracker_1'] = 'none';
        $rules_1                       = 'none';
        $request['diagram_1']          = 'none';
        
        $arrRules = array();
        $content  = '';
        $traff    = array();
        
        if ($type != 'index') {
            if (ADMIN == 'true') {
                $rules = $DB->query("SELECT rule FROM `trackers` WHERE id = '" . $trackerId_1 . "'");
            } else {
                $rules = $DB->query("SELECT rule FROM `trackers` WHERE id = '" . $trackerId_1 . "' AND uid = '" . AuthID . "'");
            }
            $arrRules[0]['rule'] = '_0_1';
            $arrRules[0]['name'] = 'TB Web';
            $arrRules[1]['rule'] = '_0_2';
            $arrRules[1]['name'] = 'TB Wap';
            $rules               = unserialize($rules[0]['rule']);
            $i                   = 2;
            ob_start();
?>
		<select id = 'rules_1' name = 'rules_1'">
			<option <?php
            if ($rulesId_1 == '') {
                echo ' selected ';
            }
?> value = ''>Все</option>
		<?php
            foreach ($rules as $key => $value) {
                if ($key == '_0') {
                    continue;
                }
?>
		
			<option <?php
                if ($rulesId_1 == $key and $type != 'getRules') {
                    echo ' selected ';
                }
?> value = '<?php
                echo $key;
?>'> <?php
                if ($value['name'] != '') {
                    echo $value['name'];
                } else {
                    echo $key;
                }
?></option>
			
		<?php
                $arrRules[$i]['rule'] = $key;
                if ($value['name'] != '') {
                    $arrRules[$i]['name'] = $value['name'];
                } else {
                    $arrRules[$i]['name'] = $key;
                }
                $i++;
            }
?>
			<option <?php
            if ($rulesId_1 == '_0_1' and $type != 'getRules') {
                echo ' selected ';
            }
?> value = '_0_1'>TrafficBack WEB</option>
			<option <?php
            if ($rulesId_1 == '_0_2' and $type != 'getRules') {
                echo ' selected ';
            }
?> value = '_0_2'>TrafficBack WAP</option>
			<option <?php
            if ($rulesId_1 == '_p' and $type != 'getRules') {
                echo ' selected ';
            }
?> value = '_p'>Предпоказ</option>
		</select> 
	<?php
            $rules_1 = ob_get_contents();
            ob_end_clean();
        }
        
        if ($type == 'getRules') {
            $t = $deff;
            for ($i = 0; $i <= 10; $i++) {
                $day = date("Y-m-d", strtotime("-$t day"));
                $t++;
                $n = 0;
                foreach ($arrRules as $arrRule) {
                    $rule   = $arrRule['rule'];
                    $result = $DB_aggregator->query("SELECT count(id) as traff FROM `storage_1` WHERE trackerId = '$trackerId_1' AND rule = '$rule' AND created like '$day%'");
                    $result = $result[0]['traff'];
                    if ($result == null) {
                        $result = 0;
                    }
                    $traff[$n]['content'][] = $result;
                    $traff[$n]['name']      = $arrRule['name'];
                    $n++;
                }
            }
            ob_start();
?>
   
    <table id='graph_1' style = 'display:none;'>
				<caption>Количество всех отправленных запросов по правилам потока за 10 дней.</caption>
				<thead>
					<tr>
								<?php
            $t = $deff - 1;
            for ($i = -1; $i <= 11; $i++) {
                $day = date("d M", strtotime("-$t day"));
                $t++;
                echo "<th>$day</th>";
            }
?>
					</tr>
				</thead>
					<tbody>
					<?php
            foreach ($traff as $value) {
?>
					<tr>
						<th><?php
                echo $value['name'];
?></th>
								<?php
                foreach ($value['content'] as $val) {
                    echo "<td>$val</td>";
                }
?>
					</tr>
					<?php
            }
?>
				</tbody>
			</table>
			
<?php
            $diagram_1 = ob_get_contents();
            ob_end_clean();
            $request['diagram_1'] = $diagram_1;
        }
        
        if ($type == 'getRuleInfo' and $rulesId_1 == '_p') {
            
            if (ADMIN == 'true') {
                $previews = $DB_aggregator->query("SELECT * FROM `storage_1` WHERE trackerId = '" . $trackerId_1 . "' AND rule = '_p'");
            } else {
                $previews = $DB_aggregator->query("SELECT * FROM `storage_1` WHERE trackerId = '" . $trackerId_1 . "' AND rule = '_p' AND ownerUid = '" . AuthID . "'");
            }
            
            if (!$previews) {
                $request['diagram_1'] = "<div style = 'text-align:center; padding:15px; color:rgba(255,0,0,0.7)'>Для данного потока и правила не найдено ни одной записи.</div>";
            } else {
                ob_start();
?>
	<div style = 'padding:10px;'>
	  <table class = 'prev_table'>
		<tr>
			<th>Дата</th> <th>Платформа</th> <th>Страна</th> <th>Город</th>  <th>IP адрес</th> <th>Браузер</th>  <th>ОС</th> <th>Язык</th>  <th>Просмотры</th> <th>Клики</th> <th>Редиректы</th> <th>Фреймы</th>
		</tr>
		<?php
                foreach ($previews as $preview) {
?>
		<tr>
			<td><?php echo $preview['timestamp'];?></td> 
			<td><?php echo $preview['phpplatform'];?></td>
			<td><?php echo $preview['phpcountry'];?></td>
			<td><?php echo $preview['phpcity'];?></td>  
			<td><?php echo $preview['phpip'];?></td>
			<td><?php echo $preview['phpbrowser'];?></td> 
			<td><?php echo $preview['phpos'];?></td>
			<td><?php echo $preview['phplanguage'];?></td> 
			<td><?php echo $preview['view'];?></td> 
			<td><?php echo $preview['click'];?></td> 
			<td><?php echo $preview['redirect'];?></td>
			<td><?php echo $preview['frame'];?></td>
		</tr>
		<?php
                }
?>	  
	  </table>
	</div>
	
	
<?php
                $request['diagram_1'] = ob_get_contents();
                ob_end_clean();
            }
        }
        if ($type == 'index') {
            
                if (ADMIN == 'true') {
                    $notAdmin = '';
                } else {
                    $notAdmin = " AND ownerUid = '" . AuthID . "' ";
                }
                $t = $deff;
                for ($i = 0; $i <= 10; $i++) {
                    $day = date("Y-m-d", strtotime("-$t day"));
                    $t++;
                    $result = $DB_aggregator->query("SELECT count(view) as views FROM `storage_1` WHERE view > 0 AND rule NOT LIKE '%_a%' $notAdmin AND created like '$day%'");
                    if ($result[0]['views'] != null) {
                        $statistic['views'][] = $result[0]['views'];
                    } else {
                        $statistic['views'][] = 0;
                    }
                    
                    $result = $DB_aggregator->query("SELECT count(click) as clicks FROM `storage_1` WHERE click > 0 AND rule NOT LIKE '%_a%' $notAdmin AND created like '$day%'");
                    if ($result[0]['clicks'] != null) {
                        $statistic['clicks'][] = $result[0]['clicks'];
                    } else {
                        $statistic['clicks'][] = 0;
                    }
                    
                    $result = $DB_aggregator->query("SELECT count(redirect) as redirects FROM `storage_1` WHERE redirect > 0 AND rule NOT LIKE '%_a%' AND rule <> '_a_1_r_b' $notAdmin AND created like '$day%'");
                    if ($result[0]['redirects'] != null) {
                        $statistic['redirects'][] = $result[0]['redirects'];
                    } else {
                        $statistic['redirects'][] = 0;
                    }
                    
                    $result = $DB_aggregator->query("SELECT count(frame) as frames FROM `storage_1` WHERE frame > 0 AND rule <> '_a_1_f' AND rule NOT LIKE '%_a%' $notAdmin AND created like '$day%'");
                    if ($result[0]['frames'] != null) {
                        $statistic['frames'][] = $result[0]['frames'];
                    } else {
                        $statistic['frames'][] = 0;
                    }
                    
                    $result = $DB_aggregator->query("SELECT count(direct) as directs FROM `storage_1` WHERE direct > 0 $notAdmin AND created like '$day%'");
                    if ($result[0]['directs'] != null) {
                        $statistic['directs'][] = $result[0]['directs'];
                    } else {
                        $statistic['directs'][] = 0;
                    }
                    
                }
                
                ob_start();
?>
   
    <table id='graph_1' style = 'display:none;'>
				<caption>Количество всех отправленных запросов по моим потокам за 10 дней.</caption>
				<thead>
					<tr>
								<?php
                $t = $deff - 1;
                for ($i = -1; $i <= 11; $i++) {
                    $day = date("d M", strtotime("-$t day"));
                    $t++;
                    echo "<th>$day</th>";
                }
?>
					</tr>
				</thead>
					<tbody>
					<tr>
						<th>Клики</th>
								<?php
                foreach ($statistic['clicks'] as $value) {
                    echo "<td>$value</td>";
                }
?>
					</tr>
					<tr>
						<th>Редиректы</th>
								<?php
                foreach ($statistic['redirects'] as $value) {
                    echo "<td>$value</td>";
                }
?>
					</tr>
					<tr>
						<th>Фреймы</th>
								<?php
                foreach ($statistic['frames'] as $value) {
                    echo "<td>$value</td>";
                }
?>
					</tr>
					<tr>
						<th>Показы</th>
								<?php
                foreach ($statistic['views'] as $value) {
                    echo "<td>$value</td>";
                }
?>
					</tr>
					<tr>
						<th>Переходы</th>
								<?php
                foreach ($statistic['directs'] as $value) {
                    echo "<td>$value</td>";
                }
?>
					</tr>
				</tbody>
			</table>
			
<?php
                $request['diagram_1'] = ob_get_contents();
                ob_end_clean();
            
        }
	
		if (ADMIN == 'true') {
			$tarackers  = $DB->query("SELECT name,id FROM `trackers` WHERE 1 = '1'");
		   } else {
			$tarackers  = $DB->query("SELECT name,id FROM `trackers` WHERE uid = '" . AuthID . "'");
		 }
		$aggregators = array();
        if ($tarackers[0]) {
			foreach ($tarackers as $taracker) {
				$tarackerId = $taracker['id'];
			
				$aggregators[$tarackerId]['name'] = $taracker['name'].'('.$taracker['id'].')';
				
				$buffer_r = $DB_aggregator->query("SELECT count(id) as counts FROM `storage_1` WHERE rule NOT LIKE '%_a%' AND created LIKE '$goDate%' AND trackerId = '$tarackerId' AND redirect <> '0'");
				$aggregators[$tarackerId]['redirects'] = $buffer_r[0]['counts'];
				$aggregators[$tarackerId]['redirects_dst'] = $this->adminTrafic($buffer_r[0]['counts']);
				
				$buffer_v = $DB_aggregator->query("SELECT count(id) as counts FROM `storage_1` WHERE rule NOT LIKE '%_a%' AND created LIKE '$goDate%' AND trackerId = '$tarackerId' AND view <> '0'");
				$aggregators[$tarackerId]['view'] = $buffer_v[0]['counts'];
				$aggregators[$tarackerId]['view_dst'] = $this->adminTrafic($buffer_v[0]['counts']);
				
				$buffer_c = $DB_aggregator->query("SELECT count(id) as counts FROM `storage_1` WHERE rule NOT LIKE '%_a%' AND created LIKE '$goDate%' AND trackerId = '$tarackerId' AND click <> '0'");
				$aggregators[$tarackerId]['click'] = $buffer_c[0]['counts'];
				$aggregators[$tarackerId]['click_dst'] = $this->adminTrafic($buffer_c[0]['counts']);
				
				$buffer_f = $DB_aggregator->query("SELECT count(id) as counts FROM `storage_1` WHERE rule NOT LIKE '%_a%' AND created LIKE '$goDate%' AND trackerId = '$tarackerId' AND frame <> '0'");
				$aggregators[$tarackerId]['frame'] = $buffer_f[0]['counts'];
				$aggregators[$tarackerId]['frame_dst'] = $this->adminTrafic($buffer_f[0]['counts']);
				
				$buffer_d = $DB_aggregator->query("SELECT count(id) as counts FROM `storage_1` WHERE rule NOT LIKE '%_a%' AND created LIKE '$goDate%' AND trackerId = '$tarackerId' AND direct <> '0'");
				$aggregators[$tarackerId]['direct'] = $buffer_d[0]['counts'];
				$aggregators[$tarackerId]['direct_dst'] = $this->adminTrafic($buffer_d[0]['counts']);
			}
		}
		
        $request['selected_tracker_1'] = $trackerId_1;
        $request['rules_1']            = $rules_1;
        $request['potoks']             = $aggregators;
		$request['goDate']             = $goDate;
		
        echo openCss('statistic');
        $view->view('authorized', $request);
        
    }
    
    private function adminTrafic($int) {
		
	$percent = 25;
	$cof = $int / 100;
	$percents = $cof * $percent;
	$out = (int)$percents;
	return $out;
}
	
	
	
}





















