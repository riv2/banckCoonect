<?php

namespace App\Postbacks\Request;


use App\Models\Notify;

class BaseRequest
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
        if (empty($np)) {

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
        foreach ($order_params as $k) {
            $value = ($k == 'status') ? $params['status_' . $this->notify->status . '_value'] : $this->notify->$k;
            if (!empty($params[$k])) {
                $data[$params[$k]] = $value;
            }
        }
        $url = $params['postback_url'];

        if ($params['method'] == 'get') {
            $divider = (strpos($url, '?') !== false) ? '&' : '?';
            $url .= $divider . http_build_query($data);
        }
        $result = [
            'url' => $url,
            'data' => $data,
            'method' => $params['method']
        ];

        return $result;
    }
}
