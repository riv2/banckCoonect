<?php
//настройки полей offers_meta
return [
    'default' => [
        'tag' => [
            'title' => 'Теги',
            'type' => 'list',
            'multiple' => true,
            'options' => [
                'pop' => 'Популярное',
                'rich' => 'Выгодное',
                'new' => 'Новое',
            ],
            'required' => false,
            'display' => true, //отображать партнеру это поле
        ],
        'sources' => [
            'title' => 'Разрешенные источники траффика',
            'type' => 'db_list',
            'model' => \App\Models\TrafficSource::class,
            'key' => 'id',
            'value' => 'title',
            'multiple' => true,
            'options' => [],
            'required' => false,
            'display' => true, //отображать партнеру это поле
        ],
        'geo' => [
            'title' => 'Гео',
            'type' => 'string',
            'required' => false,
            'display' => true, //отображать партнеру это поле
        ],
    ],
];
