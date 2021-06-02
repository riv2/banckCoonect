<?php
function prepareStr($str)
{
    return str_replace(array(
        "\\",
        "\0",
        "'",
        "\x1a",
        "<script>",
        "</script>"
    ), array(
        "\\",
        "\\0",
        "\\'",
        "\\Z",
        "",
        ""
    ), $str);
}


function newInvite()
{
    
    return md5((rand(1, 9999999) . rand(1, 9999999) . rand(1, 9999999) . rand(1, 9999999) . rand(1, 9999999)));
    
}

function logErrorDB($file, $error)
{
    $LOG = new DB();
    $LOG->insert(array(
        'table' => 'logerror',
        'set' => array(
            'file' => $file,
            'error' => $error
        )
    ));
    unset($LOG);
}


function logText($log)
{
    
    if (gettype($log) == 'array') {
        $logContent = '';
        foreach ($log as $key => $value) {
            $logContent .= $key . '=>' . $value . PHP_EOL;
        }
    } else {
        $logContent = $log;
    }
    
    $log = $_SERVER['DOCUMENT_ROOT'] . "/var/log/" . date('d-m-y|h:i:s') . ".log";
    $fp  = fopen($log, "w"); // ("r" - считывать "w" - создавать "a" - добовлять к тексту), мы создаем файл
    fwrite($fp, $logContent);
    fclose($fp);
}


function openCss($css)
{
    
    if (!AuthTheme) {
        if (isset($_COOKIE['theme']) and $_COOKIE['theme'] != '') {
            $theme = prepareStr($_COOKIE['theme']);
        } else {
            $theme = 'blue';
        }
    } else {
        $theme = AuthTheme;
        if (!isset($_COOKIE['theme'])) {
            error_reporting(0);
            setcookie('theme', $theme, time() + 9999999, '/', 'vizortab.ru', FALSE, TRUE);
        }
    }
    
    if ($theme == 'blue') {
        $color_1 = 'rgba(0,104,139,0.9)';
    } else if ($theme == 'red') {
        $color_1 = 'rgba(255,10,10,0.9)';
    } else if ($theme == 'green') {
        $color_1 = 'rgba(0,139,69,0.9)';
    } else if ($theme == 'grey') {
        $color_1 = 'rgba(100,100,100,0.9)';
    } else if ($theme == 'orange') {
        $color_1 = 'rgba(249,154,38,0.9)';
    } else if ($theme == 'cyan') {
        $color_1 = 'rgba(0,139,139,0.9)';
    } else if ($theme == 'peru') {
        $color_1 = 'rgba(205,133,63,0.9)';
    } else if ($theme == 'orchid') {
        $color_1 = 'rgba(218,112,214,0.9)';
    } else if ($theme == 'brown') {
        $color_1 = 'rgba(139,35,35,0.9)';
    } else if ($theme == 'black') {
        $color_1 = 'rgba(0,0,0,0.9)';
    } else {
        $color_1 = 'rgba(249,154,38,0.9)';
    }
    
    $cssLine = file($_SERVER['DOCUMENT_ROOT'] . '/public/css/' . $css . '.css');
    
    echo '<style>';
    foreach ($cssLine as $line) {
        $line = str_replace('rgba(249,154,38,0.7)', $color_1, $line); // основной
        
        echo $line;
    }
    echo '</style>';
    
}

function unique_multidim_array($array, $key) { 
    $temp_array = array(); 
    $i = 0; 
    $key_array = array(); 
    
    foreach($array as $val) { 
        if (!in_array($val[$key], $key_array)) { 
            $key_array[$i] = $val[$key]; 
            $temp_array[$i] = $val; 
        } 
        $i++; 
    } 
    return $temp_array; 
}








