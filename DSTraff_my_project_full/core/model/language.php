<?php

class Lang
{
    
    public function getLangArray($lang)
    {
        if ($lang == null) {
            
            
            
            $lang = 'ru';
        }
        
        if ($lang == 'ru') {
            require_once(LangRU);
        } elseif ($lang == 'en') {
            require_once(LangEN);
        } else {
            die('Unknown language');
        }
        
        return $LANG;
        
        
    }
    
}
