<?php
$params = [
    'adminEmail' => 'thejet@ya.ru',
    'amqp' => [
        'host'              => getenv('RABBIT_HOST'),
        'port'              => getenv('RABBIT_PORT'),
        'login'             => getenv('RABBIT_LOGIN'),
        'password'          => getenv('RABBIT_PASSWORD'),
        'connect_timeout'   => 60,
        'write_timeout'     => 60,
    ],
    'db' => [
        'class'               => 'yii\db\Connection',
        'dsn'                 => 'pgsql:host='.getenv('POSTGRES_VHOST').';port='.getenv('POSTGRES_PORT').';dbname='.getenv('POSTGRES_DB'),
        'username'            => getenv('POSTGRES_USER'),
        'password'            => getenv('POSTGRES_PASSWORD'),
        'charset'             => 'utf8',
        'enableSchemaCache'   => true,
        'schemaCacheDuration' => 300,
        'tablePrefix'         => 'prc_',
        'masterConfig'        => [
            'attributes' => [
                PDO::ATTR_TIMEOUT => 60,
            ],
        ],
        'schemaMap'           => [
            'pgsql' => 'app\components\Schema',
        ],
        'on afterOpen'        => function ($event) {
            $event->sender->createCommand("SET TIME ZONE '" . date_default_timezone_get() . "'")->execute();
        }
    ],
    'exchange' => [
        'import'    => [
            'Users'              => 'app\components\exchange\Ldap',
            'PriceFormerTypes'   => 'app\components\exchange\PriceFormer',
            'Brands'             => 'app\components\exchange\ProductHub',
            'Categories'         => 'app\components\exchange\ProductHub',
            'Items'              => 'app\components\exchange\ProductHub',
            'PricesSupply'       => 'app\components\exchange\PriceFormer',
            'PricesVi'           => 'app\components\exchange\PriceFormer',
        ],
        'importLabels'    => [
            'Users'              => 'Пользователи',
            'PriceFormerTypes'   => 'Типы цены',
            'PricesSupply'       => 'Закупочные цены',
            'PricesVi'           => 'Цены ВИ МСК',
            'Brands'             => 'Бренды',
            'Categories'         => 'Рубрики',
            'Items'              => 'Товары',
        ],
        'export'    => [
            'Prices'             => 'app\components\exchange\PriceFormer',
        ],
        'exportLabels'    => [
            'Prices'             => 'Расчитанные цены',
        ],
        'systems'    => [
            'app\components\exchange\PriceFormer' => [
                'importEnabled'         =>  true,
                'exportEnabled'         =>  getenv('PRICE_FORMER_EXPORT') === 'true',
                'url'                   =>  getenv('PRICE_FORMER_URL'),
                'username'              =>  getenv('PRICE_FORMER_USERNAME'),
                'password'              =>  getenv('PRICE_FORMER_PASSWORD'),
                'headers'   => [
                    'Content-Type' => 'application/json',
                    'Accept-Type'  => 'application/json'
                ],
            ],
            'app\components\exchange\ProductHub' => [
                'importEnabled'         => true,
                'exportEnabled'         => false,
                'url'                   => getenv('PHUB_URL'),
                'site_guid'             => getenv('PHUB_SITE_GUID'),
                'username'              => getenv('PHUB_USERNAME'),
                'password'              => getenv('PHUB_PASSWORD'),
                'headers'   => [
                    'Content-Type' => 'application/json',
                    'Accept-Type'  => 'application/json'
                ],
            ],
            'app\components\exchange\Ldap' => [
                'importEnabled'         => true,
                'exportEnabled'         => true,
                'ad_port'               => getenv('LDAP_AD_PORT'),
                'domain_controllers'    => [getenv('LDAP_HOST'),getenv('LDAP_HOST2')],
                'account_suffix'        => getenv('LDAP_ACCOUNT_SUFFIX'),
                'base_dn'               => 'DC=VSEINSTRUMENTI,DC=RU',//getenv('LDAP_BASE_DN'),
                'admin_username'        => getenv('LDAP_ADMIN_USERNAME'),
                'admin_password'        => getenv('LDAP_ADMIN_PASSWORD'),
            ]
        ]
    ],
    'proanalyticsSftp' => [
        'host' => getenv('PROANALYTICS_HOST'),
        'username' => getenv('PROANALYTICS_USERNAME'),
        'password' => getenv('PROANALYTICS_PASSWORD'),
    ],
    'sms' => [
        'login' => '',
        'password' => '',
    ],
];

$local = [];
if (file_exists(__DIR__ . '/params-local.php')) {
    $local = require(__DIR__ . '/params-local.php');
}

$params = yii\helpers\ArrayHelper::merge($params, $local);

return $params;