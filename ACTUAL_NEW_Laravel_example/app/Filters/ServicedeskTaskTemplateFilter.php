<?php

namespace App\Filters;

use App\Models\ServicedeskTask;
use Illuminate\Http\Request;

class ServicedeskTaskTemplateFilter extends QueryFilter
{
    public array $fields = [
        'subject' => [
            'column' => 'subject',
            'title' => 'Название',
            'type' => 'search',
            'method' => 'like',
        ],
        'body' => [
            'column' => 'body',
            'title' => 'Текст',
            'type' => 'search',
            'method' => 'like',
        ],
    ];
}
