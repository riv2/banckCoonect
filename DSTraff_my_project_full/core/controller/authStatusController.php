<?php

class authStatusController
{
    
    public function indexAction($request)
    {
        if (!AuthID or !AuthLogin) {
            echo 'Not Auth';
            exit;
        } else {
            echo 'Auth';
            exit;
        }
        
        
    }
    
    
}