<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class PixelLogFilter extends QueryFilter
{
    public array $fields = [
        'id' => [
            'column' => 'id',
            'title' => 'ID',
            'type' => 'number',
            'method' => 'incomma',
        ],
        'guid' => [
            'column' => 'data->uid',
            'title' => 'GUID',
            'type' => 'text',
            'method' => 'jsonContains',
        ],
        'pp_id' => [
            'column' => 'pp_id',
            'title' => 'PPID',
            'type' => 'number',
            'method' => 'equals',
        ],
        'created_at' => [
            'column' => 'created_at',
            'title' => 'Дата с/по',
            'type' => 'daterange',
            'method' => 'daterange',
        ],
        'utm_medium' => [
            'column' => 'data->utm_medium',
            'title' => 'utm_medium',
            'type' => 'text',
            'method' => 'jsonContains',
        ],
        'utm_source' => [
            'column' => 'data->utm_source',
            'title' => 'utm_source',
            'type' => 'text',
            'method' => 'jsonContains',
        ],
        'is_valid' => [
            'column' => 'is_valid',
            'title' => 'Прошел валидацию',
            'type' => 'select',
            'method' => 'boolean',
            'options' => [
                'null' => 'Не задано',
                'false' => 'Нет',
                'true' => 'Да',
            ],
        ],
        'is_click' => [
            'column' => 'is_click',
            'title' => 'Это переход по ссылке',
            'type' => 'select',
            'method' => 'boolean',
            'options' => [
                'false' => 'Нет',
                'true' => 'Да',
            ],
        ],
        'is_order' => [
            'column' => 'is_order',
            'title' => 'Это продажа',
            'type' => 'select',
            'method' => 'boolean',
            'options' => [
                'false' => 'Нет',
                'true' => 'Да',
            ],
        ],
        'status' => [
            'column' => 'status',
            'title' => 'Обработан в очереди',
            'type' => 'select',
            'method' => 'filledData',
            'options' => [
                'false' => 'Да',
                'true' => 'Нет',
            ],
        ],
    ];
}
