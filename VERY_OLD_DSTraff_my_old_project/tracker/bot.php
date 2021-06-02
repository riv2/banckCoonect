<?php
function whobot()
{
    $bot = 'not_bot';
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
        $bot = '--';
        return $bot;
    }
    if (!$_SERVER['HTTP_USER_AGENT'] || $_SERVER['HTTP_USER_AGENT'] == ' ') {
        $bot = '---';
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Yandex')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Google')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Accoona')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'ia_archiver')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Jeeves')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'curl')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'EltaIndexer')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'baidu')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'crawler')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'W3C_Validator')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Wget')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'WebAlta')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Yahoo')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Rambler')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Ask')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Turtle')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Nigma')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Robot')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'proximic')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'bot')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'mail')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'spider')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Bond')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    }
    //if ( stristr($_SERVER['HTTP_USER_AGENT'], 'YaBrowser') ) {$bot=$_SERVER['HTTP_USER_AGENT'];}
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Mozilla/5.0 (Linux; U; Android 3.1; en-us; GT-P7510 Build/HMJ37) AppleWebKit/534.13 (KHTML, like Gecko) Version/4.0 Safari/534.13')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    } #Dr.Web
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Mozilla/5.0 (Windows; U; Windows NT 5.1; fr; rv:1.9.2b5) Gecko/20091204 Firefox/3.6b5')) {
        $bot = $_SERVER['HTTP_USER_AGENT'];
    } #Dr.Web
    return $bot;
}
