<?php
ini_set("max_execution_time", "160");
error_reporting(E_ALL);
//error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

require(__DIR__ . '/../vendor/autoload.php');


require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();
