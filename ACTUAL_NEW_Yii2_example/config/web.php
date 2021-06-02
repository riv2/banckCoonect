<?php
use yii\helpers\ArrayHelper;

$common = require(__DIR__ . '/common.php');

$config = [
    'id' => 'pricing',
    'modules' => [
        'usr' => [
            'class' => 'nineinchnick\usr\Module',
            'requireVerifiedEmail' => false,
            'scenarioViews' => [
                'login' => [
                    'view' => '@app/views/site/login.php'
                ]
            ]
        ],
    ],
    'components' => [
        'errorHandler' => [
            'class'         => 'app\components\ErrorHandlerWeb',
            'errorAction'   => 'site/error',
        ],
        'response' => [
            'formatters' => [
                'csv' => 'netis\crud\web\CsvResponseFormatter',
                'pdf' => 'netis\crud\web\PdfResponseFormatter',
                'xls' => 'netis\crud\web\XlsResponseFormatter',
            ],
        ],
        'request' => [
            'cookieValidationKey' => 'ygFNhP-IHHPyukM2_Xl-NZZXgeYyqyeK',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [],
        ],
        'crudModelsMap' => [
            'class' => 'netis\crud\crud\ModelsMap',
            'data' => [],
        ],
        'view' => [
            'class' => 'netis\crud\web\View',
            'defaultPath' => [
                '@app/views/crud/',
            ],
        ],
    ],
    'controllerMap' => [
        'reference' => [
            'class'             => 'app\controllers\ReferenceController',
        ]
    ]
];

$entities = [
    'app\models\reference\User',
    'app\models\enum\Status',
    'app\models\enum\Source',
    'app\models\reference\Brand',
    'app\models\reference\Category',
    'app\models\reference\PriceFormerType',
    'app\models\enum\Region',
    'app\models\reference\Competitor',
    'app\models\reference\Item',
    'app\models\reference\CompetitorItem',
    'app\models\enum\SelectPriceLogic',
    'app\models\enum\PriceExportMode',
    'app\models\enum\CompetitionMode',
    'app\models\cross\CategoryItem',
    'app\models\pool\PriceParsed',
    'app\models\pool\PriceRefined',
    'app\models\enum\ProjectExecutionStatus',
    'app\models\reference\Project',
    'app\models\reference\ProjectItem',
    'app\models\reference\ProjectCompetitor',
    'app\models\reference\ProjectCompetitorBrand',
    'app\models\reference\ProjectCompetitorCategory',
    'app\models\document\ProjectExecution',
    'app\models\cross\ProjectSource',
    'app\models\pool\PriceCalculated',
    'app\models\pool\LogProjectExecution',
    'app\models\pool\LogPriceCalculation',
    'app\models\cross\CategoryCategory',
    'app\models\enum\ErrorType',
    'app\models\register\Error',
    'app\models\reference\ExchangeSystem',
    'app\models\register\ExchangeImport',
    'app\models\register\ExchangeExport',
    'app\models\SqlogError',
    'app\models\reference\FileExchangeSettings',
    'app\models\register\FileExchange',
    'app\models\enum\FileFormat',
    'app\models\reference\CompetitorShopName',
    'app\models\reference\CompetitorShopDomain',
    'app\models\reference\CompetitorShopIndex',
    'app\models\register\Task',
    'app\models\enum\TaskStatus',
    'app\models\enum\TaskType',
    'app\models\reference\ReportKeywordsControl',
    'app\models\pool\ReportCalculationOverview',
    'app\models\reference\ProjectTheme',
    'app\models\reference\JournalSettings',
    'app\models\reference\ParsingProject',
    'app\models\register\Parsing',
    'app\models\cross\ParsingProjectRegion',
    'app\models\reference\Schedule',
    'app\models\pool\ParsingBuffer',
    'app\models\enum\PriceParsedStatus',
    'app\models\pool\Screenshot',
    'app\models\reference\Masks',
    'app\models\reference\Robot',
    'app\models\pool\ParsingError',
    'app\models\register\AntiCaptchaTask',
    'app\models\enum\HoradricCubeStatus',
    'app\models\register\HoradricCube',
    'app\models\reference\BrandFilter',
    'app\models\register\FileProcessing',
    'app\models\reference\FileProcessingSettings',
    'app\models\pool\LogKpi',
    'app\models\reference\Role',
    'app\models\reference\Vpn',
    'app\models\reference\NomenclatureDocument',
    'app\models\pool\NomenclatureDocumentItem',
    'app\models\enum\ConsoleTaskStatus',
    'app\models\enum\ConsoleTaskType',
    'app\models\reference\Setting',
    'app\models\pool\ProjectChart',
];

foreach ($entities as $class) {
    $path       = str_ireplace('app\models\\','', $class);
    $names      = explode('\\', $path);
    $shortName  = end($names);
    $action = 'crud-'.\yii\helpers\Inflector::camel2id($shortName);
    $config['controllerMap'][$action] = [
        'class'             => 'app\components\crud\controllers\ActiveController',
        'modelClass'        => $class,
        'searchModelClass'  => $class
    ];
    $config['components']['crudModelsMap']['data'][$class] = '/'.$action;
}

$config['components']['crudModelsMap']['data']['app\models\reference\Project'] = '/project';
$config['components']['crudModelsMap']['data']['app\models\reference\Competitor'] = '/competitor';
$config['components']['crudModelsMap']['data']['app\models\register\FileProcessing'] = '/file';
$config['components']['crudModelsMap']['data']['app\models\reference\FileProcessingSettings'] = '/file-settings';
$config['components']['crudModelsMap']['data']['app\models\pool\LogPriceCalculation'] = '/log-price-calculation';
$config['components']['crudModelsMap']['data']['app\models\reference\ExchangeSystem'] = '/exchange-system';
$config['components']['crudModelsMap']['data']['app\models\register\Task'] = '/task';
$config['components']['crudModelsMap']['data']['app\models\reference\ParsingProject'] = '/parsing-project';
$config['components']['crudModelsMap']['data']['app\models\reference\Masks'] = '/masks';
$config['components']['crudModelsMap']['data']['app\models\reference\Robot'] = '/robot';
$config['components']['crudModelsMap']['data']['app\models\enum\Region'] = '/region';
$config['components']['crudModelsMap']['data']['app\models\register\HoradricCube'] = '/horadric-cube';
$config['components']['crudModelsMap']['data']['app\models\reference\User'] = '/users';
$config['components']['crudModelsMap']['data']['app\models\reference\CompetitorItem'] = '/competitor-item';
$config['components']['crudModelsMap']['data']['app\models\reference\ProjectCompetitor'] = '/project-competitor';
$config['components']['crudModelsMap']['data']['app\models\register\Error'] = '/error';
$config['components']['crudModelsMap']['data']['app\models\reference\Role'] = '/roles';
$config['components']['crudModelsMap']['data']['app\models\reference\Vpn'] = '/vpn';
$config['components']['crudModelsMap']['data']['app\models\reference\NomenclatureDocument'] = '/nomenclature-document';
$config['components']['crudModelsMap']['data']['app\models\pool\NomenclatureDocumentItem'] = '/nomenclature-document-item';
$config['components']['crudModelsMap']['data']['app\models\pool\ProjectChart'] = '/project-chart';


if (getenv('YII_DEBUG')) {
    $config['components']['log'] = [
        'targets' => [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error', 'warning'],
            ],
        ],
    ];
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];
}

$local = [];
if (file_exists(__DIR__ . '/web-local.php')) {
    $local = require(__DIR__ . '/web-local.php');
}

$config = ArrayHelper::merge($common, $config, $local);

return $config;
