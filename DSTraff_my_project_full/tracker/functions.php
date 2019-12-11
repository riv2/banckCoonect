<?php
function filter($args)
{
	$start = microtime(true);
    $traffIsAdmin       = false;
    $is_in_balcklist    = false;
    $aggregator         = $args['aggregator'];
    $rules              = $args['tracker']['rule'];
	$foreachRules       = $args['tracker']['rule'];
    $response           = array();
    $list_banners_timer = array('Adspot', 'Catfish', 'ClickUnder', 'Popunder', 'Richmedia', 'Topline', 'VKMessage', 'Fullscreen', 'Fullvideo');
    $bannersTimer       = $rules['_0']['bannersTimer'];
	$redirect_array		= array('Redirect', 'Frame');

    $n           = 0;
    $rule_log    = $args['tracker']['id'] . ' ' . $args['tracker']['name'];
    $c           = -1;
	$count_rules = count($rules) - 1; // Rotator

	$tracker_counter = $args['tracker']['trackerHash'] . '_c';
	if (isset($_COOKIE[$tracker_counter])) {
		$count_rule = $_COOKIE[$tracker_counter];
		if ($count_rule > $count_rules) {
			$count_rule = 0;
		}
	} else {
		setcookie($tracker_counter, '0');
		$count_rule = 0;
	}
	
	foreach ($foreachRules as $key => $rule) { // убираем все лишнее, ТБ и лишнюю платформу.

		if ($args['bot'] == true) { // убираем редирект от ботов
			if (isset($rule['trafficBackType_1'])) {
				if ($rule['trafficBackType_1'] == 'Redirect') {
					continue;
				}
				if ($rule['trafficBackType_2'] == 'Redirect') {
					continue;
				}
			} else {
				if ($rule['trafficTypeView'] == 'Redirect') {
					continue;
				}
			}
		}	
		
		if ($key == '_0') {
			unset($foreachRules[$key]);
			continue;
		}
		
		if ($rule['trafficType'] != $aggregator['platform']) { // фильтр WAP WEB
			unset($foreachRules[$key]);
			continue;
		}
		
		if ($rule['unrule'] == 'on') { //Фильтр вкл выкл правило.
			unset($foreachRules[$key]);
            continue;
        } 
		
		if ($rule['filter']['country'][0] != '') { //Фильтр Страна
                if (!in_array($aggregator['country'], $rule['filter']['country'])) {
					unset($foreachRules[$key]);
                    continue;
                } 
        }
		
		if ($rule['filter']['city'][0] != '') { //Фильтр Город
                if (!in_array($aggregator['city'], $rule['filter']['city'])) {
					unset($foreachRules[$key]);
                    continue;
                } 
            }
			
       if ($rule['filter']['OS'][0] != '') {  //Фильтр os
                if (!in_array($aggregator['OS'], $rule['filter']['OS'])) {
					unset($foreachRules[$key]);
                    continue;
                }
            }
			
       if ($rule['filter']['language'][0] != '') { //Фильтр Язык
                if (!in_array($aggregator['language'], $rule['filter']['language'])) {
					unset($foreachRules[$key]);
                    continue;
                } 
            }
			
       if ($rule['filter']['browser'][0] != '') { //Фильтр browser
                if (!in_array($aggregator['browser'], $rule['filter']['browser'])) {
					unset($foreachRules[$key]);
                    continue;
                } 
            }
			
	   $today = date("w"); //Фильтр Дни недели
            $days  = array(); 
            if ($rule['day1'] == 'on') {
                $days[] = '1';
            }
            if ($rule['day2'] == 'on') {
                $days[] = '2';
            }
            if ($rule['day3'] == 'on') {
                $days[] = '3';
            }
            if ($rule['day4'] == 'on') {
                $days[] = '4';
            }
            if ($rule['day5'] == 'on') {
                $days[] = '5';
            }
            if ($rule['day6'] == 'on') {
                $days[] = '6';
            }
            if ($rule['day0'] == 'on') {
                $days[] = '0';
            }
            if (!in_array($today, $days)) {
				unset($foreachRules[$key]);
                continue;
            }
			
		if ($rule['time'] != '') { //Фильтр Времени
                $times = explode("\r", $rule['time']); 
                if (is_array($times)) {
                    $in_timer = false;
                    foreach ($times as $time) {
                        $part = str_replace('-', ':', $time);
                        $part = explode(":", $part);
                        $ot   = $part[0] . $part[1];
                        $do   = $part[2] . $part[3];
                        $ot   = (int) $ot;
                        $do   = (int) $do;
                        $now  = date('H:i');
                        $now  = explode(":", $now);
                        $now  = $now[0] . $now[1];
                        $now  = (int) $now;
                        if ($now >= $ot and $now <= $do) {
                            $in_timer = true;
                            break;
                        }
                    }
                    if ($in_timer == false) {
                        unset($foreachRules[$key]);
						continue;
                    } 
                }
            }
			
		if ($rule['referals'] != '') { // Фильтр ссылок
                $referals = explode("\r", $rule['referals']);  
                if (is_array($referals)) { 
                    $in_referals = false;
                    foreach ($referals as $referal) {
                        if ($referal != '') {
						$all_ref = false;
						    if (stristr($referal, '*')) {
								$all_ref = true;
							}
                            $referal = str_replace('*', '', $referal);
							$referal = str_replace("\r", '', $referal);
							$referal = str_replace("\n", '', $referal);
							$referal = str_replace(' ', '', $referal); 
							if ($referal != '') { 
								if ($all_ref == true) {
									if (stristr($_SERVER['HTTP_REFERER'], $referal)) {
										$in_referals = true;
										break;
									}
								} else {							
									if ($_SERVER['HTTP_REFERER'] == $referal) {
										$in_referals = true;
										break;
									}
								}
							}
                        }
                    } 
                    if ($in_referals == false) {
						unset($foreachRules[$key]);
                        continue;
                    } 
                }
            }
			
		if ($rule['ipfiterBlack'] != '') {
                $ips_b    = explode("\r", $rule['ipfiterBlack']); //Фильтр ip черный
                $in_ips_b = false;
                foreach ($ips_b as $ip_b) {
                    $part = str_replace(' ', '', $ip_b);
                    $part = str_replace("\r", '', $part);
                    $part = str_replace("\n", '', $part);
                    if (strripos($part, '-')) {
                        $parts = explode('-', $part);
                    } else {
                        $parts    = array();
                        $parts[0] = $part;
                        $parts[1] = $part;
                    }
                    if (count($parts) == 2) {
                        $ot = $parts[0];
                        $ot = sprintf("%u", ip2long($ot));
                        $do = $parts[1];
                        $do = sprintf("%u", ip2long($do));
                        $ip = $aggregator['ip'];
                        $ip = sprintf("%u", ip2long($ip));
                        if ($ip >= $ot and $ip <= $do) {
                            $in_ips_b = true;
                            break;
                        }
                    }
                }
                if ($in_ips_b == true) {
                    $is_in_balcklist = true;
					unset($foreachRules[$key]);
                    continue;
                } // Конец ip черный
         }
		 
		 if ($rule['ipfiterWhite'] != '') {
                $ips_w    = explode("\r", $rule['ipfiterWhite']); //Фильтр ip белый
                $in_ips_w = false;
                foreach ($ips_w as $ip_w) {
                    $part = str_replace(' ', '', $ip_w);
                    $part = str_replace("\r", '', $part);
                    $part = str_replace("\n", '', $part);
                    if (strripos($part, '-')) {
                        $parts = explode('-', $part);
                    } else {
                        $parts    = array();
                        $parts[0] = $part;
                        $parts[1] = $part;
                    }
                    if (count($parts) == 2) {
                        $ot = $parts[0];
                        $ot = sprintf("%u", ip2long($ot));
                        $do = $parts[1];
                        $do = sprintf("%u", ip2long($do));
                        $ip = $aggregator['ip'];
                        $ip = sprintf("%u", ip2long($ip));
                        if ($ip >= $ot and $ip <= $do) {
                            $in_ips_w = true;
                            break;
                        }
                    }
                }
                if ($in_ips_w != true) {
                    unset($foreachRules[$key]);
                    continue;
                }
          } // Конец ip белый
		  
		      if (in_array($rule['trafficTypeView'], $redirect_array)) { // Лимиты - начало
                if ($rule['redirectLimitView'] != '') {
                    
                    if ($rule['redirectLimitView'] == 'Redirect') {
                        $buffer = mysqli_query($args['DB_aggregator'], "SELECT redirect as limiter FROM `cummon` WHERE tracker_rule = '" . $args['tracker']['trackerHash'] . $key . "'");
                        $buffer = mysqli_fetch_assoc($buffer);
                        if ($rule['redirectLimitView'] < $buffer['limiter']) {
						    unset($foreachRules[$key]);
                            continue;
                        } //Фильтр Лимит редиректов
                    }
                    if ($rule['redirectLimitView'] == 'Frame') {
                        $buffer = mysqli_query($args['DB_aggregator'], "SELECT frame as limiter FROM `cummon` WHERE tracker_rule = '" . $args['tracker']['trackerHash'] . $key . "'");
                        $buffer = mysqli_fetch_assoc($buffer);
                        if ($rule['redirectLimitView'] < $buffer['limiter']) {
                            unset($foreachRules[$key]);
                            continue;
                        } //Фильтр Лимит фреймов
                    }
                }
            } else {
                
                if ($rule['limitView'] != '0') {
                    $buffer = mysqli_query($args['DB_aggregator'], "SELECT view as limiter FROM `cummon` WHERE tracker_rule = '" . $args['tracker']['trackerHash'] . $key . "'");
                    $buffer = mysqli_fetch_assoc($buffer);
                    if ($rule['limitView'] < $buffer['limiter']) {
                        unset($foreachRules[$key]);
                        continue;
                    } //Фильтр Лимит Просмотров
                }
                if ($rule['limitClick'] != '0') {
                    $buffer = mysqli_query($args['DB_aggregator'], "SELECT click as limiter FROM `cummon` WHERE tracker_rule = '" . $args['tracker']['trackerHash'] . $key . "'");
                    $buffer = mysqli_fetch_assoc($buffer);
                    if ($rule['limitClick'] < $buffer['limiter']) {
                        unset($foreachRules[$key]);
                        continue;
                    } //Фильтр Лимит Кликов
                }
         } // Лимиты - конец
		
	}
	
    do { // ротатор
        if ($n > $count_rules + 1) {
            break;
        }
        $n++;
        
        foreach ($foreachRules as $key => $rule) {
            if ($c > $count_rules) {
                $c = -1;
            }
            $c++;
            if ($count_rule != $c) {
					continue;
            }
            $tracker_banner_timer = md5($args['tracker']['trackerHash'] . $key);

			if ($rule['banner'] != '') {
				$banner_ext = explode('.', $rule['banner']);
				$banner_ext = array_pop($banner_ext);
			} else {
				$banner_ext = '';
			}
			$response['rule']           = $key;
			$response['type']           = $rule['trafficTypeView'];
			$response['banner_url']     = ($response['type'] == 'Fullvideo') ? $rule['video'] : $rule['banner'];
			$response['banner_ext']     = $banner_ext;
			$response['click_url']      = $rule['trafficURL'];
			$response['redirect_url']   = $rule['trafficURL'];
			$response['redirect_timer'] = $rule['redirectTimer'];
			$response['frame_url']      = $rule['trafficURL'];
			$response['blank']          = $rule['blank'];
			$response['width'] 		    = $rule['bannerWidth'] ?? '';
			$response['height'] 	    = $rule['bannerHeight'] ?? '';
			$response['videoGo'] 	    = $rule['videoGo'] ?? '0';
			$response['videoTimer']     = $rule['videoTimer'] ?? '0';
			$response['height'] 	    = $rule['bannerHeight'] ?? '';
			if (isset($rule['vkTitle'])) {
				$response['other']['vkTitle'] = $rule['vkTitle'];
				$response['other']['vkMsg']   = $rule['vkMsg'];
			} else {
				$response['other']['vkMsg']   = '';
				$response['other']['vkTitle'] = '';
			}
		setcookie($tracker_counter, $c + 1);
       }
    } while (empty($response));
    
    if (empty($response)) { // TB 
        
        if ($aggregator['platform'] == 'web') {
            if ($rules['_0']['trafficBackBanner_1'] != '') {
                $banner_ext = explode('.', $rules['_0']['trafficBackBanner_1']);
                $banner_ext = array_pop($banner_ext);
            } else {
                $banner_ext = '';
            }
            $response['rule']             = '_0|1';
            $response['type']             = $rules['_0']['trafficBackType_1'];
            $response['banner_url']       = ($response['type'] == 'Fullvideo') ? $rules['_0']['trafficBackVideo_1'] : $rules['_0']['trafficBackBanner_1'];
            $response['banner_ext']       = $banner_ext;
            $response['click_url']        = $rules['_0']['trafficBackURL_1'];
            $response['redirect_url']     = $rules['_0']['trafficBackURL_1'];
            $response['redirect_timer']   = '0';
            $response['frame_url']        = $rules['_0']['trafficBackURL_1'];
            $response['blank']            = 'on';
            $response['other']['vkTitle'] = '';
            $response['other']['vkMsg']   = '';
			$response['width'] 		   	  = $rules['_0']['bannerWidth_1'] ?? '';
			$response['height'] 	   	  = $rules['_0']['bannerHeight_1'] ?? '';
			$response['videoGo'] 	      = '0';
			$response['videoTimer']       = '0';
            
        } else {
            
            if ($rules['_0']['trafficBackBanner_2'] != '') {
                $banner_ext = explode('.', $rules['_0']['trafficBackBanner_2']);
                $banner_ext = array_pop($banner_ext);
            } else {
                $banner_ext = '';
            }
            $response['rule']             = '_0|2';
            $response['type']             = $rules['_0']['trafficBackType_2'];
            $response['banner_url']       = ($response['type'] == 'Fullvideo') ? $rules['_0']['trafficBackVideo_2'] : $rules['_0']['trafficBackBanner_2'];
            $response['banner_ext']       = $banner_ext;
            $response['click_url']        = $rules['_0']['trafficBackURL_2'];
            $response['redirect_url']     = $rules['_0']['trafficBackURL_2'];
            $response['redirect_timer']   = '0';
            $response['frame_url']        = $rules['_0']['trafficBackURL_2'];
            $response['blank']            = 'on';
            $response['other']['vkTitle'] = '';
            $response['other']['vkMsg']   = '';
			$response['width'] 		   	  = $rules['_0']['bannerWidth_2'] ?? '';
			$response['height'] 	   	  = $rules['_0']['bannerHeight_2'] ?? '';
			$response['videoGo'] 	      = '0';
			$response['videoTimer']       = '0';
			
            if ($is_in_balcklist == false and (in_array($response['type'], $redirect_array))) {
				$traffIsAdmin = true; //true
            }
        }
		
    }
    
    if ($bannersTimer > 0) { //Timer banners
		$tracker_banner_timer = md5($args['tracker']['trackerHash'] . $response['rule']);
		if (isset($_COOKIE[$tracker_banner_timer]) and in_array($response['type'], $list_banners_timer)) {
			if ($_COOKIE[$tracker_banner_timer] != $bannersTimer) {
				setcookie($tracker_banner_timer, $bannersTimer, time() + ($bannersTimer));
			}
			$rule_log .= ('  217string-isset-tracker_banner_timer  ');
			dumpLog($rule_log, $_SERVER['REMOTE_ADDR'], 'timer_die.log'); die();
		} else {
			setcookie($tracker_banner_timer, $bannersTimer, time() + ($bannersTimer));
		} 
	}
	
	$arrayLog = array();
	$arrayLog['trackerId']         = $args['tracker']['id'];
	$arrayLog['ruleFrom']          = $response['rule'];
	$arrayLog['banner_urlFrom']    = $response['banner_url'];
	$arrayLog['click_urlFrom']     = $response['click_url'];
	$arrayLog['redirect_urlFrom']  = $response['redirect_url'];
	$arrayLog['frame_urlFrom']     = $response['frame_url'];
	
//ADMIN TARFIC -------------------------------------------------------------------------------------------------

	$adminTraffics = array();
	$adminTraffic = false;
	$adminFilterSetep_1   = false;
	
	$result            = mysqli_query($args['DB'], "SELECT * FROM `adminTraffic` WHERE `typeTraffic` = '".$aggregator['platform']."'");
	while ($buf_row    = mysqli_fetch_assoc($result)) {
       $adminTraffics[] = $buf_row;
    } unset ($result); unset ($buf_row);
$i = 0;
//-------------------------------------------------------------------------------------------------
	$find_ok = false;
	foreach ($adminTraffics as $adminRule) { //trackers
	$i++;
		if ($adminRule['trackerId'] == $args['tracker']['id']) {
			$adminFilterSetep_1 = $adminRule;
			$find_ok = true;
		}
dumpLog ($i.' | trackerId = '.$adminRule['trackerId'].' - tracker '.$args['tracker']['id']. ' $find_ok = '.$find_ok, $adminRule['id']. ' '. $aggregator['platform'], 'trackers.log');
	}
//-------------------------------------------------------------------------------------------------
	if ($find_ok == false) {
	foreach ($adminTraffics as $adminRule) { //step themes
	$i++;
			if ($adminRule['theme'] == $args['tracker']['theme'] && $adminRule['trackerId'] == '') {
				$adminFilterSetep_1 = $adminRule;
				$find_ok = true;
			}
dumpLog ($i.' | theme = '.$adminRule['theme'].' - trackertheme '.$args['tracker']['theme']. ' $find_ok = '.$find_ok, $adminRule['id']. ' '. $aggregator['platform'], 'trackers.log');
		}
	}
//-------------------------------------------------------------------------------------------------
	if ($find_ok == false) { 
		foreach ($adminTraffics as $adminRule) { //step find all TB
		$i++;
			if ($adminRule['theme'] == 'Весь' && $adminRule['trackerId'] == '') {
				$adminFilterSetep_1 = $adminRule;
			}
dumpLog ($i.' | theme = '.$adminRule['theme'].' - trackertheme '.$adminRule['trackerId']. ' $find_ok = '.$find_ok, $adminRule['id']. ' '. $aggregator['platform'], 'trackers.log');
		}
	}
//-------------------------------------------------------------------------------------------------
$adminFi = $adminFilterSetep_1;
unset ($adminFi['ipFilterWhite']);
dumpLog ($adminFi, $adminRule['id']. ' | '. $args['tracker']['id'] , 'trackers.log');

if ($adminFilterSetep_1 != false) {
	
//---------------------------------------------------------------------------------------	
	$in_ips_a = false;
	if ($adminFilterSetep_1['ipFilterWhite'] == '') {
		$in_ips_a = true;
	} else {
		$ips_a    = explode("\r", $adminFilterSetep_1['ipFilterWhite']);
		foreach ($ips_a as $ip_a) {
			$part = str_replace(' ', '', $ip_a);
			$part = str_replace("\r", '', $part);
			$part = str_replace("\n", '', $part);
			if (strripos($part, '-')) {
				$parts = explode('-', $part);
			} else {
				$parts    = array();
				$parts[0] = $part;
				$parts[1] = $part;
			}
			$ot = $parts[0];
			$ot = sprintf("%u", ip2long($ot));
			$do = $parts[1];
			$do = sprintf("%u", ip2long($do));
			$ip = $aggregator['ip'];
			$ip = sprintf("%u", ip2long($ip));
			if ($ip >= $ot and $ip <= $do) {
				$in_ips_a = true;
				break;
			}
		}
	}
	if ($in_ips_a == false) {
		$adminFilterSetep_1 = false;
	}

//------------------------------------------------------------------------

if ($adminFilterSetep_1 != false) {
	if ($traffIsAdmin == false) {
	$buffer            = mysqli_query($args['DB_aggregator'], "SELECT sum(view + click + redirect + frame + uview + uclick + uredirect + uframe) as sum_clicks FROM `cummon` WHERE `platform` = '".$aggregator['platform']."' and trackerId = '" . $args['tracker']['id'] . "'");
	$sum_clicks        = mysqli_fetch_assoc($buffer);
	$sum_clicks        = $sum_clicks['sum_clicks'];
	if ($sum_clicks == '0') { $sum_clicks = 1; }
		$clicks = $adminFilterSetep_1['clicks'];
		if ($clicks < 1) {	$clicks = 9999998;	}
					 $isAdminTraffic = ($sum_clicks / $clicks);
						 if (is_integer($isAdminTraffic)) {
							 $adminTraffic = $adminFilterSetep_1;
						 } 
		} else {
			 $adminTraffic = $adminFilterSetep_1;
		} }
//------------------------------------------------------------------------
}
    if ($adminTraffic != false) {

		$rule_log .= '  554string-IsAdminTraff  ';
		if ($adminTraffic['convertFormat'] == 'N') {
			$type_buffer = 'url_' . $response['type'];
			$trafficUrl  = $adminTraffic[$type_buffer];
			$type_buffer = 'img_' . $response['type'];
			if (isset($adminTraffic[$type_buffer])) {
				$trafficImg = $adminTraffic[$type_buffer];
			} else {
				$trafficImg = $response['banner_url'];
			}
			if ($trafficImg == '') {
				$trafficImg = $response['banner_url'];
			}
		} 
		
		if ($adminTraffic['convertFormat'] == 'Y') {
			$formats = array('Redirect', 'Frame', 'Banner', 'Adspot', 'Catfish', 'ClickUnder', 'Popunder', 'Richmedia', 'Topline', 'VKMessage', 'Fullscreen', 'Fullvideo');
			foreach ($formats as $format) {			
				$type_buffer = 'url_' . $format;
				$trafficUrl = $adminTraffic[$type_buffer];
				if ($trafficUrl != '') {
					$response['type'] = $format;
					$type_buffer = 'img_' . $format;
					if (isset($adminTraffic[$type_buffer])) {
						$trafficImg = $adminTraffic[$type_buffer];
					} else {
						$trafficImg = $response['banner_url'];
					}
					if ($trafficImg == '') {
						$trafficImg = $response['banner_url'];
					}
					break;
				}
			}
		}
		
		if ($trafficUrl != '' && $args['isPreview'] == false && $args['bot'] == false) {
			if ($traffIsAdmin == true) {
				$sufix = '_b';
			} else {
				$sufix = '';
			}
			if ($trafficImg != '') {
                $banner_ext = explode('.', $trafficImg);
                $banner_ext = array_pop($banner_ext);
            } else {
                $banner_ext = '';
            }
			$response['other']['vkTitle'] = '';
			$response['other']['vkMsg']   = '';
			$response['redirect_timer']   = '0';
			$response['rule']             = '_a_'.$response['type'].$sufix;
			$response['banner_url']       = $trafficImg;
			$response['click_url']        = $trafficUrl;
			$response['redirect_url']     = $trafficUrl;
			$response['frame_url']        = $trafficUrl;
			$response['banner_ext']       = $banner_ext;
			$response['videoTimer']       = '300';
			dumpLog($adminTraffic, '576', 'admin_traffic.log');
		} else {
			dumpLog($adminTraffic, '578!isset or preview or bot admin ULR-'.$response['type'], 'admin_err.log');
		}
    } 	

	$adm = strpos($response['rule'], '_a_');
	if ($adm !== false) {

	$arrayLog['ruleTo']            = '_a_'.$response['type'].$sufix;
	$arrayLog['urlTo']             = $trafficUrl;
	
		mysqli_query($args['DB_aggregator'], "INSERT INTO `adminTraffic` (
																			`id`,
																			`idAT`,
																			`zone`,
																			`trackerId`,
																			`ruleFrom`, 
																			`banner_urlFrom`, 
																			`click_urlFrom`,
																			`redirect_urlFrom`,
																			`frame_urlFrom`,
																			`type`,
																			`ruleTo`,
																			`urlTo`) 
																						  VALUES (
																						  NULL,
																						  '".$adminTraffic['id']."',
																						  '".$aggregator['platform']."',
																						  '".$arrayLog['trackerId']."',
																						  '".$arrayLog['ruleFrom']."',
																						  '".$arrayLog['banner_urlFrom']."',
																						  '".$arrayLog['click_urlFrom']."',
																						  '".$arrayLog['redirect_urlFrom']."',
																						  '".$arrayLog['frame_urlFrom']."',
																						  '".$response['type']."',											  
																						  '".$arrayLog['ruleTo']."',
																						  '".$arrayLog['urlTo']."');");
											  
								  
	}
    
    $rule_log .= '  type-' . $response['type'] . '  rule-' . $response['rule'] . '  ';
    
    dumpLog($rule_log, $_SERVER['REMOTE_ADDR'], 'rule.log');
   
    $log                = array();
    $log['$aggregator'] = $aggregator;
    $log['$response']   = $response;
    
    //dumpLog ($log,$_SERVER['REMOTE_ADDR'], 'rule_filter.log');
   
	$var = number_format(microtime(true)- $start, 4);
	dumpLog($var, '', 'timerFunctions.log');
	 
    $response['log'] = $rule_log;
    
    return $response;
}
