<?php
class regController
{
    
    public function indexAction($request)
    {
        
        header("Location: " . Site);
        exit;
        
    }
    
    public function registrationAction()
    {
        
        
        if (isset($_POST['User']['login']) and $_POST['User']['login'] != '') {
            $login = prepareStr($_POST['User']['login']);
        } else {
            echo '#error_login';
            exit;
        }
        
        if (isset($_POST['User']['pass']) and $_POST['User']['pass'] != '') {
            $pass = $_POST['User']['pass'];
        } else {
            echo '#error_pass';
            exit;
        }
        
        if (isset($_POST['User']['email']) and $_POST['User']['email'] != '') {
            $email = prepareStr($_POST['User']['email']);
        } else {
            echo '#error_email';
            exit;
        }
        
        if (isset($_POST['User']['invite']) and $_POST['User']['invite'] != '') {
            $invite = prepareStr($_POST['User']['invite']);
        } else {
            echo '#error_invite';
            exit;
        }
        
        $DB       = new DB();
        $DB_forum = new DB('forum');
        
        $uniqEmail = $DB->selectAnd(array(
            'table' => 'users',
            'where' => array(
                'email' => $email
            )
        ));
        if ($uniqEmail) {
            echo '#error_uniqEmail';
            exit;
        }
        
        
        $uniq = $DB->selectAnd(array(
            'table' => 'users',
            'where' => array(
                'login' => $login
            )
        ));
        
        if (!$uniq) {
            
            $isInvite = $DB->selectAnd(array(
                'table' => 'invites',
                'where' => array(
                    'invite' => $invite,
                 //   'used' => 'N'
                )
            ));
            if ($isInvite) {
                
                $result = $DB->insert(array(
                    'table' => 'users',
                    'set' => array(
                        'login' => $login,
                        'password' => md5(md5($pass . passKey)),
                        'email' => $email,
                        'invite' => $invite,
                        'ip' => $_SERVER['REMOTE_ADDR']
                    )
                ));
                
                $useInvait = $DB->update(array(
                    'table' => 'invites',
                    'where' => array(
                        'invite' => $invite
                    ),
                    'set' => array(
                        'used' => 'Y',
                        'whoUsedId' => $result,
                        'whoUsedLogin' => $login
                    )
                ));
				/*
                if (regToForum == 'true') {
                    $pass      = sha1(strtolower($login) . $pass);
                    $useInvait = $DB_forum->insert(array(
                        'table' => 'smf_members',
                        'set' => array(
                            'member_name' => $login,
                            'date_registered' => time(),
                            'passwd' => $pass,
                            'email_address' => $email,
                            'is_activated' => '1',
                            'password_salt' => 'QWE120',
                            'real_name' => $login
                        )
                    ));
                }
                */
            } else {
                
                echo '#error_invite';
                exit;
            }
            
            if ($result) {
                
                echo '#login';
                exit;
                
            }
            
        } else {
            
            echo '#error_uniq';
            exit;
            
        }
        
    }
    
    
    
    
}