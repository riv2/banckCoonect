<?php
namespace App\Providers\Gateways;

use Illuminate\Support\ServiceProvider;
use App\Providers\BillingExtServiceProvider;

class RobokassaServiceProvider extends ServiceProvider
{
    private $mrchLogin;
    private $pass1;
    private $pass2;
    public  $type = 'robokassa';
    private $server = 'https://auth.robokassa.ru/Merchant/Index.aspx';
    private $testMode = 'test'; // work = work mode, other - test mode.

    private $invId = 501; //todo

    public function __construct()
    {
        $this->testMode  = env("ROBOKASSA_WORK_MODE", "test");

        if ($this->testMode === 'work') {
            $this->mrchLogin = env("ROBOKASSA_LOGIN", "");
            $this->pass1     = env("ROBOKASSA_PASS1", "");
            $this->pass2     = env("ROBOKASSA_PASS2", "");
            $this->testMode  = 0;
        } else {
            $this->mrchLogin = env("ROBOKASSA_TEST_LOGIN", "");
            $this->pass1     = env("ROBOKASSA_TEST_PASS1", "");
            $this->pass2     = env("ROBOKASSA_TEST_PASS2", "");
            $this->testMode  = 1;
        }
    }

    /**
     * Формирование ссылки и отправка юзера оплачивать заказ
     */
    public function payment($data)
    {
        $pay_sum   = isset($data['pay_sum']) ? $data['pay_sum'] :  null;
        $pay_uniq  = isset($data['pay_uniq']) ? $data['pay_uniq'] :  null;
        $desc      = isset($data['desc']) ? $data['desc'] : $pay_uniq;

        $invId     = $this->invId;
        $culture   = 'ru'; //todo
        $currency  = BillingExtServiceProvider::DEFAULT_CURRENCY_UNIQ;

        $crc = md5($this->mrchLogin . ":" . $pay_sum . ":" . $invId . ":" . $this->pass1 . ":Shp_uniq=" .  $pay_uniq);

        $queryParams = [
            'MrchLogin'      => $this->mrchLogin,
            'OutSum'         => $pay_sum,
        //    'OutSumCurrency' => $currency,
            'InvId'          => $invId,
            'Shp_uniq'       => $pay_uniq,
            'Desc'           => substr($desc, 0, 99),
            'SignatureValue' => $crc,
            'Culture'        => $culture,
            'IsTest'         => $this->testMode,
        ];

        $url = $this->server . '?' . http_build_query($queryParams);

        return $url;

    }

    public function result($data)
    {
        $result = $this->checkCallbacks($data);
        // todo logic
        return $result;
    }


    public function success($data)
    {
        $result = $this->checkCallbacks($data);
        // todo logic
        return $result;
    }


    public function fail($data)
    {
        $result = $this->checkCallbacks($data);
        // todo logic
        return $result;
    }


    private function checkCallbacks($data) {

        dumpLog($data, '---$data');

        $result = ['error' => true, 'data' => $data];

        if (!is_array($data)) {
            return $result;
        }

        $pay_sum   = isset($data['OutSum']) ? $data['OutSum'] :  null;
        $pay_uniq  = isset($data['Shp_uniq']) ? $data['Shp_uniq'] :  null;
        $invId     = isset($data['InvId']) ? $data['InvId'] : $this->invId;
        $currency  = isset($data['OutSumCurrency']) ? $data['OutSumCurrency'] : BillingExtServiceProvider::DEFAULT_CURRENCY_UNIQ;

        $crc = md5($pay_sum . ":" . $invId . ":" . $this->pass2 . ":Shp_uniq=" .  $pay_uniq);

        dumpLog($crc, '---$crc');

        $signatureValue =  isset($data['SignatureValue']) ? $data['SignatureValue'] : null;

        $crc = $signatureValue; // todo - разобраться с signature

        if (strtoupper($crc) == strtoupper($signatureValue))
        {
            $data['pay_sum']   = $pay_sum;
            $data['pay_uniq']  = $pay_uniq;
            $data['currency']  = $currency;
            $data['signature'] = $signatureValue;

            $result = ['error' => false, 'data' => $data];
        }

//        $data['pay_uniq'] = $pay_uniq; // todo -- убрать после проверки.
//        $data['pay_type'] = $pay_type; // todo -- убрать после проверки.
//        $data['currency'] = $currency; // todo -- убрать после проверки.
//        $data['signature'] = $signatureValue;  // todo -- убрать после проверки.
//        $result = ['error' => false, 'data' => $data];  // todo -- убрать после проверки.

        dumpLog($result, '---$result');


        return $result;

    }



}