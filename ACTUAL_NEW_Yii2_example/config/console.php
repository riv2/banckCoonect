<?php
use yii\helpers\ArrayHelper;


$common = require(__DIR__ . '/common.php');

if (YII_ENV == 'dev') {
    Yii::setAlias('@tests', dirname(__DIR__) . '/tests/codeception');
}

$config = [
    'id' => 'pricing-console',
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'templateFile' => '@app/components/MigrationTeamplate.php'
        ],
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'errorHandler' => [
            'class'         => 'app\components\ErrorHandlerConsole'
        ],
        'fs' => [
            'class' => 'creocoder\flysystem\FtpFilesystem',
            'host' => 'ftp.proanalytics.net',
            'username' => 'vseinstrumenti',
            'password' => 'vmxu4vu4vsi2',
        ]
    ],
];

$config = ArrayHelper::merge($common, $config);

return $config;
