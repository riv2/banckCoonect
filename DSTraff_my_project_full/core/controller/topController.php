<?php
class topController
{
    
    public function indexAction($request)
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }

        require_once(Viewer);
        $view = new viewer();
        
        $request['category'] = 'top';
        $request['login']    = AuthLogin;
        $request['Site']     = Site;
        $request['LANG']     = LANG;
		$request['topLimit'] = topLimit;
		$request['toDay']    = date('Y-m-d');
        
        $DB            = new DB();
        $DB_aggregator = new DB('aggregator');
        
        $statViewBuffer = $DB_aggregator->query('SELECT sum(view + uview + click + uclick + redirect + uredirect + frame + uframe + direct + udirect) AS statView, ownerUid AS uid, ownerLogin as login
									   FROM  `cummon` WHERE 1 GROUP BY ownerUid ORDER BY statView DESC LIMIT ' . topLimit . ';');
        $i        = 0;
        $statView = array();
		$notForTop = array(1, 5);
        
        if ($statViewBuffer) {
            
            foreach ($statViewBuffer as $viewed) {
                
                $nick = $DB->query('SELECT nick,hidden FROM `users` WHERE id = ' . $viewed['uid'] . ';');
                
                if ($nick[0]['hidden'] != '0') {
                    $viewed['statView'] = '';
                }
                
                if ($nick[0]['nick'] == '') {
                    $nick = 'WebMaster_' . $viewed['uid'];
                } else {
                    $nick = $nick[0]['nick'];
                }

                $statView[$i]          = $viewed;
                $statView[$i]['nick']  = $nick;
                $statView[$i]['uid']   = $viewed['uid'];
                $statView[$i]['login'] = $viewed['login'];
				
				if ($statView[$i]['uid'] == 389) { //stalnoy
				$rand = rand(0, 1);
					$statView[$i]['statView'] = $statView[$i]['statView']; + $rand;
				}
				
				if (in_array($statView[$i]['uid'], $notForTop)) {
					unset ($statView[$i]);
				}

                $i++;
            }
        }

        if ($statView) {
			if (count($statView) < topLimit) { // Bots start
				$bots =  array('100' => array('nick' => 'WebMaster_419', 'statView' => $this->botTrafic(100)),
							   '101' => array('nick' => 'WebMaster_9',   'statView' => $this->botTrafic(101)),
							   '102' => array('nick' => 'Streight',      'statView' => $this->botTrafic(102)),
							   '103' => array('nick' => 'Anonim',        'statView' => $this->botTrafic(103)),
							   '104' => array('nick' => 'WebMaster_101', 'statView' => $this->botTrafic(104)),
							   '105' => array('nick' => 'WebMaster_26',  'statView' => $this->botTrafic(105)),
							   '106' => array('nick' => 'Skramer',       'statView' => $this->botTrafic(106)), 
							   '107' => array('nick' => 'WebMaster_19',  'statView' => $this->botTrafic(107)),
							   '108' => array('nick' => 'Orion',         'statView' => $this->botTrafic(108)),
							   '109' => array('nick' => 'WebMaster_13',  'statView' => $this->botTrafic(109)),
							   '110' => array('nick' => 'Joy',           'statView' => $this->botTrafic(110)));
				$countBots = topLimit - count($statView); 
				$n = 100;
				for ($i=0; $i < $countBots; $i++) {
					$statView[$n]['statView']    = $bots[$n]['statView'];
					$statView[$n]['nick'] 		 = $bots[$n]['nick'];
					$statView[$n]['uid']  		 = '-';
					$statView[$n]['login']		 = '-';
				$n++;
				}
				
				$forSort = $statView; // sort
				$statView = array();
				for ($i = 0; $i < topLimit; $i++) {
				$max = 0;
					foreach ($forSort as $key => $one) {
						if ((int)$one['statView'] > $max) {
							$max = $one['statView'];
							$statView[$i]['statView']    = $one['statView'];
							$statView[$i]['nick'] 		 = $one['nick'];
							$statView[$i]['uid']  		 = $one['uid'];
							$statView[$i]['login']		 = $one['login'];
							$statView[$i]['key']		 = $key;
						}
					}
					$forSort[$statView[$i]['key']] = -1;
				} // sort end

			} //bots end

            $request['statistic'] = $statView;
        } else {
            $request['statistic'] = 'none';
        }

        echo openCss('top');
        $view->view('authorized', $request);
    }


	private function botTrafic($num) {

	switch ($num) {
		case 100:
			$rand = rand(0, 1);
			$step = time() - 1479822474;
			$sum = 1;
			$step = $step / 55;
			$step = (int)$step;
			$sum = $sum + $step;
			$sum = 1;
			break;
		case 101:
			$rand = rand(0, 1);
			$step = time() - 1479822474;
			$sum = 1;
			$step = $step / 56;
			$step = (int)$step;
			$sum = $sum + $step;
			$sum = 1;
			break;
		case 102:
			$rand = rand(0, 1);
			$step = time() - 1479822474;
			$sum = 1;			
			break;
		case 103:
			$rand = rand(0, 1);
			$step = time() - 1479822474;
			$sum = 1;
			$step = $step / 31;
			$step = (int)$step;
			$sum = $sum + $step;
			$sum = 1;
			break;
		case 104:
			$rand = rand(0, 1);
			$step = time() - 1479822474;
			$sum = 1;
			break;
		case 105:
			$rand = rand(0, 1);
			$step = time() - 1479822474;
			$sum = 1;	
			$step = $step / 74;
			$step = (int)$step;
			$sum = $sum + $step;
			$sum = 1;
			break;
		case 106:
			$rand = rand(0, 1);
			$step = time() - 1479822474;
			$sum = 1;	
			$step = $step / 21;
			$step = $step + $rand;
			$step = (int)$step;
			$sum = $sum + $step;
			$sum = 1;
			break;
		case 107:
			$rand = rand(0, 1);
			$step = time() - 1479822474;
			$sum = 1;			
			break;
		case 108:
			$rand = rand(0, 1);
			$step = time() - 1479822474;
			$sum = 1;	
			break;	
		case 109:
			$rand = rand(0, 1);
			$step = time() - 1479822474;
			$sum = 1;
			break;	
		case 110:
			$rand = rand(0, 1);
			$step = time() - 1479822474;
			$sum = 1;
			break;				
	}	
	return $sum;
}
	
	
	
	
	
	
	
	
	
	
    
}