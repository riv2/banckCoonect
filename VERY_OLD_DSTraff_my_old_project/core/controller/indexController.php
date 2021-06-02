<?php
class indexController
{
    
    public function indexAction($request)
    {
        
        require_once(Viewer);
        $view = new viewer();
        //dump (LANG);
        if (!AuthID or !AuthLogin) {
            require_once(Mobile);
            $Modile = new Mobile_Detect;
            if ($Modile->isMobile() == true) {
                $is_modile = 'TRUE';
            } else {
                $is_modile = 'FALSE';
            }
            $request['category']  = 'index';
            $request['is_modile'] = $is_modile;
            $request['LANG']      = LANG;
            $view->view('notauthorized', $request);
            
        } else {
            
            $request['login']    = AuthLogin;
            $request['category'] = 'index';
            $request['Site']     = Site;
            $request['LANG']     = LANG;
            $buffer              = $_SERVER['DOCUMENT_ROOT'] . "/var/tmp/stat_index.tmp";
            $buffer_admin        = $_SERVER['DOCUMENT_ROOT'] . "/var/tmp/stat_index_admin.tmp";
            $buffer_all          = $_SERVER['DOCUMENT_ROOT'] . "/var/tmp/stat_index_all.tmp";
            $buffer_admin_all    = $_SERVER['DOCUMENT_ROOT'] . "/var/tmp/stat_index_admin_all.tmp";
            
            $file_stat           = fopen($buffer, "r");
            $file_stat_admin     = fopen($buffer_admin, "r");
            $file_stat_all       = fopen($buffer_all, "r");
            $file_stat_admin_all = fopen($buffer_admin_all, "r");
            
            if (!$file_stat) {
                $request['statistic']     = '';
                $request['statistic_all'] = '';
            } else {
                $buff                     = fread($file_stat, 2000);
                $buff_all                 = fread($file_stat_all, 2000);
                $request['statistic']     = $buff;
                $request['statistic_all'] = $buff_all;
            }
            
            if (ADMIN == 'true') {
                if (!$file_stat) {
                    $request['admin_stat'] = '';
                } else {
                    $buff                      = fread($file_stat_admin, 2000);
                    $buff_all                  = fread($file_stat_admin_all, 2000);
                    $request['admin_stat']     = $buff;
                    $request['admin_stat_all'] = $buff_all;
                }
            } else {
                $request['admin_stat']     = '';
                $request['admin_stat_all'] = '';
            }
            
            echo openCss('index');
            $view->view('authorized', $request);
            
        }
        
        
        
    }
    
    
}
