<?php

namespace App\Filters;

class BannedFraudFilter extends QueryFilter
{
    public array $fields = [
        'order_id' => [
            'column' => 'order_id',
            'title' => 'ID заявки',
            'type' => 'text',
            'method' => 'equals',
        ],
        'offer_id' => [
            'column' => 'offer_id',
            'title' => 'Offer ID ',
            'type' => 'text',
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
    ];
}
