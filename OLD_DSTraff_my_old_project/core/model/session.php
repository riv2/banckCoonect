<?php
class Session
{
    
    public function newSession()
    {
        
        return (md5((rand(1, 9999999) . rand(1, 9999999) . rand(1, 9999999) . rand(1, 9999999) . rand(1, 9999999))));
        
    }
    
    public function cookieInsertSession($sessionId)
    { 
	   setcookie ("sessionId_tds_ds", $sessionId, time() + 36000, "/", $_SERVER['HTTP_HOST']);

    }
    
    public function getSession()
    {
           
        if (isset($_COOKIE['sessionId_tds_ds']) and $_COOKIE['sessionId_tds_ds'] != '') {  
            $sessionId = $_COOKIE['sessionId_tds_ds'];

            setcookie ("sessionId_tds_ds", $sessionId, time() + 36000, "/", $_SERVER['HTTP_HOST']);
            
            $DB     = new DB();
			
            $result = $DB->selectAnd(array(
                'table' => 'sessions',
                'where' => array(
                    'session' => $sessionId
                )
            ));

            $result = $DB->selectAnd(array(
                'table' => 'users',
                'where' => array(
                    'id' => $result['uid'],
                    'active' => 'Y'
                )
            ));
            $DB->update(array(
                'table' => 'sessions',
                'where' => array(
                    'session' => $sessionId
                ),
                'set' => array(
                    'timestamp' => date('Y-m-d h:i:s')
                )
            ));

            unset($result['password']);
            unset($DB);
            
        } else {
            
            $result = false;
        }
        
        return $result;
        
    }
    
    public function deleteSession()
    {
        
        if (isset($_COOKIE['sessionId_tds_ds'])) {
            
			setcookie ("sessionId_tds_ds", '', time() + 36000, "/", $_SERVER['HTTP_HOST']);
            
        }
        
    }
    
}
























