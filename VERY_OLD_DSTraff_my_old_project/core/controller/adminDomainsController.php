 <?php
class adminDomainsController
{
    
    public function indexAction($request)
    {
        if (!AuthID or !AuthLogin or ADMIN != 'true') {
            header("Location: " . Site);
            exit;
        }
        
        require_once(Viewer);
        $view = new viewer();
        
        $request['category'] = 'adminDomains';
        $request['login']    = AuthLogin;
        $request['Site']     = Site;
        $request['LANG']     = LANG;
        
        $DB = new DB();
        
        $request['domains'] = $DB->selectAllAnd(array(
            'table' => 'domains',
            'where' => array(
                '1' => '1'
            )
        ));
        
        
        if ($request['domains'] == false) {
            $request['domains'] = 'none';
        }
        
        echo openCss('domains');
        $view->view('authorized', $request);
        
    }
    
    public function digestAction()
    {
        if (!AuthID or !AuthLogin or ADMIN != 'true') {
            header("Location: " . Site);
            exit;
        }
        
        if (isset($_POST['status'])) {
            $status = $_POST['status'];
        } else {
            $status = '';
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
        if ($status != '' and $id != '') {
            
            $DB = new DB();
            
            if ($status == 'Удалить') {
                
                $result = $DB->deleteAnd(array(
                    'table' => 'domains',
                    'where' => array(
                        '1' => '1',
                        'id' => $id
                    )
                ));
                $DB->update(array(
                    'table' => 'users',
                    'where' => array(
                        'id' => $uid
                    ),
                    'set' => array(
                        'newDomains' => 'Y'
                    )
                ));
            }
            
            if ($status == 'Припаркован') {
                $DB->update(array(
                    'table' => 'domains',
                    'where' => array(
                        'id' => $id
                    ),
                    'set' => array(
                        'status' => 'Припаркован'
                    )
                ));
                
                $DB->update(array(
                    'table' => 'users',
                    'where' => array(
                        'id' => $uid
                    ),
                    'set' => array(
                        'newDomains' => 'Y'
                    )
                ));
            }
            
        }
        
        header("Location: " . Site . "/adminDomains/");
        exit;
        
        
    }
    
    
}
