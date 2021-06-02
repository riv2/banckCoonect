<?php

namespace App\Console\Commands;

use App\Helpers\CalculateFeesHelper;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RecalcRates extends Command
{
    use CalculateFeesHelper;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gocpa:recalcrates {pp_id : Partner Program ID} {date_start : Start date} {date_end : End Date} {--partner_id= : Partner ID} {--bu= : Business Unit ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate rates with progressive matrix';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Пересчет ставок для указанных дат, БЮ, партнерки, айди партнера
     *
     * @return int
     */
    public function handle()
    {
        $partner_id = $this->option('partner_id');
        $bu = $this->option('bu');
        $pp = $this->argument('pp_id');
        $date_start = $this->argument('date_start');
        $date_end = $this->argument('date_end');
        Log::channel('rates')->info('Start recalc, params: ' . json_encode(
            compact('partner_id','bu','date_start','date_end', 'pp')
            ));
        $q = Order::query()->whereNull('reestr_id')->where('pp_id','=', $pp);
        if ($partner_id) {//Ищем заказы этого партнера
            $q->where('partner_id','=', $partner_id);
        }
        if ($bu) {//Ищем заданные БЮ
            $q->where('business_unit_id','=', $bu);
        }
        $q = $q->whereBetween('date', [$date_start, $date_end])
            ->whereNotNull('fee_id');
        //Начинаем обходить заказы
        foreach($q->get() as $order) {
            $log = ["ID:" . $order->order_id];
            $fee_type = $order->offer->fee_type;
            //Для лидов применяем к таблице orders
            if($order->pp->pp_target == 'lead') {
                $fee = $this->getProgressiveFee($order);
                //Не найдена ставка, ругаемся!
                if(!$fee) {
                    Log::channel('rateserror')->error('Для заказа ' . $order->order_id . ' не найдена ставка');
                    continue;
                }
                //Применяем найденную ставку к заказу
                $order->fee = $fee->fee;
                $order->fee_id = $fee->id;
                if($fee_type == 'fix') {//Ставка фикс
                    $order->amount = $fee->fee;
                } else {//Процентная ставка
                    $order->amount = round($order->gross_amount / 100 * $fee->fee,2);
                }
                $order->save();
                //Писнём в лог примененную ставку и айди заказа
                $log[] = sprintf('Fee: %s (#%s)', $order->amount, $fee->id);
                Log::channel('rates')->info(join("\t", $log));
            } else {
                //Для товаров применяем к таблице orders_products последовательно к каждому продукту
                $amount = 0;
                foreach($order->products->where('amount','>',0) as $product) {
                    $fee = $this->getProgressiveFee($order, $product->business_unit_id);
                    //Нет ставки - пишем в лог
                    if(!$fee) {
                        Log::channel('rateserror')
                            ->error('Для заказа ' . $order->id . ' не найдена ставка, BU: ' . $product->business_unit_id);
                        continue;
                    }


                    $product->fee_id = $fee->id;
                    if($fee_type == 'fix') {//Ставка фикс - просто ставим нужный amount
                        $product->amount = $fee->fee;
                    } else {//Начисляем процент
                        $product->amount = round($product->total / 100 * $fee->fee,2);
                    }
                    $product->save();

                    $amount+=$product->amount;
                }
                $order->amount = $amount;
                $order->save();
                $log[] = sprintf('Amount: %s', $amount);
                Log::channel('rates')->info(join("\t", $log));
            }
        }
        Log::channel('rates')->error( 'DONE');
        return 0;
    }

}
