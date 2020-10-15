<?php
class faqController
{
    
    public function indexAction($request)
    {
        if (!AuthID or !AuthLogin) {
            header("Location: " . Site);
            exit;
        }
        
        $DB = new DB();
        
        require_once(Viewer);
        $view                = new viewer();
        $request['category'] = 'faq';
        $request['login']    = AuthLogin;
        $request['Site']     = Site;
        $request['LANG']     = LANG;
        
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
    
}