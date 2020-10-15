<?php
class newsController
{
    
    public function indexAction($request)
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        
        require_once(Viewer);
        $view                = new viewer();
        $request['Site']     = Site;
        $request['category'] = 'news';
        $request['login']    = AuthLogin;
        $request['LANG']     = LANG;
        
        $DB = new DB();
        
        $DB->update(array(
            'table' => 'users',
            'where' => array(
                'id' => AuthID
            ),
            'set' => array(
                'newNews' => 'N'
            )
        ));
        
        $request['news'] = $DB->selectAllAnd(array(
            'table' => 'news',
            'where' => array(
                '1' => '1'
            )
        ));
        
        if ($request['news'] == false) {
            $request['news'] = 'none';
        }
        
        echo openCss('news');
        $view->view('authorized', $request);
    }
    
}