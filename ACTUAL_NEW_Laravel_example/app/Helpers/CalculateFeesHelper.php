<?php

namespace App\Helpers;

use App\Models\Order;
use App\Models\RateRule;
use App\Processors\ProductsOrderProcessor;

trait CalculateFeesHelper
{
    protected $dispatcher;

    protected function runWithoutDispatchers(&$object, $function)
    {
        $this->dispatcher = $object::getEventDispatcher();
        $object::unsetEventDispatcher();
        $function();
        $object::setEventDispatcher($this->dispatcher);
    }

    /**
     * Подсчет выплат
     *
     * @param Order $order
     * @param boolean $productsChanged
     * @param boolean $beforeEvent - событие вызывается ДО сохранения (on updating например)
     * @return void
     * @todo Проверка на некорректный pp_target
     * @todo Проверка заявки в реестре (запрет изменения полей статуса, amount и прочего)
     * @todo Выполнять только в случае изменения зависимых полей (предусмотреть force-режим)
     */
    public function calculateOrderFees(Order $order, $beforeEvent = false, $productsChanged = false)
    {
        $productsOrderProcessor = new ProductsOrderProcessor($order);
        $productsOrderProcessor->process($order->pp->pp_target, $productsChanged);
        if (!$beforeEvent) {
            $order->save();
            return;
        }
    }

    /**
     * Возвращает статистику по партнеру (заказы, оборот) за месяц, в котором сделан заказ
     * @param Order $order
     * @return array
     * @todo кешировать это все
     */
    protected function getPartnerStats(Order $order)
    {
        $firstDay = date('Y-m-01', strtotime($order->date));
        $lastDay = date('Y-m-t', strtotime($order->date));
        $data = Order::query()
            ->whereBetween('date', [$firstDay, $lastDay])
            ->where('amount', '>', 0)
            ->selectRaw('count(*) AS orders_cnt')
            ->selectRaw('SUM(gross_amount) AS orders_amount')
            ->first();
        $amounts = $data->orders_amount;
        $count = $data->orders_cnt;
        return [$amounts, $count];
    }

    protected function getProgressiveFee(Order $order, $businessUnitId = null)
    {
        $businessUnitId = ($businessUnitId) ? $businessUnitId : $order->business_unit_id;
        // Получаем все ставки для заказа
        $fees = RateRule::query()
            ->where('offer_id', '=', $order->offer_id)
            ->whereRaw('(rate_rules.partner_id is NULL or rate_rules.partner_id=?)', $order->partner_id)
            ->whereRaw('(rate_rules.link_id is NULL or rate_rules.link_id=?)', $order->link_id)
            ->whereRaw('? between date_start and coalesce(date_end, ?)', [$order->date, $order->date])
            ->where(function ($where) use ($businessUnitId) {
                $where->where('business_unit_id', '=', $businessUnitId)
                    ->orWhereNull('business_unit_id');
            })
            ->orderBy('fee', 'DESC')
            // Индивидуальная ставка приоритетнее всех остальных
            ->orderByDesc('partner_id')
            ->orderByDesc('link_id')
            ->get();
        $hasProgressiveParam = false;
        $firstFee = null;
        // Есть ли прогрессивная ставка?
        foreach ($fees as $fee) {
            if (!$firstFee) {
                $firstFee = $fee;
            }
            // Индивидуальная ставка приоритетнее всех остальных
            if ($fee->partner_id || $fee->link_id) {
                return $fee;
            }
            if ($fee->progressive_param) {
                $hasProgressiveParam = true;
                break;
            }
        }
        // Если нет, возвращаем первую найденую
        if (!$hasProgressiveParam) {
            return $firstFee;
        }
        $firstFee = null;
        list($amounts, $count) = $this->getPartnerStats($order);
        foreach ($fees as $fee) {
            // Запоминаем первую ставку без параметра
            if (!$fee->progressive_param && !($firstFee)) {
                $firstFee = $fee;
            }
            // По кол-ву заказов
            if ($fee->progressive_param == 'orders') {
                if ($fee->progressive_value <= $count) {
                    return $fee;
                }
                // По обороту
            } elseif ($fee->progressive_param == 'amount') {
                if ($fee->progressive_value <= $amounts) {
                    return $fee;
                }
            }
        }
        // Иначе возвращаем по умолчанию
        return $firstFee;
    }
}
