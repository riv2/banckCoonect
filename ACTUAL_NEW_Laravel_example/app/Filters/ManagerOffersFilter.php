<?php

namespace App\Filters;

use App\Lists\OrderStateList;
use Illuminate\Http\Request;

class ManagerOffersFilter extends QueryFilter
{
    public array $fields = [
        'user_id_or_email' => [
            'column' => 'id',
            'title' => 'E-mail/ID партнера',
            'type' => 'text',
            'method' => 'user_id_or_email',
        ],
        'model' => [
            'column' => 'model',
            'title' => 'Модель',
            'type' => 'select',
            'method' => 'equals',
        ],
    ];

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->fields["model"]["options"] = OrderStateList::getList();
    }
}
