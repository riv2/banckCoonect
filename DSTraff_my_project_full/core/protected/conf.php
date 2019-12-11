<?php 
error_reporting (E_ALL);
$ADMINS = array('1', '434');
// Derictives
define ('smartyDIR', $_SERVER['DOCUMENT_ROOT'].'/core/smarty/Smarty.class.php');
define ('DefaultSmarty', $_SERVER['DOCUMENT_ROOT'].'/core/model/smatryDefault.php');
define ('tlpDir', $_SERVER['DOCUMENT_ROOT'].'/core/view/tpl');
define ('tlpCache', $_SERVER['DOCUMENT_ROOT'].'/var/tmp');
define ('authorizedCss', '/public/css/authorized.css');
define ('notauthorizedCss', '/public/css/notauthorized.css');
define ('normalize', '/public/css/normalize.css');
define ('sweetCss', '/public/css/sweet.css');
define ('sweetJs', '/public/js/sweet.js');
define ('initJs', '/public/js/init.js');
define ('tipsJs', '/public/js/tips.js');
define ('notauthorizedJs', '/public/js/notauthorized.js');
define ('jQuery', '/public/js/jquery-2.1.3.min.js');
define ('defaultController', 'controller/indexController.php');
define ('mainDir', $_SERVER['DOCUMENT_ROOT'].'/');
define ('Site', 'https://tds.i-cdm.ru');
define ('SiteName', 'tds.i-cdm');
define ('confDB', $_SERVER['DOCUMENT_ROOT'].'/core/protected/confDB.php');

//Models
define ('Viewer', $_SERVER['DOCUMENT_ROOT'].'/core/model/viewer.php');
define ('Auth', $_SERVER['DOCUMENT_ROOT'].'/core/model/auth.php');
define ('modelDB', $_SERVER['DOCUMENT_ROOT'].'/core/model/db.php');
define ('Session', $_SERVER['DOCUMENT_ROOT'].'/core/model/session.php');
define ('Mailer', $_SERVER['DOCUMENT_ROOT'].'/core/model/mailer.php');
define ('Smtp', $_SERVER['DOCUMENT_ROOT'].'/core/model/SendMailSmtpClass.php');
define ('Imager', $_SERVER['DOCUMENT_ROOT'].'/core/model/SimpleImage.php');
define ('Tracker', $_SERVER['DOCUMENT_ROOT'].'/core/model/tracker.php');
define ('Functions', $_SERVER['DOCUMENT_ROOT'].'/core/model/functions.php');
define ('Css', $_SERVER['DOCUMENT_ROOT'].'/core/model/css.php');
define ('Dump', $_SERVER['DOCUMENT_ROOT'].'/core/model/dump.php');
define ('Mobile', $_SERVER['DOCUMENT_ROOT'].'/core/model/mobile.php');
define ('PHPExcel', $_SERVER['DOCUMENT_ROOT'].'/core/model/PHPExcel.php');
define ('Language', $_SERVER['DOCUMENT_ROOT'].'/core/model/language.php');
define ('LangRU', $_SERVER['DOCUMENT_ROOT'].'/core/view/lang/ru.php');
define ('LangEN', $_SERVER['DOCUMENT_ROOT'].'/core/view/lang/en.php');

//System
define ('passKey', 'G5A8Q5M2V5G2f7'); //IMPORTANT!!!
define ('regToForum', 'false'); //true /false
define ('countInvites', '1');
define ('itemsInAggregatorStorage', '9000000'); 
define ('logDBOn', 'false'); //true /false
define ('logAuthOn', 'true'); //true /false
define ('logAuthPassOn', 'true'); //true /false
define ('topLimit', '10'); 
define ('Version', ' (v 3.0.2)'); 














































