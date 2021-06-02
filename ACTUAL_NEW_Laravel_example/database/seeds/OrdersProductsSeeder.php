<?php

use App\Models\Order;
use App\Models\OrdersProduct;
use Illuminate\Database\Seeder;

class OrdersProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orders = Order::query()->where("partner_id", "=", 4)
            ->get()->toArray();

        foreach ($orders as $order) {
            for ($i = 0; $i < 10; $i++) {
                OrdersProduct::withoutEvents(function () use ($i, $order) {
                    $OrdersProduct = new OrdersProduct();
                    $OrdersProduct->order_id = $order['order_id'];
                    $OrdersProduct->parent_id = $order['id'];
                    $OrdersProduct->pp_id = $order['pp_id'];
                    $OrdersProduct->partner_id = $order['partner_id'];
                    $OrdersProduct->product_id = $i;
                    $OrdersProduct->link_id = $order['link_id'];
                    $OrdersProduct->price = rand(10000, 1000000) / 100;
                    $OrdersProduct->fee = $order['fee'];
                    $OrdersProduct->quantity = rand(1,20);
                    $OrdersProduct->datetime = now();
                    $OrdersProduct->save();
                });
            }
        }
    }
}
