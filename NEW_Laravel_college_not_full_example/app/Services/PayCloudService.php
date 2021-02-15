<?php


namespace App\Services;


use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;

class PayCloudService
{
    const API_URL_PAY = 'https://api.cloudpayments.ru/payments/tokens/charge';

    private $publicId = '';
    private $currency = '';
    private $token = '';
    private $basicAuthLogin = '';
    private $basicAuthPass = '';

    /**
     * PayCloudService constructor.
     * @param string $token
     */
    function __construct($token = '')
    {
        $this->publicId = env('CLOUDPAYMENTS_PUBLIC_ID','');
        $this->currency = env('CLOUDPAYMENTS_CURRENCY','');
        $this->basicAuthLogin = env('CLOUDPAYMENTS_BASIC_AUTH_LOGIN','');
        $this->basicAuthPass = env('CLOUDPAYMENTS_BASIC_AUTH_PASS','');
        $this->token = $token;
    }

    /**
     * @param $amount
     * @param $iin
     * @param $userId
     * @param string $invoiceId
     * @param string $description
     * @return mixed
     */
    function pay($amount, $userId, $invoiceId = '', $description = '')
    {
        $params = [
            'Amount' => $amount,
            'Currency' => $this->currency,
            'AccountId' => $userId,
            'Token' => $this->token
        ];

        if($description)
        {
            $params['InvoiceId'] = $invoiceId;
        }

        if($description)
        {
            $params['Description'] = $description;
        }

        $response = Curl::to(self::API_URL_PAY)
            ->withResponseHeaders()
            ->returnResponseObject()
            ->allowRedirect()
            //->withHeader('Content-Type: application/json')
            ->withOption('USERPWD', $this->basicAuthLogin . ':' . $this->basicAuthPass)
            ->withData($params)
            ->post();

        return json_decode($response->content, true);
    }
}