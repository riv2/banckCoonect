<?php

namespace App\Helpers;


use App\Models\ApiLog;
use App\Models\Order;

abstract class ApiCheckerHelper
{
    var $ppID;
    public abstract function sendOrdersRequest($orders);
    public abstract function implementFilters(&$query);

    /**
     * @param Order $order
     * @param string $data_in
     * @return ApiLog
     */
    public function createApiLog(Order $order, $data_in)
    {
        $log = new ApiLog();
        $log->fill([
            "offer_id"=>$order->offer_id,
            "order_id"=>$order->order_id,
            "click_id"=>$order->click_id,
            "data_in"=>$data_in
        ]);
        return $log;
    }

    public function run()
    {
        $orders = $this->getOrdersToCheck();
        if(!$orders->count()) {
            return;
        }
        //отправляем сразу пачкой
        $this->sendOrdersRequest($orders);
    }

    //Получаем по 10 заказов
    public function getOrdersToCheck()
    {
        $query = Order::query()->whereIn("status",["new", "approve"])
            ->where(function($where){
                $where->where("updated_at","<=", date("Y-m-d H:i:s", strtotime("-12 hour")))
                    ->orWhereNull("updated_at");
            });
        $this->implementFilters($query);

        return $query
            ->limit(10)
            ->get()->keyBy("order_id");
    }

}
