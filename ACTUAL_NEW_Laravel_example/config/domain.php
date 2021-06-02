<?php

return [
    'cloud_domain' => env("IS_CLOUD_DOMAIN", false),
    'clouds' => [
        'advert_reg_domains' => [
            'gocpa.cloud',
            'dev.gocpa.cloud',
            'cloud.localhost',
            'cloud.local',
            '127.0.0.1',
        ]
    ],
    'env_stub' => '.env',
    'storage_dirs' => [
        'app' => [
            'public' => [
            ],
        ],
        'framework' => [
            'cache' => [
            ],
            'testing' => [
            ],
            'sessions' => [
            ],
            'views' => [
            ],
        ],
        'logs' => [
        ],
    ],
    'domains' => [
        'gocpa.cloud' => 'gocpa.cloud',
        'dev.gocpa.cloud' => 'dev.gocpa.cloud',
        'cloud.localhost' => 'cloud.localhost',
        'cloud.local' => 'cloud.local',
    ],
];
