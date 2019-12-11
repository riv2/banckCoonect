<?php
class adminFaqController
{
    
    public function indexAction($request)
    {
        if (!AuthID or !AuthLogin or ADMIN != 'true') {
            header("Location: " . Site);
            exit;
        }
        
        require_once(Viewer);
        $view = new viewer();
        
        $request['category'] = 'adminFaq';
        $request['login']    = AuthLogin;
        $request['Site']     = Site;
        $request['LANG']     = LANG;
        
        $DB = new DB();
        
        $request['faq'] = $DB->selectAllAnd(array(
            'table' => 'faq',
            'where' => array(
                '1' => '1'
            )
        ));
        
        
        if ($request['faq'] == false) {
            $request['faq'] = 'none';
        }
        
        echo openCss('faq');
        $view->view('authorized', $request);
        
    }
    
    public function digestAction()
    {
        if (!AuthID or !AuthLogin or ADMIN != 'true') {
            header("Location: " . Site);
            exit;
        }
        
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
        } else {
            $id = '';
        }
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
        } else {
            $action = '';
        }
        if (isset($_POST['subject'])) {
            $subject = $_POST['subject'];
            $subject = str_replace("'", "''", $subject);
        } else {
            $subject = '';
        }
        if (isset($_POST['message'])) {
            $message = $_POST['message'];
            $message = str_replace("'", "''", $message);
        } else {
            $message = '';
        }
        
        if ($action != '' and $subject != '' and $message != '') {
            
            $DB = new DB();
            
            if ($action == 'Добавить') {
                
                $result = $DB->insert(array(
                    'table' => 'faq',
                    'set' => array(
                        'subject' => $subject,
                        'message' => $message
                    )
                ));
                
            }
            if ($action == 'Редактировать') {
                
                $DB->update(array(
                    'table' => 'faq',
                    'where' => array(
                        'id' => $id
                    ),
                    'set' => array(
                        'subject' => $subject,
                        'message' => $message
                    )
                ));
            }
            
            if ($action == 'Удалить') {
                
                $result = $DB->deleteAnd(array(
                    'table' => 'faq',
                    'where' => array(
                        '1' => '1',
                        'id' => $id
                    )
                ));
                
            }
            
            
        }
        
        header("Location: " . Site . "/adminFaq/");
        exit;
        
        
    }
    
    
}