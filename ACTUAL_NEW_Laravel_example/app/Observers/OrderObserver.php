<?php

namespace App\Observers;

use App\Helpers\CalculateFeesHelper;
use App\Models\Notify;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    use CalculateFeesHelper;

    /**
     * Handle the order "created" event.
     *
     * @param Order $order
     * @return void
     */
    public function creating(Order $order)
    {
        if (is_null($order->offer_id)) {
            throw new Exception('Запрещено создавать заказ без offer_id');
        }

        if ($order->reestr_id) {
            throw new Exception('Запрещено создавать заказ с указанным reestr_id');
        }

        if ($order->status !== 'new') {
            throw new Exception('Запрещено создавать заказ со статусом !== new, статус ' . $order->status);
        }
    }

    /**
     * Handle the order "created" event.
     *
     * @param Order $order
     * @return void
     */
    public function created(Order $order)
    {
        try {
            $this->runWithoutDispatchers($order, function () use ($order) {
                $this->calculateOrderFees($order);
            });
        } catch (Exception $e) {
            Log::channel('rateserror')->error('Ошибка при пересчете ставки для заказа ' . $order->order_id);
        }
        $this->createNotification($order);
    }

    protected function createNotification(Order $order)
    {
        $np = $order->notifyParams;
        $status = 'status_' . $order->status . '_value';
        if (empty($np) || empty($np->$status)) {
            return;
        }
        $notify = Notify::query()->where('order_id', '=', $order->order_id)
            ->where('status', '=', $order->status)->first();
        if ($notify) {
            return;
        }
        $notify = new Notify();
        $notify->order_id = $order->order_id;
        $notify->partner_id = $order->partner_id;
        $notify->status = $order->status;
        $notify->fee_id = $order->fee_id;
        $notify->sent_cnt = 0;
        $notify->datetime = date('Y-m-d H:i:s');
        $notify->click_id = $order->click_id;
        $notify->web_id = $order->web_id;
        $notify->link_id = $order->link_id;
        $notify->gross_amount = $order->gross_amount ?? 0.00;
        $notify->model = $order->model;
        $notify->amount = $order->amount ?? 0;
        $notify->save();
    }


    /**
     * Handle the order "updated" event.
     *
     * @param Order $order
     * @return void
     */
    public function updated(Order $order)
    {
        //
    }

    public function updating(Order $order)
    {
        //Если остановлено обновление статусов, не делаем ничего
        if ($order->isDirty('status')) {
            if ($order->pp->stopupdate) {
                return false;
            }
        }
        try {
            $this->runWithoutDispatchers($order, function () use ($order) {
                $this->calculateOrderFees($order, true);
            });
        } catch (Exception $e) {
            Log::channel('rateserror')->error('Ошибка при пересчете ставки для заказа ' . $order->order_id);
        }
        $this->createNotification($order);
    }

    /**
     * Handle the order "deleted" event.
     *
     * @param Order $order
     * @return void
     */
    public function deleted(Order $order)
    {
        //todo
    }

    /**
     * Handle the order "restored" event.
     *
     * @param Order $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the order "force deleted" event.
     *
     * @param Order $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
