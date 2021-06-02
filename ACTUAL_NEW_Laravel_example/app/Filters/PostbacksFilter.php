<?php

namespace App\Filters;

use App\Lists\UsersTypesList;
use App\Models\Offer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PostbacksFilter extends QueryFilter
{
    public array $fields = [
        'order_id' => [
            'column' => 'order_id',
            'title' => 'ID',
            'type' => 'text',
            'method' => 'equals',
        ],
        'offer_id' => [
            'column' => 'offer_id',
            'title' => 'Offer',
            'type' => 'select',
            'method' => 'equals',
        ],
        'date_from' => [
            'column' => 'datetime',
            'title' => 'Дата',
            'type' => 'date',
            'method' => 'gte',
        ],
        'date_to' => [
            'column' => 'datetime',
            'title' => 'Дата',
            'type' => 'date',
            'method' => 'lte',
        ],
    ];

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->fields["offer_id"]["options"] = Offer::getOwnOffers(auth()->user())
            ->pluck("offer_name", "id")
            ->toArray();
    }
}
