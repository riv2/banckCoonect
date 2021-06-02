<?php

defined('ANON_URL') or define('ANON_URL', '');
defined('YII_DEBUG') or define('YII_DEBUG', getenv('YII_DEBUG') ? true : false);
defined('YII_ENV') or define('YII_ENV', getenv('YII_ENV'));
defined('YII_ENV_DEV') or define('YII_ENV_DEV', YII_ENV === 'dev');
date_default_timezone_set('Europe/Moscow');

$params = require(__DIR__ . '/params.php');

$config = [
    'name' => 'Pricing',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [],
    'timeZone' => 'Europe/Moscow',
    'language' => 'ru-RU',
    'aliases' => [
        '@nineinchnick/usr' => '@vendor/nineinchnick/yii2-usr',
        '@netis/crud' => '@app/netis/yii2-crud',
        '@netis/rbac' => '@app/netis/yii2-relauth',
        '@netis' => '@app/netis',
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'templateFile' => '@app/components/MigrationTeamplate.php'
        ],
    ], 
    'components' => [
        'assetManager' => [
            'appendTimestamp' => true,
            'linkAssets' => true,
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'sourceLanguage' => 'en-US',
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                ],
            ],
        ],
        'authManager' => [
            'class' => 'app\components\DbManager',
            'cache' => 'cache',
        ],
        'cache'         => [
            'class'        => 'yii\caching\MemCache',
            'useMemcached' => true,
            'servers'      => [
                [
                    'host' => '127.0.0.1',
                    'port' => 11211,
                ],
            ],
        ],
        'user' => [
            'identityClass'     => 'app\models\reference\User',
            'class'             => 'app\components\User',
            'loginUrl'          => ['usr/login'],
            'enableAutoLogin'   => true,
        ],
        'session'      => [
            'class'     => 'app\components\Session',
            'keyPrefix' => 'prc:session:',
        ],
        'redis'         => [
            'class'    => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port'     => 6379,
            'database' => 0,
        ],
        'ws'            => [
            'class' => 'app\modules\ws\components\Manager',
        ],
        'formatter' => [
            'timeZone' => 'Europe/Minsk',
            'class'      => 'app\components\crud\Formatter',
            'dateFormat' => 'dd-MM-yyyy',
            'datetimeFormat' => 'dd-MM-yyyy HH:mm:ss',
            'nullDisplay' => '',
            'currencyFormat' => '{value}&nbsp;{currency}',
            'thousandSeparator' => ' ',
        ],
        'mailer' => [
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'dev.email.ct@gmail.com',
                'password' => 'vo6cye6b',
                'encryption' => 'ssl',
                'port' => '465',
            ],
        ],
        'ldap' => [
            'class'=>'Edvlerblog\Ldap',
            'options'=> $params['exchange']['systems']['app\components\exchange\Ldap']
        ],
        'db'        => $params['db'],
    ],
    'params' => $params
];

return $config;