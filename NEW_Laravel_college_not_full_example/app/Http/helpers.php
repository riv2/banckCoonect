<?php

use App\Settings;
use App\User;
use App\Properties;
use App\Types;
use Illuminate\Support\Facades\{Log};
 
if (! function_exists('getcong')) {

    function getcong($key)
    {
    	 
        $settings = Settings::findOrFail('1');

        if( ($key == 'terms_conditions_description') ||
            ($key == 'terms_conditions_description_kz') ||
            ($key == 'terms_conditions_description_en')
        )
        {
            switch( app()->getLocale() )
            {
                case 'ru':
                    return $settings->terms_conditions_description;
                break;
                case 'kz':
                    return $settings->terms_conditions_description_kz;
                break;
                case 'en':
                    return $settings->terms_conditions_description_en;
                break;
                default:
                    return $settings->terms_conditions_description;
                break;
            }

        }

        if( ($key == 'agitator_terms_conditions_description') ||
            ($key == 'agitator_terms_conditions_description_kz') ||
            ($key == 'agitator_terms_conditions_description_en')
        )
        {
            switch( app()->getLocale() )
            {
                case 'ru':
                    return $settings->agitator_terms_conditions_description;
                    break;
                case 'kz':
                    return $settings->agitator_terms_conditions_description_kz;
                    break;
                case 'en':
                    return $settings->agitator_terms_conditions_description_en;
                    break;
                default:
                    return $settings->agitator_terms_conditions_description;
                    break;
            }

        }


        return $settings->$key;


    }
}

if (!function_exists('htmlClearfromMsTags')) {
    function htmlClearfromMsTags($value)
    {
        $value = preg_replace('/<!--\[if gte mso 9\]>(.*?)<!\[endif\]-->/s', "", $value);
        $value = preg_replace('/<!--\[if gte mso 10\]>(.*?)<!\[endif\]-->/s', "", $value);
        return $value;
    }
}
 
if (!function_exists('classActivePath')) {
    function classActivePath($path)
    {
        $first = explode('/', request()->getPathInfo());
        return isset($first[1]) && $first[1] == $path ? 'active' : '';
        /*$segment = 1;
        foreach($path as $p) {
            if((request()->segment($segment) == $p) == false) {
                return '';
            }
            $segment++;
        }
        return ' active';*/
    }
}

if (!function_exists('classActivePathPublic')) {
    function classActivePathPublic($path)
    {
        $path = explode('.', $path);
        $segment = 1;
        
	    //Если URL (где нажали на переключение языка) содержал корректную метку языка
	    if (in_array(request()->segment(1), App\Http\Middleware\LocaleMiddleware::$languages)) {
	        $segment++;
	    } 
        
        if(request()->segment($segment) == $path[0]) {
            return ' active';
        }
        
        return '';
        
    }
}

if (!function_exists('classActlinkPathPublic')) {
    function classActlinkPathPublic($path)
    {
        $path = explode('.', $path);
        $segment = 1;
        
	    //Если URL (где нажали на переключение языка) содержал корректную метку языка
	    if (in_array(request()->segment(1), App\Http\Middleware\LocaleMiddleware::$languages)) {
	        $segment++;
	    } 
        
        if(request()->segment($segment) == $path[0]) {
            return ' act-link';
        }
        
        return '';
        
    }
}

if (! function_exists('getLangURI')) {
	function getLangURI($lang) 
	{ 
		$referer = Request::url(); //URL страницы
	    $parse_url = parse_url($referer, PHP_URL_PATH); //URI страницы
	
	    //разбиваем на массив по разделителю
	    $segments = explode('/', $parse_url);
	
	    //Если URL (где нажали на переключение языка) содержал корректную метку языка
	    if (in_array($segments[1], App\Http\Middleware\LocaleMiddleware::$languages)) {
	
	        unset($segments[1]); //удаляем метку
	    } 
	    
	    //Добавляем метку языка в URL (если выбран не язык по-умолчанию)
	    if ($lang != App\Http\Middleware\LocaleMiddleware::$mainLanguage){ 
	        array_splice($segments, 1, 0, $lang); 
	    }
	
	    //формируем полный URL
	    $url = Request::root().implode("/", $segments);
	    
	    //если были еще GET-параметры - добавляем их
	    if(parse_url($referer, PHP_URL_QUERY)){    
	        $url = $url.'?'. parse_url($referer, PHP_URL_QUERY);
	    }
	    return $url;
	}
}

