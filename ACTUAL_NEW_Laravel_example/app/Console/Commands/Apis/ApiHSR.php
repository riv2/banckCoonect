<?php


namespace App\Console\Commands\Apis;


use App\Helpers\ApiCheckerHelper;
use App\Models\Order;

class ApiHSR extends ApiCheckerHelper
{

    var $ppID = 79;


    public function implementFilters(&$query)
    {
        $query->where("pp_id","=", $this->ppID);
    }


    public function sendOrdersRequest($orders)
    {
        $ids = [];
        $logs = [];
        foreach($orders as $k=>$order) {
            $ids[] = "sitenumber[]=" . $k;
            $order->updated_at = date("Y-m-d H:i:s");
            $order->save();
            $logs[$order->order_id] = $this->createApiLog($order, "");
        }
        $ids = join("&", $ids);
        $url = "https://api.hsr24.ru/v1/orders/info?" . $ids;

        foreach($logs as $k=>$log) {
            $log->data_out = $url;
            $log->save();
        }

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER , true);
        curl_setopt($curl,CURLOPT_ENCODING , "");
        curl_setopt($curl,CURLOPT_MAXREDIRS , 10);
        curl_setopt($curl,CURLOPT_TIMEOUT , 0);
        curl_setopt($curl,CURLOPT_FOLLOWLOCATION , true);
        curl_setopt($curl,CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST , "GET");

        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "Accept: application/json";
        $headers[] = "Authorization: f729e8a39bf54555bc5cd57d0b15aab2";
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $data_in = $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response, true);
        if(!$response || $response["message"]!="OK") {
            foreach($logs as $k=>$log) {
                $log->data_in = $data_in;
                $log->result = 666;
                $log->save();
            }
            return;
        }

        foreach($response["data"] as $row) {
            $orders[$row["sitenumber"]]->gross_amount = $row["total"];
            switch($row["state"]) {
                case "returned":
                case "cancelled":
                    $orders[$row["sitenumber"]]->status = "reject";
                    break;
                case "finished":
//                    $orders[$row["sitenumber"]]->status = "sale";
                    break;
                case "inwork":
                    $orders[$row["sitenumber"]]->status = "new";
                    break;
            }
            $orders[$row["sitenumber"]]->save();
        }
        foreach($logs as $k=>$log) {
            $log->data_in = $data_in;
            $log->status = $orders[$k]->status;
            $log->result = 200;
            $log->save();
        }


    }


}
