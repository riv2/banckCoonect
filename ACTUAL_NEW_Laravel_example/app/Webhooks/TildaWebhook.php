<?php

namespace App\Webhooks;

use App\Helpers\PartnerProgramStorage;
use App\Interfaces\WebhookInterface;
use App\Models\Offer;
use App\Models\Order;
use App\Models\ApiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TildaWebhook extends BaseWebhook implements WebhookInterface
{
    public function validate()
    {
        $pp_id = PartnerProgramStorage::getPP()->id;

        $this->validation_rules = [
            'offer_id' => [
                // 'exists' => Rule::exists(Offer::class, 'id')->where('pp_id', $pp_id),
            ],
        ];

        /**
         * @todo Проверка подписи (параметр sign)
         */
        return parent::validate();
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function handle()
    {
        $log = new ApiLog();
        $log->offer_id = $this->request->get('offer_id') ?? null;
        $log->data_in = json_encode($this->request->all());
        $log->save();

        Log::channel('telegram')->debug('Получен новый webhook тильда ', [
            'request' => $this->request->all(),
            'referer' => $this->request->header('referer', null),
        ]);

        if (empty($this->request->input('utm_source'))) {
            Log::channel('telegram')->debug('Пустой utm_source');
            return response()->noContent(200);
        }

        if ($this->request->input('utm_source') != 'partners') {
            Log::channel('telegram')->debug('utm_source != partners');
            return response()->noContent(200);
        }

        if (empty($this->request->input('offer_id'))) {
            Log::channel('telegram')->debug('Отсутствует offer_id');
            return response()->noContent(200);
        }

        $pp_id = PartnerProgramStorage::getPP()->id;
        $order_id = null;
        $gross_amount = null;
        if ($this->request->input('offer_id')) {
            $order_id = $this->request->input('payment.orderid');
            $gross_amount = $this->request->input('payment.amount');
        } else {
            $order_id = $this->request->input('tranid');
            $gross_amount = 0;
        }

        if (empty($order_id)) {
            Log::channel('telegram')->debug('Отсутствует order_id');
            return response()->noContent(200);
        }

        $order = Order::query()
                ->where('pp_id', '=', $pp_id)
                ->where('order_id', '=', $order_id)
                ->first() ?? new Order();
        if ($order->reestr_id) {
            Log::channel('telegram')->debug('Ошибка! ORDER уже в реестре!');
            return response()->noContent(200);
        }

        $order->pp_id = $pp_id;
        $order->order_id = $order_id;
        $order->offer_id = $this->request->input('offer_id');
        $order->link_id = $this->request->input('utm_campaign');
        $order->web_id = $this->request->input('utm_term');
        $order->partner_id = $this->request->input('utm_content');
        $order->click_id = $this->request->input('click_id');
        $order->gross_amount = $gross_amount;
        $order->datetime = now();
        $order->status = 'new';
        $order->save();//создали заказ

        $order->status = $this->request->input('status');
        $order->sale = 1;
        $order->save();//обновили статус

        $log->offer_id = $order->offer_id ?? null;
        $log->order_id = $order->order_id ?? null;
        $log->click_id = $order->click_id ?? null;
        $log->save();

        return response()->noContent(200);
    }
}
