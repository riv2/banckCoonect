<?php

namespace App\Processors;

use App\Lists\PoProcessorMsgList;
use App\Models\Order;
use App\Models\OrdersProduct;
use Illuminate\Support\Facades\Log;

class ProductsOrderProcessor
{
    protected $order;
    protected $beforeEvent;
    protected $pp_target;
    protected $msgs;
    protected $processorLogResult = '';
    protected $isError            = false;
    protected $debug              = false;

    /**
     * @param Order $order
     * @param $pp_target
     * @param $beforeEvent
     * Для работы процессинга нужен экземпляр обьекта заказо - обязательно.
     *  также выполняется подгрузка справочника сообщений
     */
    public function __construct(Order $order)
    {
        $this->order   = $order;
        $this->msgs    = PoProcessorMsgList::getList();
        $this->isError = false;
    }

    /**
     * @param string $pp_target = lead / product     *
     * @param boolean $beforeEvent
     * return array ['msg' = string, 'status' = success / error]
     *  метод инициализирует перерасчет процессинга.
     *
     */
    public function process($pp_target, $beforeEvent = false)
    {
        if (!is_null($this->order->banned_fraud_id) || !is_null($this->order->banned_link_id)) {
            $this->processorLogResult .= $this->msgs['MSG_31'];
            if (!is_null($this->order->banned_fraud_id)) {
                $table_name = 'banned_fraud';
                $table_id   = $this->order->banned_fraud_id;
            }
            if (!is_null($this->order->banned_link_id)) {
                $table_name = 'banned_links';
                $table_id   = $this->order->banned_link_id;
            }
            $this->banOrder($table_name, $table_id);
            $this->isError = true;
        }
        if ($pp_target == 'lead' && !$this->isError) {
            $this->processTypeLead($beforeEvent);
        } elseif  ($pp_target == 'products' && !$this->isError) {
            if (isset($this->order->products) && count($this->order->products) > 0) {
                $this->processTypeProducts();
            } else {
                $this->isError             = true;
                $this->processorLogResult .= $this->msgs['MSG_40'];
            }
        } else {
            $this->isError             = true;
            $this->processorLogResult .= $this->msgs['MSG_32'];
        }
        return $this->getRetunMsg();
    }

    /**
     *  *
     * @param boolean $withProducts
     * @param string  $table_name
     * @param int  $table_id
     * return array ['msg' = string, 'status' = success / error]
     * Метод высталяет цену в 0 у продуктов заказа и самого заказа - и помечает заказ и продукты как забаненые.
     *
     */
    public function banOrder(string $table_name, int $table_id, $withProducts = true)
    {
        if (!empty($table_name) && $table_id > 0) {
            $this->order->amount = 0.00;
            if ($table_name == 'banned_fraud') {
                $this->order->banned_fraud_id = $table_id;
            }
            else if ($table_name == 'banned_links') {
                $this->order->banned_link_id  = $table_id;
            } else {
                $this->isError             = true;
                $this->processorLogResult .= $this->msgs['MSG_29'];
            }
            if ($this->isError || !$this->order->save()) {
                $this->isError             = true;
                $this->processorLogResult .= $this->msgs['MSG_26'];
            }
            if (($withProducts) && (isset($this->order->products) && count($this->order->products) > 0) && !$this->isError) {
                foreach ($this->order->products as $product) {
                    $product->amount = 0.00;
                    if ($table_name == 'banned_fraud') {
                        $product->banned_fraud_id = $table_id;
                    }
                    else if ($table_name == 'banned_links') {
                        $product->banned_link_id  = $table_id;
                    } else {
                        $this->isError             = true;
                        $this->processorLogResult .= $this->msgs['MSG_30'];
                    }
                    if (!$product->save()) {
                        $this->isError             = true;
                        $this->processorLogResult .= $this->msgs['MSG_27'];
                    }
                }
            }
        } else {
            $this->isError             = true;
            $this->processorLogResult .= $this->msgs['MSG_28'];
        }
        return $this->getRetunMsg();
    }

    /**
     *   param $withProducts boolean
     *   return array - msg | status(error, success)
     *   Метод разбанивает.
     */
    public function unbanOrder($withProducts = true) {
        $this->order->banned_fraud_id = null;
        $this->order->banned_link_id  = null;
        if (!$this->order->save()) {
            $this->isError             = true;
            $this->processorLogResult .= $this->msgs['MSG_34'];
        }
        if (($withProducts) && (isset($this->order->products) && count($this->order->products) > 0) && !$this->isError) {
            foreach ($this->order->products as $product) {
                $product->banned_fraud_id = null;
                $product->banned_link_id  = null;
                if (!$product->save()) {
                    $this->isError = true;
                    $this->processorLogResult .= $this->msgs['MSG_35'];
                }
            }
        }
        return $this->getRetunMsg();
    }

