 <?php
class adminUsersController
{
    
    public function indexAction($request)
    {
        if (!AuthID or !AuthLogin or ADMIN != 'true') {
            header("Location: " . Site);
            exit;
        }
        
        require_once(Viewer);
        $view = new viewer();
        
        $request['category'] = 'adminUsers';
        $request['login']    = AuthLogin;
        $request['Site']     = Site;
        $request['LANG']     = LANG;
        
        $DB            = new DB();
        $DB_aggregator = new DB('aggregator');
        
        $users = $DB->selectAllAnd(array(
            'table' => 'users',
            'where' => array(
                '1' => '1'
            )
        ));
        $i     = 0;
        foreach ($users as $user) {
            $buffer = $DB_aggregator->query('SELECT sum(view + uview + click + uclick + redirect + uredirect + frame + uframe + direct + udirect) AS stat FROM  `cummon`
											 WHERE ownerUid = ' . $user['id']);
            if ($buffer[0]['stat'] == null) {
                $buffer[0]['stat'] = 0;
            }
            $users[$i]['stat'] = $buffer[0]['stat'];
            $i++;
        }
        $request['users'] = $users;
        
        echo openCss('profile');
        $view->view('authorized', $request);
        
    }
    
    public function banAction($request)
    {
        if (!AuthID or !AuthLogin or ADMIN != 'true') {
            header("Location: " . Site);
            exit;
        }
        
        if ($request[2] == '1') {
            header("Location: " . Site . "/adminUsers/");
            exit;
        }
        
        $DB = new DB();
        
        $DB->update(array(
            'table' => 'users',
            'where' => array(
                'id' => $request[2]
            ),
            'set' => array(
                'active' => 'N'
            )
        ));
        
        $DB->update(array(
            'table' => 'trackers',
            'where' => array(
                'uid' => $request[2]
            ),
            'set' => array(
                'active' => 'N'
            )
        ));
        
        header("Location: " . Site . "/adminUsers/");
        exit;
        
    }
    
    public function unbanAction($request)
    {
        if (!AuthID or !AuthLogin or ADMIN != 'true') {
            header("Location: " . Site);
            exit;
        }
        
        if ($request[2] == '1') {
            header("Location: " . Site . "/adminUsers/");
            exit;
        }
        
        $DB = new DB();
        
        $DB->update(array(
            'table' => 'users',
            'where' => array(
                'id' => $request[2]
            ),
            'set' => array(
                'active' => 'Y'
            )
        ));
        
        $DB->update(array(
            'table' => 'trackers',
            'where' => array(
                'uid' => $request[2]
            ),
            'set' => array(
                'active' => 'Y'
            )
        ));
        
        header("Location: " . Site . "/adminUsers/");
        exit;
        
    }
    
}
