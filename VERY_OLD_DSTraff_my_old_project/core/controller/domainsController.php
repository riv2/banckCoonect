<?php
class domainsController
{
    
    public function indexAction($request)
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        
        require_once(Viewer);
        $view = new viewer();
        
        $request['category'] = 'domains';
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
                'newDomains' => 'N'
            )
        ));
        
        if (ADMIN == 'true') {
            $request['domains'] = $DB->selectAllAnd(array(
                'table' => 'domains',
                'where' => array(
                    '1' => '1'
                )
            ));
        } else {
            $request['domains'] = $DB->selectAllOr(array(
                'table' => 'domains',
                'where' => array(
                    'uid' => AuthID,
					'for_all' => '1'
                )
            ));
        }
        
        if ($request['domains'] == false) {
            $request['domains'] = 'none';
        }
        
        echo openCss('domains');
        $view->view('authorized', $request);
        
        
    }
    
    
    public function newAction()
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        
        if (isset($_POST['Domains']['name']) and $_POST['Domains']['name'] != '') {
            
            $domain = $_POST['Domains']['name'];
            
        } else {
            
            header("Location: " . Site . "/domains/");
            exit;
            
        }
        
        $DB = new DB();
        
        $uniq = $DB->selectAnd(array(
            'table' => 'domains',
            'where' => array(
                'domain' => $domain
            )
        ));
        
        if (!$uniq) {
            
            $result = $DB->insert(array(
                'table' => 'domains',
                'set' => array(
                    'uid' => AuthID,
                    'login' => AuthLogin,
                    'domain' => $domain
                )
            ));
            
            if ($result) {
                
                header("Location: " . Site . "/domains/");
                exit;
                
            } else {
                
                header("Location: " . Site . "/domains/");
                exit;
            }
            
        } else {
            
            header("Location: " . Site . "/domains/#error_uniq");
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
            $result = $DB->update(array(
                'table' => 'domains',
                'where' => array(
                    'id' => $id,
                    '1' => '1'
                ),
                'set' => array(
                    'status' => 'Удаление'
                )
            ));
        } else {
            $result = $DB->update(array(
                'table' => 'domains',
                'where' => array(
                    'id' => $id,
                    'uid' => AuthID
                ),
                'set' => array(
                    'status' => 'Удаление'
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
    
    
    
    
    
    
    
    
    
    
}