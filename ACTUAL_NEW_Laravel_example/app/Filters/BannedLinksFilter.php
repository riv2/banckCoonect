<?php

namespace App\Filters;

class BannedLinksFilter extends QueryFilter
{
    public array $fields = [
        'date_start' => [
            'column' => 'date_start',
            'title' => 'Дата начала',
            'type' => 'date',
            'method' => 'gte',
        ],
        'date_end' => [
            'column' => 'date_end',
            'title' => 'Дата окончания',
            'type' => 'date',
            'method' => 'lte',
        ],
        'web_id' => [
            'column' => 'web_id',
            'title' => 'web_id',
            'type' => 'text',
            'method' => 'like',
        ],
        'link_id' => [
            'column' => 'link_id',
            'title' => 'Ссылка',
            'type' => 'number',
            'method' => 'equals',
        ],
        'comment' => [
            'column' => 'comment',
            'title' => 'Комментарий',
            'type' => 'text',
            'method' => 'like',
        ],
        'evidence' => [
            'column' => 'evidence',
            'title' => 'Доказательства',
            'type' => 'text',
            'method' => 'like',
        ],
        'id' => [
            'column' => 'id',
            'title' => 'ID',
            'type' => 'number',
            'method' => 'equals',
        ]
    ];
}
