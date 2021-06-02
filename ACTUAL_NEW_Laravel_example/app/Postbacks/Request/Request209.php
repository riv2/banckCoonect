<?php

namespace App\Postbacks\Request;


use App\Models\Notify;
use App\Models\OrdersProduct;
use Illuminate\Http\Request;

class Request209 extends BaseRequest
{
    protected Notify $notify;

    public function __construct(Notify $notify)
    {
        $this->notify = $notify;
    }

    /**
     * @return array
     */
    public function request()
    {
        $np = $this->notify->notify_param;
        if(empty($np)) {
            return false;
        }
        $params = $np->toArray();
        $order_params = [
            'order_id',
            'status',
            'fee_id',
            'amount',
            'gross_amount',
            'click_id',
            'web_id',
        ];
        $data = [];
        foreach($order_params as $k) {
            $value = ($k == 'status') ? $params['status_' . $this->notify->status . '_value'] : $this->notify->$k;
            if(!empty($params[$k])) {
                $data[$params[$k]] = $value;
            }
        }

        //Костыль добавим customer_type=5 для постбэков new @TODO убрать костыль после iss #3558
        if ($this->notify->status=='new') {
            $data['customer_type'] = 5;
        }


        //Добавим состав корзины
        $basket = [];
        $orderProducts = OrdersProduct::query()->where('order_id', '=', $this->notify->order_id)->where('pp_id', '=', 79)->get();
        foreach ($orderProducts as $op) {
            $basket[] = [
                'pid' => (string)$op->product_id,
                'pn' => (string)$op->product_name,
                'up' => (string)$op->price,
                'pc' => (string)$op->category,
                'qty' => (string)$op->quantity,
                'pd' => "0",
            ];
        }
        $data['basket'] = json_encode($basket);


        $url = $params['postback_url'];
        if ($params['method'] == 'get') {
            $divider = (strpos($url,'?')!==false) ? '&' : '?';
            $url .= $divider . http_build_query($data);
        }

        $result = [
            'url'=>$url,
            'data'=>$data,
            'method'=>$params['method']
        ];
        return $result;
    }

}
