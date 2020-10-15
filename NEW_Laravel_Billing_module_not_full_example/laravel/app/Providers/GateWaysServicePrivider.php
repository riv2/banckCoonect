<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Providers\Gateways\ManualServiceProvider;
use App\Providers\Gateways\RobokassaServiceProvider;
use App\Providers\BillingServiceProvider;
use App\Providers\BillingExtServiceProvider;

class GateWaysServicePrivider extends ServiceProvider
{
    private $gateway;
    private $gatewayName;

    public function __construct($gateWay)
    {
        if (!in_array(mb_strtolower($gateWay), BillingExtServiceProvider::$gateWaysList)){
            return ['error' => true, 'data' => ['msg' => BillingExtServiceProvider::ERROR_GATEWAY]];
        }
        $gateWayClassName = 'App\Providers\Gateways\\' . ucfirst(mb_strtolower($gateWay)) . 'ServiceProvider';
        $this->gateway = new $gateWayClassName;
        $this->gatewayName = strtoupper($gateWay);
    }

    public function doPayment($data) {
        $result  = $this->gateway->payment($data);
        // todo logic
        return $result;
    }

    public function doResult($data) {

        $result  = $this->gateway->result($data);

        if ($result['error'] === false) {
            $data = $result['data'];
            if (!BillingExtServiceProvider::checkmemberUniq($data['pay_uniq'])) {
                return BillingLoggerServiceProvider::addBillingLogRow(
                    'GateWaysServicePrivider:doResult',
                    ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_MEMBER_UNIQ, 'uidProduct' => $data['pay_uniq']],
                    $data['pay_uniq']
                );
            }

            $pay_sum  = (float)$data['pay_sum'];

            if ($pay_sum < 0 ) {
                return BillingLoggerServiceProvider::addBillingLogRow(
                    'GateWaysServicePrivider:doResult',
                    ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_SUMM_ZERO, 'pay_sum' => $pay_sum],
                    $data['pay_uniq']
                );
            }

            $signature               = $data['signature'];
            $billingServiceProvider  = new BillingServiceProvider();
            $uidProductResult        = $billingServiceProvider->getUidProductByCodeAndValue(BillingExtServiceProvider::CODE_GATEWAY_ADD_PRODUCT, $this->gatewayName);

            if ($uidProductResult['success'] == false) {
                return BillingLoggerServiceProvider::addBillingLogRow(
                    'GateWaysServicePrivider:doResult',
                    ['success' => true, 'msg' => BillingExtServiceProvider::SUCCESS_REQUEST, 'data' => $data]
                );
            }

            $balanceArr = [];
            $balanceArr['uniqMember']      = $data['pay_uniq'];
            $balanceArr['uidProduct']      = $uidProductResult['uidProduct'];
            $balanceArr['sum']             = $pay_sum;
            $balanceArr['signature']       = $signature;
            $balanceArr['typeTransaction'] = BillingExtServiceProvider::STATUS_GATEWAY_ADD_BALANCE;

            $resultBillingServiceProvider = $billingServiceProvider->buyProduct($balanceArr);

            if ($resultBillingServiceProvider['success'] === true) {
                $data['resultBillingServiceProvider'] = $resultBillingServiceProvider;
                return BillingLoggerServiceProvider::addBillingLogRow(
                    'GateWaysServicePrivider:doResult',
                    ['success' => true, 'msg' => BillingExtServiceProvider::SUCCESS_REQUEST, 'data' => $data],
                    $data['pay_uniq']
                );
            }

            return BillingLoggerServiceProvider::addBillingLogRow(
                'GateWaysServicePrivider:doResult',
                ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_ADD_BALANCE, 'resultBillingServiceProvider' => $resultBillingServiceProvider],
                $data['pay_uniq']
            );

        } else {
            return BillingLoggerServiceProvider::addBillingLogRow(
                'GateWaysServicePrivider:doResult',
                ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_REQUEST, 'request' => $data],
                $data['pay_uniq']
            );
        }
    }

    public function doSuccess($data) {
        $result  = $this->gateway->success($data);
        // todo logic
        return $result;
    }

    public function doFail($data) {
        $result  = $this->gateway->fail($data);
        // todo logic
        return $result;
    }



}