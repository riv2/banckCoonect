<?php

/**
 * Project laravel
 * Created by danila 15.05.20 @ 7:05
 */

namespace App\Console\Commands\Checkers;

use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\PixelLog;
use App\Models\Pp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CheckerGrossAmountInOrders extends CheckerTemplate
{
    protected $signature = 'checker:GrossAmountInOrders';

    protected array $ppIds = [
        79,
    ];

    public function doCheck()
    {
        //Partner Programs with pp_target equals to products
        $pps = Pp::query()->where('pp_target', '=', 'products')->whereIn('id', $this->ppIds)->get();
        $this->errors = [];
        foreach ($pps as $pp) {
            $orders = Order::query()->where('pp_id', '=', $pp->id)->get();
            foreach ($orders as $order) {
                $ordersProducts = OrdersProduct::query()->where('order_id', '=', $order->order_id)->get();
                //Sum of multiplication of price and quantity in orders_products
                $grossAmount = 0;
                foreach ($ordersProducts as $ordersProduct) {
                    $grossAmount += $ordersProduct->price * $ordersProduct->quantity;
                }
                if ($order->gross_amount != $grossAmount) {
                    $this->errors[] = 'Сумма gross_amount у заказа c id=' . $order->order_id . ' '
                        . 'не равна сумме произведений price и quantity в orders_products ' . $order->gross_amount
                        . '!=' . $grossAmount . ' для партнерки ' . $pp->tech_domain . 'c id=' . $pp->id;
                }
            }
        }
    }
}
