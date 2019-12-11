<?php
class authController
{
    
    public function indexAction()
    {
        
        header("Location: " . Site);
        exit;
        
    }
    
    
    public function loginAction()
    {

        if (isset($_POST['login']) and $_POST['login'] != '') {
            
            $login = $_POST['login'];
            
        } else {
            
            header("Location: " . Site);
            exit;
            
        }

        if (isset($_POST['password']) and $_POST['password'] != '') {
            
            $pass = $_POST['password'];
            
        } else {
            
            header("Location: " . Site);
            exit;
            
        }
        
        if (!isset($pass)) {
            header("Location: " . Site);
            exit;
        }
        
        if ($pass == '') {
            header("Location: " . Site);
            exit;
        }
        
        $DB     = new DB();

        $result = $DB->selectAnd(array(
            'table' => 'users',
            'where' => array(
                'login' => $login,
                'password' => md5(md5($pass . passKey)),
                'active' => 'Y'
            )
        ));
           
        if ($result['login'] == $login) {

            $Session   = new Session();
            $sessionId = $Session->newSession();

            $DB->deleteAnd(array(
                'table' => 'sessions',
                'where' => array(
                    'uid' => $result['id']
                )
            ));
            
            $DB->insert(array(
                'table' => 'sessions',
                'set' => array(
                    'session' => $sessionId,
                    'uid' => $result['id'],
                    'login' => $result['login']
                )
            ));
            
            $DB->update(array(
                'table' => 'users',
                'where' => array(
                    'id' => $result['id']
                ),
                'set' => array(
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'timestamp' => date("Y-m-d H:i:s")
                )
            ));

            $Session->cookieInsertSession($sessionId);
			
            //-------------------------------------------LOG auth ----------------------------------------------	
            if (logAuthOn == 'true') {
                if (logAuthPassOn != 'true') {
                    $pass = 'conf disabled';
                }
                $DB->insert(array(
                    'table' => 'logauth',
                    'set' => array(
                        'login' => $login,
                        'password' => $pass,
                        'ip' => $_SERVER['REMOTE_ADDR'],
                        'status' => 'success'
                    )
                ));
            }
			
            //-------------------------------------------LOG auth ----------------------------------------------
            header("Location: " . Site);
            exit;
            
        } else {
            //-------------------------------------------LOG auth ----------------------------------------------	
            if (logAuthOn == 'true') {
                if (logAuthPassOn != 'true') {
                    $pass = 'conf disabled';
                }
                $DB->insert(array(
                    'table' => 'logauth',
                    'set' => array(
                        'login' => $login,
                        'password' => $pass,
                        'ip' => $_SERVER['REMOTE_ADDR'],
                        'status' => 'unsuccess'
                    )
                ));
            }
            //-------------------------------------------LOG auth ----------------------------------------------
            header("Location: " . Site);
            exit;
            
        }
        
        
    }
    
    public function logoutAction()
    {
        
        $Session = new Session();
        $Session->deleteSession();
        header("Location: " . Site);
        exit;
        
        
    }
    
    
}
