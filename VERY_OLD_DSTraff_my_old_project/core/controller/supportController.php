<?php
class supportController
{
    
    public function indexAction($request)
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        
        require_once(Viewer);
        $view = new viewer();
        
        $request['category'] = 'support';
        $request['login']    = AuthLogin;
        $request['Site']     = Site;
        $request['LANG']     = LANG;
        
        $DB = new DB();
        
        $DB->update(array(
            'table' => 'users',
            'where' => array(
                'id' => AuthID
            ),
            'set' => array(
                'newSupport' => 'N'
            )
        ));
        
        if (ADMIN == 'true') {
            $fromDB = $DB->selectAllAnd(array(
                'table' => 'support',
                'where' => array(
                    '1' => '1'
                )
            ));
        } else {
            $fromDB = $DB->selectAllAnd(array(
                'table' => 'support',
                'where' => array(
                    'uid' => AuthID
                )
            ));
        }
        
        if ($fromDB == false) {
            $request['support'] = 'none';
        } else {
            $buffer = array();
            $i      = 0;
            foreach ($fromDB as $one) {
                $buffer[$i]        = $one;
                $buffer[$i]['msg'] = unserialize($one['message']);
                $i++;
            }
            
            $request['support'] = $buffer;
        }
        
        if (ADMIN == 'true') {
            $users            = $DB->selectAllAnd(array(
                'table' => 'users',
                'where' => array(
                    '1' => '1'
                )
            ));
            $request['users'] = $users;
        }
        
        echo openCss('support');
        $view->view('authorized', $request);
        
    }
    
    public function newAction()
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        
        if (isset($_POST['Support']['subject']) and $_POST['Support']['subject'] != '') {
            
            $subject = $_POST['Support']['subject'];
            
        } else {
            
            header("Location: " . Site . "/support/");
            exit;
            
        }
        
        if (isset($_POST['Support']['message']) and $_POST['Support']['message'] != '') {
            
            $message   = $_POST['Support']['message'];
            $message   = nl2br($message);
            $messageDB = array();
            if (ADMIN == 'true') {
                $messageDB[0] = array(
                    'uMsg' => 'Сообщение от Администрации: ',
                    'aMsg' => $message,
                    'uTime' => date('h:i d-m-Y'),
                    'aTime' => date('h:i d-m-Y')
                );
            } else {
                $messageDB[0] = array(
                    'uMsg' => $message,
                    'aMsg' => '',
                    'uTime' => date('h:i d-m-Y'),
                    'aTime' => ''
                );
            }
            $message = serialize($messageDB);
            
        } else {
            
            header("Location: " . Site . "/support/");
            exit;
            
        }
        
        $DB = new DB();
        
        if (ADMIN == 'true') {
            if (isset($_POST['Support']['user'])) {
                $user = $_POST['Support']['user'];
            }
            
            $userLogin = $DB->selectAllAnd(array(
                'table' => 'users',
                'where' => array(
                    'id' => $user
                )
            ));
            
            $result = $DB->insert(array(
                'table' => 'support',
                'set' => array(
                    'uid' => $user,
                    'login' => $userLogin[0]['login'],
                    'subject' => $subject,
                    'message' => $message,
                    'status' => 'Отвечен',
                    'answer' => 'Y'
                )
            ));
            $DB->update(array(
                'table' => 'users',
                'where' => array(
                    'id' => $user
                ),
                'set' => array(
                    'newSupport' => 'Y'
                )
            ));
        } else {
            $result = $DB->insert(array(
                'table' => 'support',
                'set' => array(
                    'uid' => AuthID,
                    'login' => AuthLogin,
                    'subject' => $subject,
                    'message' => $message
                )
            ));
            
        }
        
        if ($result) {
            
            header("Location: " . Site . "/support/");
            exit;
            
        } else {
            
            header("Location: " . Site . "/support/");
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
        
        $DB = new DB();
        
        if (ADMIN == 'true') {
            $result = $DB->deleteAnd(array(
                'table' => 'support',
                'where' => array(
                    '1' => '1',
                    'id' => $id
                )
            ));
            
        } else {
            $result = $DB->deleteAnd(array(
                'table' => 'support',
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
    
    public function answerAction($request)
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        
        if (!isset($request['2'])) {
            header("Location: " . Site);
            exit;
        } else {
            $id = $request['2'];
        }
        
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            header("Location: " . Site);
            exit;
        }
        
        if (isset($_POST['Support']['message']) and $_POST['Support']['message'] != '') {
            $message = prepareStr($_POST['Support']['message']);
        } else {
            header("Location: " . Site . '/support/#' . $id);
            exit;
        }
        
        $DB = new DB();
        
        if (ADMIN == 'true') {
            $result = $DB->selectAllAnd(array(
                'table' => 'support',
                'where' => array(
                    '1' => '1',
                    'id' => $id
                )
            ));
            
        } else {
            $result = $DB->selectAllAnd(array(
                'table' => 'support',
                'where' => array(
                    'uid' => AuthID,
                    'id' => $id
                )
            ));
        }
        
        $result = $result[0];
        
        if (!$result) {
            header("Location: " . Site);
            exit;
        }
        
        $buffer = $result['message'];
        $buffer = unserialize($buffer);
        if (ADMIN == 'true') {
            $updBuffer = array();
            $count     = count($buffer);
            $count     = $count - 1;
            $i         = 0;
            foreach ($buffer as $one) {
                if ($i == $count) {
                    $one = array(
                        'uMsg' => $one['uMsg'],
                        'aMsg' => $message,
                        'uTime' => $one['uTime'],
                        'aTime' => date('h:i d-m-Y')
                    );
                }
                $updBuffer[$i] = $one;
                $i++;
            }
            $buffer = $updBuffer;
            
        } else {
            $add      = array(
                'uMsg' => $message,
                'aMsg' => '',
                'uTime' => date('h:i d-m-Y'),
                'aTime' => ''
            );
            $buffer[] = $add;
        }
        
        $buffer = serialize($buffer);
        
        if ($buffer != true) {
            header("Location: " . Site);
            exit;
        }
        
        if (ADMIN == 'true') {
            $DB->update(array(
                'table' => 'support',
                'where' => array(
                    'id' => $id
                ),
                'set' => array(
                    'message' => $buffer,
                    'status' => 'Отвечен',
                    'answer' => 'Y'
                )
            ));
            $DB->update(array(
                'table' => 'users',
                'where' => array(
                    'id' => $result['uid']
                ),
                'set' => array(
                    'newSupport' => 'Y'
                )
            ));
        } else {
            $DB->update(array(
                'table' => 'support',
                'where' => array(
                    'id' => $id
                ),
                'set' => array(
                    'message' => $buffer,
                    'status' => 'Расмотрение',
                    'answer' => ''
                )
            ));
        }
        
        
        header("Location: " . Site . '/support/#' . $id);
        exit;
    }
    
    
}
