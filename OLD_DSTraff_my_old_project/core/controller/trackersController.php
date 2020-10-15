<?php
class trackersController
{
    
    public function indexAction($request)
    { 
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        
        require_once(Viewer);
        $view = new viewer();
        
        $request['category'] = 'listtrackers';
        $request['login']    = AuthLogin;
        $request['Site']     = Site;
        $request['LANG']     = LANG;
        
        $DB            = new DB();
        $DB_aggregator = new DB('aggregator');
        
        if (ADMIN == 'true') {
            $trackers = $DB->selectAllAnd(array(
                'table' => 'trackers',
                'where' => array(
                    '1' => '1'
                ),
				'sort' => 'moderated',
				'sortType' => 'ASC'
            ));
            $i        = 0;
            foreach ($trackers as $tracker) {
                $buffer = $DB_aggregator->query('SELECT sum(view + click + redirect + frame + uview + uclick + redirect + uframe) AS stat FROM  `cummon`
											 WHERE trackerId = ' . $tracker['id']);
                if ($buffer[0]['stat'] == null) {
                    $buffer[0]['stat'] = 0;
                }
                $trackers[$i]['stat'] = $buffer[0]['stat'];
				
				$rules = unserialize($tracker['rule']);
				$trafficType = array();
				foreach ($rules as $key=>$value) {
					if ($key == '_0') {
						$trafficType[] = $value['trafficBackType_1'];
						$trafficType[] = $value['trafficBackType_2'];
					} else {
						$trafficType[] = $value['trafficTypeView'];
					}
				}
				$trackers[$i]['trafficTypes'] = implode (',', array_unique($trafficType));
				$trackers[$i]['http'] = (stristr($tracker['themeURL'], 'https://')) ? 'https://' : 'http://';
				$trackers[$i]['http'] = 'https://';
                $i++;
            }

            $request['trackers'] = $trackers;
  
        } else {
            $trackers = $DB->selectAllAnd(array(
                'table' => 'trackers',
                'where' => array(
                    'uid' => AuthID
                )
            ));
            if ($trackers != false) {
                $i = 0;
                foreach ($trackers as $tracker) {
                    $buffer = $DB_aggregator->query('SELECT sum(view + click + redirect + frame + uview + uclick + redirect + uframe) AS stat FROM  `cummon`
											 WHERE trackerId = ' . $tracker['id']);
                    if ($buffer[0]['stat'] == null) {
                        $buffer[0]['stat'] = 0;
                    }
                    $trackers[$i]['stat'] = $buffer[0]['stat'];
					$trackers[$i]['http'] = (stristr($tracker['themeURL'], 'https://')) ? 'https://' : 'http://';
                    $i++;
                }
                $request['trackers'] = $trackers;
            } else {
                $request['trackers'] = false;
            }
        }
        
        $DB->update(array(
            'table' => 'users',
            'where' => array(
                'id' => AuthID
            ),
            'set' => array(
                'newTrackers' => 'N'
            )
        ));
        
        if ($request['trackers'] == false) {
            $request['trackers'] = 'none';
        } 
		
		if (ADMIN == 'true') { // sort for admin
			$buffer_tracker = array();
			foreach ($request['trackers'] as $key => $tracker) {
				$buffer_tracker[$key] = $tracker['stat'];
			}

			function cmp($a, $b) {
				if ($a == $b) {
				return 0;
				}
				return ($a > $b) ? -1 : 1;
			}
			uasort($buffer_tracker, 'cmp');
			
			$buffer = array();
			foreach ($buffer_tracker as $key => $val) {
				unset ($request['trackers'][$key]['rule']);
				$buffer[] = $request['trackers'][$key];
			}
		
			$moderKey = false;
			foreach ($buffer as $key => $val) {
				if ($val['moderated'] == 'N') {
					$moderKey = $key;
				}
			}
			
			if ($moderKey) {
				array_unshift($buffer, $buffer[$moderKey]);
				unset($buffer[$moderKey]);
			}
			
			$buffer = unique_multidim_array($buffer,'id'); 	

			$request['trackers'] = $buffer;
		} 	

        echo openCss('listtrackers');
        $view->view('authorized', $request);
        
    }
    
    
    public function moderateAction()
    {
        if (!AuthID or !AuthLogin or ADMIN != 'true') {
            header("Location: " . Site);
            exit;
        }
        
        if (isset($_POST['moderated'])) {
            $moderated = $_POST['moderated'];
        } else {
            $moderated = '';
        }
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
        } else {
            $id = '';
        }
        if (isset($_POST['uid'])) {
            $uid = $_POST['uid'];
        } else {
            $uid = '';
        }
        if (isset($_POST['comment'])) {
            $comment = $_POST['comment'];
        } else {
            $comment = '';
        }
        if ($moderated != '' and $id != '') {
            
            $DB = new DB();
			
			$tracker = $DB->selectAllAnd(array(
                    'table' => 'trackers',
                    'where' => array(
                        'id' => $id,
                        '1' => '1'
                    )
            ));
            
            $DB->update(array(
                'table' => 'trackers',
                'where' => array(
                    'id' => $id
                ),
                'set' => array(
                    'moderated' => $moderated,
                    'comment' => $comment
                )
            ));
			
			if ($moderated == 'Y') {
				$DB->query("UPDATE domains SET trackers=CONCAT(trackers, ' ".$id."') WHERE domain='".$tracker['0']['domain']."';");
			}
			
            $DB->update(array(
                'table' => 'users',
                'where' => array(
                    'id' => $uid
                ),
                'set' => array(
                    'newTrackers' => 'Y'
                )
            ));
            
        }
        
        header("Location: " . Site . "/trackers/");
        exit;
    }
    
    public function newAction($request)
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        
        require_once(Viewer);
        $view = new viewer();
        
        $request['category'] = 'newtracker';
        if (!isset($request['action'])) {
            $request['action'] = 'new';
        }
        $request['login'] = AuthLogin;
        $request['uid']   = AuthID;
        $request['Site']  = Site;
        $request['LANG']  = LANG;
        
        $DB = new DB();
        
        if (isset($request['id'])) {
            
            if (ADMIN == 'true') {
                $request['tracker'] = $DB->selectAllAnd(array(
                    'table' => 'trackers',
                    'where' => array(
                        'id' => $request['id'],
                        '1' => '1'
                    )
                ));
                
                
            } else {
                $request['tracker'] = $DB->selectAllAnd(array(
                    'table' => 'trackers',
                    'where' => array(
                        'id' => $request['id'],
                        'uid' => AuthID
                    )
                ));
                
            }
            if (!$request['tracker']) {
                header("Location: " . Site);
                exit;
            }
            
            $request['tracker'] = $request['tracker'][0];
            if (!isset($request['tracker']['unTracker'])) {
                $request['tracker']['unTracker'] = 'N';
            }
            if (!isset($request['tracker']['unPreview'])) {
                $request['tracker']['unPreview'] = 'N';
            }
			if (!isset($request['tracker']['allowCOIN'])) {
                $request['tracker']['allowCOIN'] = 'N';
            }
			if (!isset($request['tracker']['coinCPU'])) {
                $request['tracker']['coinCPU'] = '50';
            }
			if (!isset($request['tracker']['coinToken'])) {
                $request['tracker']['coinToken'] = '0';
            }		

            $request['tracker']['rule'] = unserialize($request['tracker']['rule']);
            
        } else {
            $request['tracker']['id']                                = '';
            $request['tracker']['name']                              = '';
            $request['tracker']['theme']                             = '';
            $request['tracker']['themeURL']                          = '';
            $request['tracker']['domain']                            = '';
            $request['tracker']['unTracker']                         = 'N';
            $request['tracker']['unPreview']                         = 'N';
			$request['tracker']['allowCOIN']                         = 'N';
			$request['tracker']['coinToken']                         = '0';			
			$request['tracker']['coinCPU']                           = '50';
            $request['tracker']['rule']['_0']['trafficBackType_1']   = '';
            $request['tracker']['rule']['_0']['trafficBackURL_1']    = '';
            $request['tracker']['rule']['_0']['trafficBackBanner_1'] = '';
			$request['tracker']['rule']['_0']['trafficBackVideo_1']  = '';
			$request['tracker']['rule']['_1']['bannerWidth_1']       = '';
			$request['tracker']['rule']['_1']['bannerHeight_1']      = '';
            $request['tracker']['rule']['_0']['trafficBackType_2']   = '';
            $request['tracker']['rule']['_0']['trafficBackURL_2']    = '';
            $request['tracker']['rule']['_0']['trafficBackBanner_2'] = '';
			$request['tracker']['rule']['_0']['trafficBackVideo_2']  = '';
			$request['tracker']['rule']['_1']['bannerWidth_2']       = '';
			$request['tracker']['rule']['_1']['bannerHeight_2']      = '';
            $request['tracker']['rule']['_0']['bannersTimer']        = '0';
            $request['tracker']['rule']['_1']['trafficType']         = 'web';
            $request['tracker']['rule']['_1']['name']                = 'First rule';
            $request['tracker']['rule']['_1']['trafficTypeView']     = 'Banner';
            $request['tracker']['rule']['_1']['trafficURL']          = '';
            $request['tracker']['rule']['_1']['postbackType']        = '';
            $request['tracker']['rule']['_1']['vkTitle']             = '';
            $request['tracker']['rule']['_1']['vkMsg']               = '';
            $request['tracker']['rule']['_1']['bannerWidth']         = '';
			$request['tracker']['rule']['_1']['bannerHeight']        = '';
            $request['tracker']['rule']['_1']['banner']              = '';
			$request['tracker']['rule']['_1']['video']               = '';
			$request['tracker']['rule']['_1']['videoTimer']          = '0';
            $request['tracker']['rule']['_1']['redirectTimer']       = '0';
            $request['tracker']['rule']['_1']['redirectLimitView']   = '0';
            $request['tracker']['rule']['_1']['limitView']           = '0';
            $request['tracker']['rule']['_1']['limitClick']          = '0';
            $request['tracker']['rule']['_1']['filter']['country']   = array(
                ''
            );
            $request['tracker']['rule']['_1']['filter']['city']      = array(
                ''
            );
            $request['tracker']['rule']['_1']['filter']['OS']        = array(
                ''
            );
            $request['tracker']['rule']['_1']['filter']['browser']   = array(
                ''
            );
            $request['tracker']['rule']['_1']['filter']['language']  = array(
                ''
            );
            $request['tracker']['rule']['_1']['referals']            = '';
            $request['tracker']['rule']['_1']['ipfiterWhite']        = '8.8.8.8-8.8.8.8
0.0.0.0-255.255.255.255';
            $request['tracker']['rule']['_1']['ipfiterBlack']        = '127.0.0.1-127.0.0.1
192.168.0.0-192.168.255.255';
            $request['tracker']['rule']['_1']['day1']                = 'on';
            $request['tracker']['rule']['_1']['day2']                = 'on';
            $request['tracker']['rule']['_1']['day3']                = 'on';
            $request['tracker']['rule']['_1']['day4']                = 'on';
            $request['tracker']['rule']['_1']['day5']                = 'on';
            $request['tracker']['rule']['_1']['day6']                = 'on';
            $request['tracker']['rule']['_1']['day0']                = 'on';
            $request['tracker']['rule']['_1']['time']                = '00:00-12:00
12:00-23:59';
            $request['tracker']['rule']['_1']['unrule']              = '';
            $request['tracker']['rule']['_1']['blank']               = 'on';
			$request['tracker']['rule']['_1']['videoGo']             = 'off';
            $request['tracker']['rule']['_1']['uniq']                = 'on';
            $request['tracker']['rule']['_1']['return']              = 'on';
            $request['tracker']['rule']['_1']['postbackURL']         = '';
            $request['tracker']['rule']['_1']['unreferal']           = '';
            
            
        }
        
        $request['formats'] = $DB->selectAllAnd(array(
            'table' => 'referenceFormats',
            'where' => array(
                'active' => 'Y'
            )
        ));
        
        $request['country'] = $DB->query("SELECT country_iso_code as data,country_name as name FROM referenceGeo GROUP BY country_name");
        
        
        $request['OS'] = $DB->selectAllAnd(array(
            'table' => 'referenceOS',
            'where' => array(
                '1' => '1'
            )
        ));
        
        $request['browser'] = $DB->selectAllAnd(array(
            'table' => 'referenceBrowser',
            'where' => array(
                '1' => '1'
            )
        ));
        
        $request['theme'] = $DB->selectAllAnd(array(
            'table' => 'referenceTheme',
            'where' => array(
                '1' => '1'
            )
        ));
        
        $request['language'] = $DB->selectAllAnd(array(
            'table' => 'referenceLanguage',
            'where' => array(
                '1' => '1'
            )
        ));
        
        if (ADMIN == 'true') {
            if (isset($request['tracker']['uid'])) {
                $ownerId = $request['tracker']['uid'];
            } else {
                $ownerId = AuthID;
            }
            $request['domains'] = $DB->selectAllAnd(array(
                'table' => 'domains',
                'where' => array(
                    '1' => '1'
                )
            ));
            
            if ($request['domains'] == false) {
                $request['domains'] = 'none';
            }
        } else {
            $request['domains'] = $DB->selectAllOr(array(
                'table' => 'domains',
                'where' => array(
                    'uid' => AuthID,
					'for_all' => 1
                )
            ));
            if ($request['domains'] == false) {
                $request['domains'] = 'none';
            }
            
        }
        
        echo openCss('actionstracker');
        $view->view('authorized', $request);
        
    }
    
