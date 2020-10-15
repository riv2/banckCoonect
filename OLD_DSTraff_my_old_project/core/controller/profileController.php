<?php
class profileController
{
    
    public function indexAction($request)
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        
        require_once(Viewer);
        $view                 = new viewer();
        $request['Site']      = Site;
        $request['category']  = 'profile';
        $request['login']     = AuthLogin;
        $request['theme']     = AuthTheme;
        $request['addinvite'] = 'false';
        $request['LANG']      = LANG;
        
        $DB = new DB();
        
        $request['profile'] = $DB->selectAllAnd(array(
            'table' => 'users',
            'where' => array(
                'id' => AuthID
            )
        ));
        unset($request['profile'][0]['password']);
        
        $request['invites'] = $DB->selectAllAnd(array(
            'table' => 'invites',
            'where' => array(
                'uid' => AuthID
            )
        ));
        
        $inviteOwnerLogin = $DB->selectAllAnd(array(
            'table' => 'invites',
            'where' => array(
                'invite' => $request['profile'][0]['invite']
            )
        ));
        
        $request['inviteOwner'] = $inviteOwnerLogin[0]['login'];
        
        if (!$request['inviteOwner']) {
            $request['inviteOwner'] = 'none';
        }
        
        if (!$request['invites']) {
            
            $request['addinvite'] = 'true';
            
        } else {
            
            if (count($request['invites']) < countInvites) {
                
                $request['addinvite'] = 'true';
                
            }
            if (ADMIN == 'true') {
                
                $request['addinvite'] = 'true';
                
            }
            
        }
        
        echo openCss('profile');
        $view->view('authorized', $request);
        
    }
    
    
    
    public function updateAction()
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        
        $set = array();
        
        if (AuthID != '1') {
            if (isset($_POST['User']['pass']) and $_POST['User']['pass'] != '') {
                $pass            = $_POST['User']['pass'];
                $set['password'] = md5(md5($pass . passKey));
            }
        }
        
        if (isset($_POST['User']['email']) and $_POST['User']['email'] != '') {
            $email        = $_POST['User']['email'];
            $set['email'] = $email;
        }
        
        if (isset($_POST['User']['nick'])) {
            $nick = $_POST['User']['nick'];
            if ($nick == '' or $nick == ' ') {
                $nick = 'Partner_' . AuthID;
            }
            $set['nick'] = $nick;
        }
        
        if (isset($_POST['User']['notes'])) {
            $notes        = $_POST['User']['notes'];
            $set['notes'] = $notes;
        }
        
        if (isset($_POST['User']['skype'])) {
            $skype        = $_POST['User']['skype'];
            $set['skype'] = $skype;
        }
        
        if (isset($_POST['User']['icq'])) {
            $icq        = $_POST['User']['icq'];
            $set['icq'] = $icq;
        }
        
        if (isset($_POST['User']['ok'])) {
            $ok        = $_POST['User']['ok'];
            $set['ok'] = $ok;
        }
        
        if (isset($_POST['User']['vk'])) {
            $vk        = $_POST['User']['vk'];
            $set['vk'] = $vk;
        }
        
        if (isset($_POST['User']['fb'])) {
            $fb        = $_POST['User']['fb'];
            $set['fb'] = $fb;
        }
        
        if (isset($_POST['User']['hidden']) and $_POST['User']['hidden'] != '') {
            $hidden        = $_POST['User']['hidden'];
            $set['hidden'] = $hidden;
        }
        
        if (isset($_POST['User']['tips']) and $_POST['User']['tips'] != '') {
            $tips        = $_POST['User']['tips'];
            $set['tips'] = $tips;
        }
        
        $DB = new DB();
        
        
        if (!empty($set)) {
            $result = $DB->update(array(
                'table' => 'users',
                'where' => array(
                    'id' => AuthID
                ),
                'set' => $set
            ));
        }
        
        
        if (regToForum == 'true' and isset($pass)) {
            if ($pass != '') {
                $DB_forum  = new DB('forum');
                $login     = AuthLogin;
                $pass      = sha1(strtolower($login) . $pass);
                $useInvait = $DB_forum->update(array(
                    'table' => 'smf_members',
                    'where' => array(
                        'member_name' => $login
                    ),
                    'set' => array(
                        'passwd' => $pass
                    )
                ));
            }
        }
        
        
        header("Location:" . Site . "/profile/");
        exit;
        
    }
    
    public function inviteAction($request)
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        
        $DB = new DB();
        
        $invites = $DB->selectAllAnd(array(
            'table' => 'invites',
            'where' => array(
                'uid' => AuthID
            )
        ));
        
        if (!$invites or count($invites) < countInvites or AuthID == 1) {
            
            $addInvite = $DB->insert(array(
                'table' => 'invites',
                'set' => array(
                    'uid' => AuthID,
                    'login' => AuthLogin,
                    'invite' => newInvite(),
                    'used' => 'N'
                )
            ));
        }
        
        
        header("Location:" . Site . "/profile/");
        exit;
        
    }
    
    public function colorAction($request)
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        
        $theme = prepareStr($request[2]);
        
        if ($theme == '') {
            header("Location: " . Site);
            exit;
        }
        
        $DB = new DB();
        
        $result = $DB->update(array(
            'table' => 'users',
            'where' => array(
                'id' => AuthID
            ),
            'set' => array(
                'theme' => $theme
            )
        ));
        
        if ($result) {
            setcookie('theme', $theme, time() + 9999999, '/', 'ids.i-cdm.ru', FALSE, TRUE);
        }
        
        
        header("Location: " . Site . "/profile/");
        exit;
    }
    
    
    
    
}
