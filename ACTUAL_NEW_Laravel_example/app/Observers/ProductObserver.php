<?php

namespace App\Observers;

use App\Helpers\CalculateFeesHelper;
use \App\Models\OrdersProduct;

class ProductObserver
{
    use CalculateFeesHelper;


    /**
     * Handle the orders product "created" event.
     *
     * @param  OrdersProduct  $ordersProduct
     * @return void
     */
    public function created(OrdersProduct $ordersProduct)
    {
        if (!$ordersProduct->isDirty(["status"])) {
            return;
        }
        $order = $ordersProduct->order;
        $this->runWithoutDispatchers($order, function() use($order) {
            $this->calculateOrderFees($order, false, true);
        });
    }

    /**
     * Handle the orders product "updated" event.
     *
     * @param  OrdersProduct  $ordersProduct
     * @return void
     */
    public function updated(OrdersProduct $ordersProduct)
    {
        if (!$ordersProduct->isDirty(["status"])) {
            return;
        }
        $order = $ordersProduct->order;
        $this->runWithoutDispatchers($order, function() use($order) {
            $this->calculateOrderFees($order, false, true);
        });
    }

    public function updating(OrdersProduct $ordersProduct)
    {
        //Если остановлено обновление статусов, не делаем ничего
        if($ordersProduct->isDirty('status')) {
            if ($ordersProduct->pp->stopupdate) {
                return false;
            }
        }
    }

    /**
     * Handle the orders product "deleted" event.
     *
     * @param  OrdersProduct  $ordersProduct
     * @return void
     */
    public function deleted(OrdersProduct $ordersProduct)
    {
        //
    }

    /**
     * Handle the orders product "restored" event.
     *
     * @param  OrdersProduct  $ordersProduct
     * @return void
     */
    public function restored(OrdersProduct $ordersProduct)
    {
        //
    }

    /**
     * Handle the orders product "force deleted" event.
     *
     * @param  OrdersProduct  $ordersProduct
     * @return void
     */
    public function forceDeleted(OrdersProduct $ordersProduct)
    {
        //
    }
}
