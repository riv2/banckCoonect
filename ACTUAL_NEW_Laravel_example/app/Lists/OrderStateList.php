<?php
/**
 * Project qpartners
 * Created by danila 26.06.2020 @ 12:04
 */

namespace App\Lists;


class OrderStateList
{
    static function getList($case = 'I'): array
    {
        $arr = [
            'lead' => [
                'I' => [
                    'new' => __('lists.orderStateList.lead.I.new'),
                    'sale' => __('lists.orderStateList.lead.I.sale'),
                    'reject' => __('lists.orderStateList.lead.I.reject'),
                ],
                'V' => [
                    'new' => __('lists.orderStateList.lead.V.new'),
                    'sale' => __('lists.orderStateList.lead.V.sale'),
                    'reject' => __('lists.orderStateList.lead.V.reject'),
                ],

            ],
            'products' => [
                'I' => [
                    'new' => __('lists.orderStateList.products.I.new'),
                    'sale' => __('lists.orderStateList.products.I.sale'),
                    'reject' => __('lists.orderStateList.products.I.reject'),
                ],
                'V' => [
                    'new' => __('lists.orderStateList.products.V.new'),
                    'sale' => __('lists.orderStateList.products.V.sale'),
                    'reject' => __('lists.orderStateList.products.V.reject'),
                ],
            ],
        ];

        if (auth()->user()->role == 'manager') {
            //todo придумать что делать с менеджером
            return $arr['lead']['I'];

        } else {
            switch ($case):
                case 'V':
                    return $arr[auth()->user()->pp->pp_target][$case];
                default:
                    return $arr[auth()->user()->pp->pp_target]['I'];
            endswitch;
        }

    }
}
