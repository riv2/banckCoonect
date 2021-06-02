<?php

namespace App\Filters;

use App\Helpers\DataBaseHelper;
use App\Lists\OrderStatusList;
use App\Models\Offer;
use Carbon\Carbon;
use App\User;
use Illuminate\Http\Request;

class OrdersFilter extends QueryFilter
{
    public array $fields = [];

    public function __construct(Request $request)
    {
        $this->fields = [
            'order_id' => [
                'column' => 'order_id',
                'title' => 'ID',
                'type' => 'text',
                'method' => 'equals',
            ],
            'offer_id' => [
                'column' => 'offer_id',
                'title' => __('widgets.filter_fields.form.offer'),
                'type' => 'select',
                'method' => 'equals',
                'options' => [],
            ],
            'date' => [
                'column' => 'datetime',
                'title' => __('widgets.filter_fields.form.date-from-to'),
                'type' => 'daterange',
                'method' => 'daterange',
            ],
            'status' => [
                'column' => 'status',
                'title' => __('widgets.filter_fields.form.status'),
                'type' => 'select',
                'method' => 'equals',
                'options' => [],
            ],
            'click_id' => [
                'column' => 'click_id',
                'title' => 'Click ID',
                'type' => 'text',
                'method' => 'equals',
            ],
            'web_id' => [
                'column' => 'web_id',
                'title' => 'Web ID',
                'type' => 'text',
                'method' => 'equals',
            ],
            'link_id' => [
                'column' => 'link_id',
                'title' => 'Link ID',
                'type' => 'text',
                'method' => 'equals',
            ],
            'reestr_id' => [
                'column' => 'reestr_id',
                'title' => 'Reestr ID',
                'type' => 'text',
                'method' => 'equals',
            ],
            'in_reestr' => [
                'column' => 'reestr_id',
                'title' => __('widgets.filter_fields.form.in-reestr'),
                'type' => 'checkbox',
                'method' => 'filledData',
            ],

        ];

        if (auth()->user()->role !== 'partner') {
            $this->fields['partner_id'] = [
                'column' => 'partner_id',
                'title' => __('widgets.filter_fields.form.partner-id'),
                'type' => 'select',
                'method' => 'user_id_or_email',
                'options' => [],
            ];
            $this->fields['partner_name'] = [
                'column' => 'partner_id',
                'title' => __('widgets.filter_fields.form.partner-name'),
                'type' => 'select',
                'method' => 'user_id_or_email',
                'options' => [],
            ];

            $this->fields['partner_id']['options'] = User::getPartners(auth()->user())->pluck('id', 'id');
            $this->fields['partner_name']['options'] = User::getPartners(auth()->user())->pluck('name', 'id');
        }
        $this->fields['status']['options'] = OrderStatusList::getList();
        $this->fields["offer_id"]["options"] = Offer::getOwnOffers(auth()->user())->pluck("offer_name", "id");
        parent::__construct($request);
    }
}
