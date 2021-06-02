<?php

namespace App\Lists;

class OrdersProductStateList
{
    /**
     * Create the event listener.
     *
     * @param string $case
     * @return array
     */
    static function getList($case = 'I'): array
    {
        $arr = [
            'I' => [
                'new' => __('lists.ordersProductStateList.products.I.new'),
                'sale' => __('lists.ordersProductStateList.products.I.sale'),
                'approve' => __('lists.ordersProductStateList.products.I.approve'),
                'reject' => __('lists.ordersProductStateList.products.I.reject'),
            ],
            'V' => [
                'new' => __('lists.ordersProductStateList.products.V.new'),
                'sale' => __('lists.ordersProductStateList.products.V.sale'),
                'approve' => __('lists.ordersProductStateList.products.V.approve'),
                'reject' => __('lists.ordersProductStateList.products.V.reject'),
            ],
        ];

        if (auth()->user()->role == 'manager') {
            //todo придумать что делать с менеджером
            return $arr['I'];
        } else {
            switch ($case) :
                case 'V':
                    return $arr[$case];
                default:
                    return $arr['I'];
            endswitch;
        }
    }
}
