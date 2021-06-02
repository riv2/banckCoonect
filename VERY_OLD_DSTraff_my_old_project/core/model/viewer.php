<?php
class viewer
{
    
    public function view($viewTemplate, $request)
    {
        
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/core/view/' . $viewTemplate . '.php')) {
            require_once(DefaultSmarty);
            require_once($_SERVER['DOCUMENT_ROOT'] . '/core/view/' . $viewTemplate . '.php');
        } else {
            require_once(DefaultSmarty);
            require_once($_SERVER['DOCUMENT_ROOT'] . '/core/view/error.php');
        }
    }
}