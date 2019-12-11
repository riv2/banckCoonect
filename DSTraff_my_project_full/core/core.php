<?php
require_once('protected/conf.php');
require_once(modelDB);
require_once(Session);
require_once(Functions);
require_once(Language);
require_once(Dump);

$A    = new Session;
$AUTH = $A->getSession();

unset($A);

define('AuthID', $AUTH['id']);
define('AuthLogin', $AUTH['login']);
define('AuthTheme', $AUTH['theme']);
define('AuthTips', $AUTH['tips']);

if (in_array($AUTH['id'], $ADMINS)) {
    define('ADMIN', 'true');
} else {
    define('ADMIN', 'false');
}

$L    = new Lang;
$LANG = $L->getLangArray($AUTH['lang']);
define('LANG', $LANG);

unset($AUTH);
unset($ADMINS);
unset($L);

$request = array();

if (isset($_REQUEST['route'])) {
    
    $tmp = explode('/', $_REQUEST['route']);
    
    $request = $tmp;
    
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/core/controller/' . $tmp[0] . 'Controller.php')) {
        $controller = 'controller/' . $tmp[0] . 'Controller.php';
        $class      = $tmp[0] . 'Controller';
        unset($request[0]);
    } else {
        $controller = defaultController;
        $class      = 'indexController';
    }
    
    if (isset($tmp[1])) {
        $action = $tmp[1] . 'Action';
    } else {
        $action = 'indexAction';
    }
    
} else {
    $controller = defaultController;
    $class      = 'indexController';
    $action     = 'indexAction';
    $request    = array();
    
}

require_once($controller);
$output = new $class;

if (method_exists($output, $action)) {
    unset($request[1]);
    
    $output->$action($request);
    
} else {
    
    $output->indexAction($request);
}

