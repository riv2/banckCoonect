<?php

namespace App\Postbacks\Request;


use App\Models\Notify;
use App\Models\OrdersProduct;
use Illuminate\Http\Request;

class Request273 extends BaseRequest
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

        //Костыль sellaction
        $data['id'] = $this->notify->click_id.'-4368_'.$this->notify->gross_amount;
        unset($data['status']);

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