    public function editAction($request)
    {
        
        if (!isset($request[2])) {
            header("Location: " . Site);
            exit;
        } else {
            if (!filter_var($request[2], FILTER_VALIDATE_INT)) {
                header("Location: " . Site);
                exit;
            }
            $request['id'] = $request[2];
        }
        
        $request['action'] = 'edit';
        $this->newAction($request);
    }
    
    public function referenceCityAction($request)
    {
        
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        if (!isset($request[2])) {
            header("Location: " . Site);
            exit;
        }
        
        $citesArray = array();
        $DB         = new DB();
        $citesReq   = str_replace(",", "','", $request[2]);
        
        $cites = $DB->query("SELECT city_name as city,country_name as country FROM referenceGeo WHERE country_name in ('" . $citesReq . "') GROUP BY city_name ORDER BY country_name");
        
        $i = 0;
        foreach ($cites as $city) {
            $citesArray[$i]['city']    = $city['city'];
            $citesArray[$i]['country'] = $city['country'];
            $i++;
        }
        print json_encode($citesArray);
        
    }
    
    
    public function digestAction($request)
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }

        if (!isset($_SERVER['HTTP_REFERER'])) {
            header("Location: " . Site);
            exit;
        }
        $pod_type   = '';
        $is_preview = explode('/', $_SERVER['REQUEST_URI']);
        $digestType = explode('/', $_SERVER['HTTP_REFERER']);
        if (in_array('preview', $is_preview)) {
            if (in_array('edit', $digestType)) {
                $pod_type = 'edit';
            } else {
                $pod_type = 'new';
            }
            $digestType = 'preview';
            $table      = 'tmptrackers';
        } else {
            $digestType = $digestType[4];
            $table      = 'trackers';
        }
        
        if ($digestType == 'new' or $digestType == 'preview') {
            
            if (!isset($_POST['tracker'])) {
                echo '#error_post_tracker';
                exit;
            }
            
            if ($_POST['tracker'] == '') {
                echo '#error_empty_post_tracker';
                exit;
            }
            
            $Tracker = $_POST['tracker'];
            
            $Tracker['uid']   = AuthID;
            $Tracker['login'] = AuthLogin;
            $Tracker['rule']  = $_POST['rule'];
            
            if ($pod_type == 'edit') {
                $Tracker['name']     = 'edit';
                $Tracker['theme']    = 'edit';
                $Tracker['themeURL'] = 'http://edit.ru/';
                $Tracker['domain']   = 'edit.ru';
            }
            
            unset($_POST);
            
            $Tracker['rule'] = serialize($Tracker['rule']);
            if (!$Tracker['rule'] or $Tracker['rule'] == '') {
                echo '#error_rule_serialize';
                exit;
            }
            
            $validate = self::validate($Tracker);

            if ($validate != 'ok') {
                echo $validate;
                exit;
            }
            
            $DB = new DB();
            
            $trackerId = $DB->insert(array(
                'table' => $table,
                'set' => array(
                    'uid' => $Tracker['uid'],
                    'login' => $Tracker['login'],
                    'name' => $Tracker['name'],
                    'theme' => $Tracker['theme'],
                    'themeURL' => $Tracker['themeURL'],
                    'domain' => $Tracker['domain'],
                    'unTracker' => $Tracker['unTracker'],
                    'unPreview' => $Tracker['unPreview'],
					'allowCOIN' => $Tracker['allowCOIN'],
					'coinCPU' => $Tracker['coinCPU'],
					'coinToken' => $Tracker['coinToken'],					
                    'rule' => $Tracker['rule']
                )
            ));
            
            if ($trackerId) {
                $trackerHash = md5($trackerId . $Tracker['name']);
                
                $urlDomain = 'http://' . $Tracker['domain'];
                
                $rules = unserialize($Tracker['rule']);
                
                foreach ($rules as $key => $rule) {
                    
                    if ($key != '_0') {
                        
                        $postbackType = '';
                        
                        if ($postbackType == '') {
                            $postbackURL = "none";
                        }
                        
                        $rules_upd[$key]                = $rule;
                        $rules_upd[$key]['postbackURL'] = $postbackURL;
                        
                        if (!isset($rule['day1'])) {
                            $rules_upd[$key]['day1'] = 'off';
                        }
                        if (!isset($rule['day2'])) {
                            $rules_upd[$key]['day2'] = 'off';
                        }
                        if (!isset($rule['day3'])) {
                            $rules_upd[$key]['day3'] = 'off';
                        }
                        if (!isset($rule['day4'])) {
                            $rules_upd[$key]['day4'] = 'off';
                        }
                        if (!isset($rule['day5'])) {
                            $rules_upd[$key]['day5'] = 'off';
                        }
                        if (!isset($rule['day6'])) {
                            $rules_upd[$key]['day6'] = 'off';
                        }
                        if (!isset($rule['day0'])) {
                            $rules_upd[$key]['day0'] = 'off';
                        }
                        if (!isset($rule['unreferal'])) {
                            $rules_upd[$key]['unreferal'] = 'off';
                        }
                        if (!isset($rule['blank'])) {
                            $rules_upd[$key]['blank'] = 'off';
                        }
						if (!isset($rule['videoGo'])) {
                            $rules_upd[$key]['videoGo'] = 'off';
                        }
                        if (!isset($rule['uniq'])) {
                            $rules_upd[$key]['uniq'] = 'off';
                        }
                        if (!isset($rule['return'])) {
                            $rules_upd[$key]['return'] = 'off';
                        }
                        if (!isset($rule['unrule'])) {
                            $rules_upd[$key]['unrule'] = 'off';
                        }
                        
                        if (!isset($rule['filter']['country'])) {
                            $rules_upd[$key]['filter']['country'] = array(
                                ''
                            );
                        }
                        if (!isset($rule['filter']['city'])) {
                            $rules_upd[$key]['filter']['city'] = array(
                                ''
                            );
                        }
                        if (!isset($rule['filter']['OS'])) {
                            $rules_upd[$key]['filter']['OS'] = array(
                                ''
                            );
                        }
                        if (!isset($rule['filter']['browser'])) {
                            $rules_upd[$key]['filter']['browser'] = array(
                                ''
                            );
                        }
                        if (!isset($rule['filter']['language'])) {
                            $rules_upd[$key]['filter']['language'] = array(
                                ''
                            );
                        }
                        
                        
                    } else {
                        $rules_upd[$key] = $rule;
                    }
                    
                }
                
                $Tracker['rule'] = serialize($rules_upd);
                
                $updateTracker = $DB->update(array(
                    'table' => $table,
                    'set' => array(
                        'trackerHash' => $trackerHash,
                        'rule' => $Tracker['rule']
                    ),
                    'where' => array(
                        'id' => $trackerId,
                        'uid' => AuthID
                    )
                ));
                
                if ($updateTracker) {
					
                    $StorageTable = 'storage_1';
					/*
                    if ($Tracker['name'] != 'edit') {
						 require_once(Smtp);
						$mailSMTP = new SendMailSmtpClass('support@dstraff.ru', 'Dstraff312!', 'ssl://smtp.mail.ru', 'Support', 465);
						$headers  = "MIME-Version: 1.0\r\n";
						$headers .= "Content-type: text/html; charset=utf-8\r\n"; // кодировка письма
						$mailSMTP->send('support@dstraff.ru', 'Создан новый поток, требуется модерация - ' . $Tracker['name'], 'Время создания: ' . date('Y-m-d H:i:s') . ' Название: ' . $Tracker['name'], $headers);
					}
                  */
                    if (ADMIN == 'true') {
                        $updateAddStorage = $DB->update(array(
                            'table' => $table,
                            'set' => array(
                                'aggregatorStorage' => $StorageTable,
                                'active' => 'Y'
                            ),
                            'where' => array(
                                'id' => $trackerId,
                                '1' => '1'
                            )
                        ));
                    } else {
                        $updateAddStorage = $DB->update(array(
                            'table' => $table,
                            'set' => array(
                                'aggregatorStorage' => $StorageTable,
                                'active' => 'Y'
                            ),
                            'where' => array(
                                'id' => $trackerId,
                                'uid' => AuthID
                            )
                        ));
                    }
                    if ($updateAddStorage) {
                        if ($digestType == 'preview') {
                            echo $trackerHash;
                        } else {
                            echo 'ok';
                        }
                    } else {
                        echo '#error_updateAddStorage';
                        exit;
                    }
                    
                } else {
                    echo '#error_updateTracker';
                    exit;
                    
                }
            } else {
                echo '#error_trackerId';
                exit;
                
            }
        } elseif ($digestType == 'edit') {
            
            $Tracker         = $_POST['tracker'];
            $Tracker['rule'] = $_POST['rule'];
            unset($_POST);
            
            $trackerId = $Tracker['trackerId'];
            $unTracker = $Tracker['unTracker'];
            $unPreview = $Tracker['unPreview'];
			$allowCOIN = $Tracker['allowCOIN'];
			$coinCPU   = $Tracker['coinCPU'];	
			$coinToken = $Tracker['coinToken'];				
            
            if (!filter_var($trackerId, FILTER_VALIDATE_INT)) {
                echo '#error_TrackerId';
                exit;
            }
            
            
            $Tracker['rule'] = serialize($Tracker['rule']);
            if (!$Tracker['rule'] or $Tracker['rule'] == '') {
                echo '#error_rule_serialize';
                exit;
            }
            
            $Tracker['name']     = 'edit';
            $Tracker['theme']    = 'edit';
            $Tracker['themeURL'] = 'http://edit.ru/';
            $Tracker['domain']   = 'edit.ru';
            
            $validate = self::validate($Tracker);
            if ($validate != 'ok') {
                echo $validate;
                exit;
            }
            
            $urlDomain = 'http://' . $Tracker['domain'] . '/tracker';
            
            $rules = unserialize($Tracker['rule']);
            
            foreach ($rules as $key => $rule) {
                
                if ($key != '_0') {
                    
                    $postbackType = '';
                    
                    if ($postbackType == '') {
                        $postbackURL = "none";
                    }
                    
                    $rules_upd[$key]                = $rule;
                    $rules_upd[$key]['postbackURL'] = $postbackURL;
                    
                    if (!isset($rule['day1'])) {
                        $rules_upd[$key]['day1'] = 'off';
                    }
                    if (!isset($rule['day2'])) {
                        $rules_upd[$key]['day2'] = 'off';
                    }
                    if (!isset($rule['day3'])) {
                        $rules_upd[$key]['day3'] = 'off';
                    }
                    if (!isset($rule['day4'])) {
                        $rules_upd[$key]['day4'] = 'off';
                    }
                    if (!isset($rule['day5'])) {
                        $rules_upd[$key]['day5'] = 'off';
                    }
                    if (!isset($rule['day6'])) {
                        $rules_upd[$key]['day6'] = 'off';
                    }
                    if (!isset($rule['day0'])) {
                        $rules_upd[$key]['day0'] = 'off';
                    }
                    if (!isset($rule['unreferal'])) {
                        $rules_upd[$key]['unreferal'] = 'off';
                    }
                    if (!isset($rule['blank'])) {
                        $rules_upd[$key]['blank'] = 'off';
                    }
					if (!isset($rule['videoGo'])) {
                        $rules_upd[$key]['videoGo'] = 'off';
                    }
                    if (!isset($rule['uniq'])) {
                        $rules_upd[$key]['uniq'] = 'off';
                    }
                    if (!isset($rule['return'])) {
                        $rules_upd[$key]['return'] = 'off';
                    }
                    if (!isset($rule['unrule'])) {
                        $rules_upd[$key]['unrule'] = 'off';
                    }
                    
                    if (!isset($rule['filter']['country'])) {
                        $rules_upd[$key]['filter']['country'] = array(
                            ''
                        );
                    }
                    if (!isset($rule['filter']['city'])) {
                        $rules_upd[$key]['filter']['city'] = array(
                            ''
                        );
                    }
                    if (!isset($rule['filter']['OS'])) {
                        $rules_upd[$key]['filter']['OS'] = array(
                            ''
                        );
                    }
                    if (!isset($rule['filter']['browser'])) {
                        $rules_upd[$key]['filter']['browser'] = array(
                            ''
                        );
                    }
                    if (!isset($rule['filter']['language'])) {
                        $rules_upd[$key]['filter']['language'] = array(
                            ''
                        );
                    }
                    if (!isset($rule['referals'])) {
                        $rules_upd[$key]['referals'] = '';
                    }
                    
                } else {
                    $rules_upd[$key] = $rule;
                }
            }
            
            $Tracker['rule'] = serialize($rules_upd);
           
            $set = array(
                'rule' => $Tracker['rule'],
                'unTracker' => $unTracker,
                'unPreview' => $unPreview,
				'allowCOIN' => $allowCOIN,	
		    	'coinCPU' => $coinCPU,	
				'coinToken' => $coinToken,	
			);
            
            $DB = new DB();
            
            if (ADMIN == 'true') {
                $updateTracker = $DB->update(array(
                    'table' => 'trackers',
                    'set' => $set,
                    'where' => array(
                        'id' => $trackerId,
                        '1' => 1
                    )
                ));
            } else {
                $updateTracker = $DB->update(array(
                    'table' => 'trackers',
                    'set' => $set,
                    'where' => array(
                        'id' => $trackerId,
                        'uid' => AuthID
                    )
                ));
            }
            
            
            if ($updateTracker) {
                
                echo 'edit_ok';
                
            } else {
                
                echo 'error_updateTracker';
            }
            
        } else {
            echo '#error_digest_type';
            exit;
        }
    }
    
    public function delAction()
    {
        if (!AuthID or !AuthLogin) {
            echo '#error_auth';
            exit;
        }
        
        if (isset($_POST['id']) and $_POST['id'] != '') {
            $id = prepareStr($_POST['id']);
        } else {
            echo '#error_id';
            exit;
        }
        
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            echo '#error_type_id';
            exit;
        }
        
        $DB            = new DB();
        $DB_aggregator = new DB('aggregator');
        
        if (ADMIN == 'true') {
            $storage = $DB->selectAllAnd(array(
                'table' => 'trackers',
                'where' => array(
                    '1' => '1',
                    'id' => $id
                )
            ));
            $storage = $storage[0]['aggregatorStorage'];
            
            //	$DB_aggregator->query("DELETE FROM $storage WHERE trackerId = $id");
            //	$DB_aggregator->query("DELETE FROM cummon WHERE trackerId = $id");
            
            $result = $DB->deleteAnd(array(
                'table' => 'trackers',
                'where' => array(
                    '1' => '1',
                    'id' => $id
                )
            ));
        } else {
            $storage = $DB->selectAllAnd(array(
                'table' => 'trackers',
                'where' => array(
                    'uid' => AuthID,
                    'id' => $id
                )
            ));
            $storage = $storage[0]['aggregatorStorage'];
            
            //	$DB_aggregator->query("DELETE FROM $storage WHERE trackerId = $id and ownerUid = ".AuthID.";");
            //	$DB_aggregator->query("DELETE FROM cummon WHERE trackerId = $id and ownerUid = ".AuthID.";");
            
            $result = $DB->deleteAnd(array(
                'table' => 'trackers',
                'where' => array(
                    'uid' => AuthID,
                    'id' => $id
                )
            ));
        }
        
        if ($result) {
            echo '#success';
            exit;
        } else {
            echo '#error_db';
            exit;
        }
    }
    
    
    private function validate($Tracker)
    {
        
        if ($Tracker['name'] == '') {
            return 'tracker[name],empty';
        }
        if ($Tracker['theme'] == '') {
            return 'tracker[theme],empty';
        }
        if ($Tracker['domain'] == '') {
            return 'tracker[domain],empty';
        }
        if ($Tracker['themeURL'] == '') {
            return 'tracker[themeURL],empty';
        }
        if (!filter_var($Tracker['themeURL'], FILTER_VALIDATE_URL)) {
            return 'tracker[themeURL],errorType';
        }
        $Rules = unserialize($Tracker['rule']);
        if (!$Rules or $Rules == '') {
            return '#error_rule_unserialize';
        }
        
        foreach ($Rules as $key => $rule) {
            
            if ($key == '_0') {
                
                if ($rule['trafficBackType_1'] == '') {
                    return 'rule[' . $key . '][trafficBackType_1],empty,' . $key;
                }
                if ($rule['trafficBackURL_1'] == '') {
                    return 'rule[' . $key . '][trafficBackURL_1],empty,' . $key;
                }
                if ($rule['trafficBackType_1'] != 'Redirect' && $rule['trafficBackType_1'] != 'Frame' && $rule['trafficBackType_1'] != 'ClickUnder' && $rule['trafficBackType_1'] != 'Fullvideo') {
                    if ($rule['trafficBackBanner_1'] == '') {
                        return 'rule[' . $key . '][trafficBackBanner_1],empty';
                    }
                }
				if ($rule['trafficBackType_1'] == 'Fullvideo') {
                    if ($rule['trafficBackVideo_1'] == '') {
                        return 'rule[' . $key . '][trafficBackVideo_1],empty';
                    }
                }				
				
                if (!filter_var($rule['trafficBackURL_1'], FILTER_VALIDATE_URL)) {
                    return 'rule[' . $key . '][trafficBackURL_1],errorType';
                }
                
                if ($rule['trafficBackType_2'] == '') {
                    return 'rule[' . $key . '][trafficBackType_2],empty,' . $key;
                }
                if ($rule['trafficBackURL_2'] == '') {
                    return 'rule[' . $key . '][trafficBackURL_2],empty,' . $key;
                }
                if ($rule['trafficBackType_2'] != 'Redirect' && $rule['trafficBackType_2'] != 'Frame' && $rule['trafficBackType_2'] != 'ClickUnder' && $rule['trafficBackType_2'] != 'Fullvideo') {
                    if ($rule['trafficBackBanner_2'] == '') {
                        return 'rule[' . $key . '][trafficBackBanner_2],empty';
                    }
                }
				if ($rule['trafficBackType_2'] == 'Fullvideo') {
                    if ($rule['trafficBackVideo_2'] == '') {
                        return 'rule[' . $key . '][trafficBackVideo_2],empty';
                    }
                }	
				
                if (!filter_var($rule['trafficBackURL_2'], FILTER_VALIDATE_URL)) {
                    return 'rule[' . $key . '][trafficBackURL_2],errorType';
                }
                
            } else {
                
                if ($rule['trafficType'] == '') {
                    return 'rule[' . $key . '][trafficType],empty,' . $key;
                }
                if ($rule['trafficTypeView'] == '') {
                    return 'rule[' . $key . '][trafficTypeView],empty,' . $key;
                }
                if ($rule['trafficURL'] == '') {
                    return 'rule[' . $key . '][trafficURL],empty,' . $key;
                }
                if ($rule['trafficTypeView'] != 'Redirect' && $rule['trafficTypeView'] != 'Frame' && $rule['trafficTypeView'] != 'ClickUnder' && $rule['trafficTypeView'] != 'Fullvideo') {
                    if ($rule['banner'] == '') {
                        return 'rule[' . $key . '][banner],empty,' . $key;
                    }
                }
				if ($rule['trafficTypeView'] == 'Fullvideo') {
                    if ($rule['video'] == '') {
                        return 'rule[' . $key . '][video],empty,' . $key;
                    }
                }
                if ($rule['trafficTypeView'] == 'Redirect') {
                    if ($rule['redirectTimer'] == '') {
                        return 'rule[' . $key . '][redirectTimer],empty,' . $key;
                        if (!filter_var($rule['redirectTimer'], FILTER_VALIDATE_INT)) {
                            return 'rule[' . $key . '][redirectTimer],errorType,' . $key;
                        }
                    }
                }
                if (!filter_var($rule['trafficURL'], FILTER_VALIDATE_URL)) {
                    return 'rule[' . $key . '][trafficURL],errorType,' . $key;
                }
                
            }
            
        }
        
        return 'ok';
        
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
}