    /**
     *   param void
     *   return array - msg | status(error, success)
     *   Метод отдает массив с статусом и логом работы процессинга.
     */
    protected function getRetunMsg()
    {
        $return = [];
        if ($this->isError) {
            $return['msg']    = 'Пересчет ставок. Проблема: ' . $this->processorLogResult . '|Номер заказа: ' .
                                 $this->order->order_id . '|Оффер: ' . $this->order->offer_id . '|Партнерка: ' . $this->order->pp_id .
                                 '|Номер строки в таблице orders:'  .  $this->order->id;
            $return['status'] = $this->msgs['STATUS_ERROR'];
            Log::channel('telegram')->alert($return['msg']);
        } else {
            $return['msg']    = $this->msgs['MSG_25'] . $this->msgs['MSG_33'];
            $return['status'] = $this->msgs['STATUS_SUCCESS'];
        }
        if ($this->debug) {
            Log::debug($this->processorLogResult . '|Order: ' . $this->order->toJson());
        }
        return $return;
    }

    /**
     *   param void
     *   return void
     *   Метод делает перерасчет заказов - с типом Продукты
     */
    protected function processTypeProducts()
    {
        if ($this->order->isDirty(['status'])) {
            if ($this->debug) {
                $this->isError             = true;
                $this->processorLogResult .= $this->msgs['MSG_1'];
            }
            $this->productsOrderChanged();
            return;
        }
        $statuses = [];
        $finalStatus = null;
        foreach ($this->order->products as $product) {
            $statuses[$product->status] = 1;
            $finalStatus                = $product->status;
            if (!$product->save() || $this->debug) {
                $this->isError             = true;
                $this->processorLogResult .= $this->msgs['MSG_2'];
            }
        }
        if (count($statuses) == 1) {
            if (!isset($statuses[$this->order->status])) {
                $this->order->status = $finalStatus;
            }
            $this->productsOrderChanged();
            return;
        }
        foreach ($this->order->products as $product) {
            if ($product->status == 'reject') {
                $product->fee           = null;
                $product->fee_id        = null;
                $product->amount        = 0;
                $product->amount_advert = 0;
                $product->fee_advert    = 0;
                if (!$product->save() || $this->debug) {
                    $this->isError             = true;
                    $this->processorLogResult .= $this->msgs['MSG_3'];
                }
            } else {
                if (!isset($product->offerfee->id)) {
                    $this->isError             = true;
                    $this->processorLogResult .= $this->msgs['MSG_4'];
                    return;
                }
                $product->fee_id     = $product->offerfee->id;
                $product->fee        = $product->offerfee->fee;
                $product->fee_advert = $product->offerfee->fee_advert;
                if (!$product->save() || $this->debug) {
                    $this->isError             = true;
                    $this->processorLogResult .= $this->msgs['MSG_5'];
                }
                if (!isset($this->order->offer->model)) {
                    $this->isError             = true;
                    $this->processorLogResult .= $this->msgs['MSG_6'];
                    return;
                }
                if (
                    $this->order->status == $this->order->offer->model &&
                    $product->status     == $this->order->offer->model
                ) {
                    if ($this->order->offer->fee_type == 'share') {
                        $product->amount        = round($product->total * $product->offerfee->fee / 100, 2);
                        $product->amount_advert = round($product->total * $product->offerfee->fee_advert / 100, 2);
                        if (!$product->save() || $this->debug) {
                            $this->isError             = true;
                            $this->processorLogResult .= $this->msgs['MSG_7'];
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $beforeEvent
     * return void
     * Метод делает перерасчет заказов - с типом ЛИД
     *
     */
    protected function processTypeLead($beforeEvent = false)
    {
        if ($beforeEvent) {
            if (!$this->order->isDirty(['status'])) {
                if ($this->debug) {
                    $this->isError             = true;
                    $this->processorLogResult .= $this->msgs['MSG_8'];
                }
                return;
            }
        }
        if ($this->order->status == 'reject') {
            $this->order->fee           = null;
            $this->order->fee_id        = null;
            $this->order->amount        = 0;
            $this->order->fee_advert    = 0;
            $this->order->amount_advert = 0;
            if (!$this->order->save() || $this->debug) {
                $this->isError             = true;
                $this->processorLogResult .= $this->msgs['MSG_9'];
            }
            return;
        }
        if (!isset($this->order->offerfee->id)) {
            $this->isError             = true;
            $this->processorLogResult .= $this->msgs['MSG_10'];
            return;
        }
        $this->order->fee_id     = $this->order->offerfee->id;
        $this->order->fee        = $this->order->offerfee->fee;
        $this->order->fee_advert = $this->order->offerfee->fee_advert;
        if (!isset($this->order->offer->model)) {
            $this->isError             = true;
            $this->processorLogResult .= $this->msgs['MSG_11'];
            return;
        }
        if ($this->order->offer->model != ($this->order->status ?? 'new')) {
            $this->order->fee        = null;
            $this->order->fee_id     = null;
            $this->order->fee_advert = null;
            if (!$this->order->save() || $this->debug) {
                $this->isError             = true;
                $this->processorLogResult .= $this->msgs['MSG_12'];
            }
        } else {
            if ($this->order->offer->fee_type == 'fix') {
                $this->order->amount        = $this->order->offerfee->fee;
                $this->order->amount_advert = $this->order->offerfee->fee_advert;
            } elseif ($this->order->offer->fee_type == 'share') {
                $this->order->amount        = round($this->order->gross_amount * $this->order->offerfee->fee / 100, 2);
                $this->order->amount_advert = round($this->order->gross_amount * $this->order->offerfee->fee_advert / 100, 2);
            }
            if (!$this->order->save() || $this->debug) {
                $this->isError             = true;
                $this->processorLogResult .= $this->msgs['MSG_13'];
            }
        }
    }

    /**
     * @param void
     * return void
     * Метод делает перерасчет у товаров по цене - которые находятся внутри заказа.
     *
     */
    protected function productsOrderChanged()
    {
        if ($this->order->status == 'reject') {
            $this->order->products->each(function ($item) {
                if (!$item->update([
                    'status'        => 'reject',
                    'amount'        => 0,
                    'amount_advert' => 0,
                    'fee_advert'    => 0,
                    'fee'           => null,
                    'fee_id'        => null
                ]) || $this->debug) {
                    $this->isError             = true;
                    $this->processorLogResult .= $this->msgs['MSG_14'];
                }
            });
            $this->order->amount        = 0;
            $this->order->amount_advert = 0;
            $this->order->fee_advert    = 0;
            $this->order->fee           = null;
            $this->order->fee_id        = null;
            if (!$this->order->save() || $this->debug) {
                $this->isError             = true;
                $this->processorLogResult .= $this->msgs['MSG_15'];
            }
            return;
        } else {
            if ($this->order->status == $this->order->offer->model) {
                if ($this->order->offer->fee_type == 'fix') {
                    $this->order->products->each(function ($item) {
                        if(!$item->update(['amount' => 0, 'amount_advert' => 0]) || $this->debug) {
                            $this->isError             = true;
                            $this->processorLogResult .= $this->msgs['MSG_16'];
                        }
                    });
                    if (!isset($this->order->offerfee->id)) {
                        $this->isError             = true;
                        $this->processorLogResult .= $this->msgs['MSG_24'];
                        return;
                    }
                    $this->order->amount        = $this->order->offerfee->fee;
                    $this->order->amount_advert = $this->order->offerfee->amount_advert;
                    $this->order->fee           = $this->order->offerfee->fee;
                    $this->order->fee_advert    = $this->order->offerfee->fee_advert;
                    $this->order->fee_id        = $this->order->offerfee->id;
                    if (!$this->order->save()  || $this->debug) {
                        $this->isError = true;
                        $this->processorLogResult .= $this->msgs['MSG_17'];
                    }
                    return;
                } elseif ($this->order->offer->fee_type == 'share') {
                    $sum = 0;
                    $sum_advert = 0;
                    foreach ($this->order->products as $product) {
                        $product->fee_id        = $product->offerfee->id;
                        $product->fee           = $product->offerfee->fee;
                        $product->fee_advert    = $product->offerfee->fee_advert;
                        $product->amount_advert = $product->offerfee->amount_advert;
                        if ($product->status != 'reject') {
                            $product->amount        = round($product->total * $product->offerfee->fee / 100, 2);
                            $product->amount_advert = round($product->total * $product->offerfee->fee_advert / 100, 2);
                            $sum                    += $product->amount;
                            $sum_advert             += $product->amount_advert;
                        } else {
                            $product->amount        = 0;
                            $product->amount_advert = 0;
                            $product->fee           = null;
                            $product->fee_advert    = 0;
                            $product->fee_id        = null;
                        }
                        $product->status = $this->order->status;
                        if (!$product->save() || $this->debug) {
                            $this->isError             = true;
                            $this->processorLogResult .= $this->msgs['MSG_18'];
                        }
                    }
                    $this->order->amount        = $sum;
                    $this->order->amount_advert = $sum_advert;
                    if (!$this->order->save() || $this->debug) {
                        $this->isError             = true;
                        $this->processorLogResult .= $this->msgs['MSG_19'];
                    }
                    return;
                }
            } else {
                foreach ($this->order->products as $product) {
                    $product->status        = $this->order->status;
                    $product->amount        = 0;
                    $product->amount_advert = 0;
                    if (!$product->save() || $this->debug) {
                        $this->isError             = true;
                        $this->processorLogResult .= $this->msgs['MSG_20'];
                    }
                }
                if (!$this->order->save() || $this->debug) {
                    $this->isError             = true;
                    $this->processorLogResult .= $this->msgs['MSG_21'];
                }
            }
        }
    }

    /**
     * Заполняем fee и fee_id
     * @param Order|OrdersProduct $object
     * return boolean
     */
    protected function fillFee(&$object)
    {
        $offerfee = $object->offerfee;
        if (!isset($offerfee->id)) {
            $this->isError             = true;
            $this->processorLogResult .= $this->msgs['MSG_22'];;
            return false;
        }
        if ($offerfee->id != $object->fee_id || $offerfee->fee != $object->fee) {
            $object->fee_id = $offerfee->id;
            $object->fee    = $offerfee->fee;
            if (!$object->save() || $this->debug) {
                $this->isError             = true;
                $this->processorLogResult .= $this->msgs['MSG_23'];;
                return false;
            }
        }
        return true;
    }

}
