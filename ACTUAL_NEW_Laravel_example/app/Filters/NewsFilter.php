<?php

namespace App\Filters;

class NewsFilter extends QueryFilter
{
    public array $fields = [
        'datetime' => [
            'column' => 'datetime',
            'title' => 'Дата',
            'type' => 'month',
            'method' => 'yearmonth',
        ],
        'news_title' => [
            'column' => 'news_title',
            'title' => 'Тема письма',
            'type' => 'text',
            'method' => 'like',
        ],
        'news_text' => [
            'column' => 'news_text',
            'title' => 'Текст',
            'type' => 'text',
            'method' => 'like',
        ],
    ];
}
