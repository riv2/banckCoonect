<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;

use App\Models\_billing_balance;
use App\Models\_billing_gateways;
use App\Models\_billing_currences;
use App\Models\_billing_products_cost;
use App\Models\_billing_products;
use App\Models\_members;

class BillingServiceProvider extends ServiceProvider
{
    private $transactionServiceProvider;

    public function __construct()
    {
        $this->transactionServiceProvider = new TransactionServiceProvider();
    }

    // uniqMember, uidProduct, sum, typeTransaction, signature=null, hideTransaction=null
    public function buyProduct($productArr)
    {
        $typeTransaction = isset($productArr['typeTransaction'])     ? $productArr['typeTransaction']     : null;
        $sum             = isset($productArr['sum'])                 ? $productArr['sum']                 : 0;
        $hideTransaction = isset($transactionArr['hideTransaction']) ? $transactionArr['hideTransaction'] : null;

        if (!BillingExtServiceProvider::checkTypeTransaction($typeTransaction)) {
            return BillingLoggerServiceProvider::addBillingLogRow(
                'BillingServiceProvider:buyProduct',
                ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_TYPE_TRANSACTION, 'typeTransaction' => $typeTransaction, 'productArr' => $productArr],
                $productArr['uniqMember']
            );
        }

        if ($typeTransaction == BillingExtServiceProvider::STATUS_BUY_PRODUCT) {
            $productArr['sum'] = $sum < 0 ? $sum : -1 * $sum;

            $memberBalance = $this->getBalanceByMemberUniq($productArr['uniqMember']);
            $difference = abs($memberBalance['balance']) - abs($productArr['sum']);

            if ($difference <= 0 && $hideTransaction == null) {
                return BillingLoggerServiceProvider::addBillingLogRow(
                    'BillingServiceProvider:buyProduct',
                    ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_INSUFFICIENT_FUNDS, 'balance' => $memberBalance['balance'], 'difference' => $difference, 'productArr' => $productArr],
                    $productArr['uniqMember']
                );
            }

            if ($productArr['sum'] == 0 && $hideTransaction == null) {
                return BillingLoggerServiceProvider::addBillingLogRow(
                    'BillingServiceProvider:buyProduct',
                    ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_SUMM_ZERO, 'sum' => $productArr['sum'], 'productArr' => $productArr],
                    $productArr['uniqMember']
                );
            }
        }

        $transactionResult = $this->transactionServiceProvider->addTransaction($productArr);

        if ($transactionResult['success'] == true) {
            $productArr['transactionUid'] = $transactionResult['transactionUid'];

            $recalcResult = $this->doRecalcBalance($productArr['uniqMember']);

            if ($recalcResult['success'] == true) {
                return BillingLoggerServiceProvider::addBillingLogRow(
                    'BillingServiceProvider:addBalance',
                    ['success' => true, 'msg' => BillingExtServiceProvider::SUCCESS_ADD_BALANCE, 'balanceArr' => $productArr, 'transactionResult' => $transactionResult, 'recalcResult' => $recalcResult],
                    $productArr['uniqMember']
                );
            } else {
                return BillingLoggerServiceProvider::addBillingLogRow(
                    'BillingServiceProvider:addBalance:doRecalcBalance',
                    ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_ADD_BALANCE, 'balanceArr' => $productArr, 'recalcResult' => $recalcResult],
                    $productArr['uniqMember']
                );               
            }
        } else {
            return BillingLoggerServiceProvider::addBillingLogRow(
                'BillingServiceProvider:addBalance:addTransaction',
                ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_ADD_BALANCE, 'balanceArr' => $productArr, 'transactionResult' => $transactionResult],
                $productArr['uniqMember']
            );
        }
    }

    public function doRecalcBalance($uniqMember) {

        $balance = 0;
        $getTransactionsByMemberResult = $this->transactionServiceProvider->getTransactionsByMember($uniqMember);

        if ($getTransactionsByMemberResult['success'] == true) {
            $transactions = $getTransactionsByMemberResult['transactions'];

            unset($getTransactionsByMemberResult['transactions']);

            foreach ($transactions as $transaction) {
                $balance += $transaction->sum;
            }

            $balanceResult = _billing_balance::updateOrCreate(
                [   'uniq_member'      => $uniqMember],
                [
                    'uniq_member'       => $uniqMember,
                    'balance'           => (float)$balance,
                    'date_updated'      => BillingExtServiceProvider::getDateForTimestamp()
                ]);

            if ($balanceResult) {
                return BillingLoggerServiceProvider::addBillingLogRow(
                    'BillingServiceProvider:doRecalcBalance',
                    ['success' => true, 'msg' => BillingExtServiceProvider::SUCCESS_RECALC_BALANCE, 'uniqMember' => $uniqMember, 'balance' => $balance, 'getTransactionsByMemberResult' => $getTransactionsByMemberResult],
                    $uniqMember
                );                
            } else {
                return BillingLoggerServiceProvider::addBillingLogRow(
                    'BillingServiceProvider:doRecalcBalance',
                    ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_RECALC_BALANCE, 'uniqMember' => $uniqMember, 'balance' => $balance, 'getTransactionsByMemberResult' => $getTransactionsByMemberResult],
                    $uniqMember
                );
            }
        }

        return BillingLoggerServiceProvider::addBillingLogRow(
            'BillingServiceProvider:doRecalcBalance',
            ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_RECALC_BALANCE, 'uniqMember' => $uniqMember, 'balance' => $balance, 'getTransactionsByMemberResult' => $getTransactionsByMemberResult],
            $uniqMember
        );

    }

    
    public function getBalanceByMemberUniq($uniqMember) {

        $balance = 0;

        if (!BillingExtServiceProvider::checkMemberUniq($uniqMember)) {
           return ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_MEMBER_UNIQ, 'uniqMember' => $uniqMember];
        }

        $balanceResult = _billing_balance::where('uniq_member', $uniqMember)->first();

        if ($balanceResult) {
            $balance = ceil($balanceResult->balance * 100) / 100;
        }

        return ['success' => true, 'balance' => $balance, 'uniqMember' => $uniqMember];

    }

    public function getBalanceHistoryByMemberUniq($uniqMember) {

        $getTransactionsByMemberResult = $this->transactionServiceProvider->getTransactionsByMember($uniqMember, 10);

        if ($getTransactionsByMemberResult['success'] == true) {
            $transactions = $getTransactionsByMemberResult['transactions'];

            $transactionsArr = [];
            $i = 0;

            foreach($transactions as $transaction) { // todo пепренести в трансформер
                $transactionsArr[$i][] = ceil($transaction->sum * 100) / 100;
                $transactionsArr[$i][] = $transaction->date_created;
                $i++;
            }

            return ['success' => true, 'transactions' => $transactionsArr];
        }

        return ['success' => false, 'transactions' => [], 'getTransactionsByMemberResult' => $getTransactionsByMemberResult];

    }

    public function updateCostProduct($arrProduct) {
        $costProduct  = 0;
        $currencyUniq = BillingExtServiceProvider::DEFAULT_CURRENCY_UNIQ;
        $count        = 0;
        $uidProduct   = isset($arrProduct['uid'])  ? $arrProduct['uid']  : null;
        $exeptDelete  = [];
        $uniqMember   = isset($arrProduct['uniqMember'])  ? $arrProduct['uniqMember']  : null;

        if (isset($arrProduct['billing']) && !empty($arrProduct['billing'])) {
            foreach ($arrProduct['billing'] as $one) {
                if (isset($one['cost']) && !empty($one['cost'])) {
                    $costProduct = $one['cost'];
                }
                if (isset($one['currency']) && !empty($one['currency'])) {
                    $currencyUniq = $one['currency'];
                }
                if (isset($one['count']) && !empty($one['count'])) {
                    $count = $one['count'];
                }

                $uid = isset($one['uniq']) ? $one['uniq'] : null;

                if (!BillingExtServiceProvider::checkProductUidCost($uid)) {
                    $last = _billing_products_cost::where('uid', '>', 0)->latest('uid')->first();

                    if ($last) {
                        $last = $last->uid + 1;
                    } else {
                        $last = 1;
                    }

                    $uid = $last;
                }

                $costProduct = (float)$costProduct;

                if (!BillingExtServiceProvider::checkCurrencyUniq($currencyUniq)) {
                     return BillingLoggerServiceProvider::addBillingLogRow(
                         'BillingServiceProvider:updateCostProduct',
                         ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_CURRENCY, 'currencyUniq' => $currencyUniq, 'arrProduct' => $arrProduct],
                         $uniqMember
                     );
                }

                $updateCostResult = _billing_products_cost::updateOrCreate(
                    [
                        'uid' => $uid
                    ],
                    [
                        'uniq_currency'     => $currencyUniq,
                        'uid_product'       => $uidProduct,
                        'date_created'      => BillingExtServiceProvider::getDateForTimestamp(),
                        'date_updated'      => BillingExtServiceProvider::getDateForTimestamp(),
                        'article'           => null,
                        'cost'              => $costProduct,
                        'count'             => $count,
                        'advanced'          => null
                    ]);

                if (!$updateCostResult) {
                    return BillingLoggerServiceProvider::addBillingLogRow(
                        'BillingServiceProvider:updateCostProduct',
                        ['success' => true, 'msg' => BillingExtServiceProvider::ERROR_UPDATE_PRODUCT_COST, 'uidProduct' => $uidProduct, 'costProduct' => $costProduct, 'currencyUniq' => $currencyUniq, 'count' => $count, 'arrProduct' => $arrProduct],
                        $uniqMember
                    );
                }

                $exeptDelete['uid_product'] = $uidProduct;
                $exeptDelete['uids'][]      = $uid;
            }

            _billing_products_cost::where('uid_product', $exeptDelete['uid_product'])->whereNotIn('uid', $exeptDelete['uids'])->delete();

            return BillingLoggerServiceProvider::addBillingLogRow(
                'BillingServiceProvider:updateCostProduct',
                ['success' => true, 'msg' => BillingExtServiceProvider::SUCCESS_UPDATE_PRODUCT_COST, 'arrProduct' => $arrProduct, 'exeptDelete' => $exeptDelete],
                $uniqMember
            );

        }

        return ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_MUST_BE_COST];

    }

    public function addCostToProductArr($product) {

        $table        = isset($product['table'])       ? $product['table']        : null;
        $uniq_table   = isset($product['uniq_table'])  ? $product['uniq_table']   : null;
        $code         = isset($product['code'])        ? $product['code']         : null;
        $uid_product  = isset($product['uid_product']) ? $product['uid_product']  : null;

        $product['billing'][0]['currency']   = BillingExtServiceProvider::DEFAULT_CURRENCY_UNIQ;;
        $product['billing'][0]['cost']       = 0;
        $product['billing'][0]['count']      = 0;
        $product['uidProduct']               = $uid_product;
        $productCosts                        = null;

        if (!is_null($uid_product)) {
            $productCosts = _billing_products_cost::where('uid_product', $uid_product)->get();
        } else {
            $productTable = _billing_products::where('table', $table)
                ->where('uniq_table', $uniq_table)
                ->where('code', $code)
                ->first();
            if ($productTable) {
                $productCosts           = _billing_products_cost::where('uid_product', $productTable->uid)->get();
                $product['uidProduct']  = $productTable->uid;
            }
        }

        if ($productCosts) {
            $i = 0;
            foreach($productCosts as $productCost) {
                $product['billing'][$i]['currency']   = $productCost['uniq_currency'];
                $product['billing'][$i]['cost']       = ceil($productCost['cost'] * 100) / 100;
                $product['billing'][$i]['count']      = $productCost['count'];
                $i++;
            }
        }

        return $product;
    }


    public function addBalanceByGateWay($data) {

        if (!is_array($data)) {
            $data = [];
        }

        $gateWay = isset($data['gateway']) ? $data['gateway'] : BillingExtServiceProvider::DEFAULT_GATEWAY;

        $data['pay_uniq'] = isset($data['pay_uniq']) ? $data['pay_uniq'] : null;
        $data['pay_sum']  = isset($data['pay_sum'])  ? (float)$data['pay_sum']  : 0;

        if (!BillingExtServiceProvider::checkMemberUniq($data['pay_uniq'])) {
            return BillingLoggerServiceProvider::addBillingLogRow(
                'BillingServiceProvider:addBalanceByGateWay',
                ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_MEMBER_UNIQ, 'uidProduct' => $data['pay_uniq']],
                $data['pay_uniq']
            );
        }

        if (!BillingExtServiceProvider::checkGateWay($gateWay)) {
            return BillingLoggerServiceProvider::addBillingLogRow(
                'BillingServiceProvider:addBalanceByGateWay',
                ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_GATEWAY, 'gateWay' => $gateWay],
                $data['pay_uniq']
            );
        }

        if ($data['pay_sum'] <= 0) {
            return BillingLoggerServiceProvider::addBillingLogRow(
                'BillingServiceProvider:addBalanceByGateWay',
                ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_SUMM_ZERO, 'pay_sum' => $data['pay_sum']],
                $data['pay_uniq']
            );
        }

        $gateWaysServicePrivider = new GateWaysServicePrivider($gateWay);

        if (is_object($gateWaysServicePrivider)) {
             $result = $gateWaysServicePrivider->doPayment($data);
        } else {
            $result = $gateWaysServicePrivider;
        }

        return $result;
    }

    public function getGateWay($uniq) {
        $gateWay = _billing_gateways::where('uniq', $uniq)->first();
        return $gateWay;
    }

    public function getCurrencyByUniq($uniq, $collum = null) {
        $currency = _billing_currences::where('uniq', $uniq)->first();
        if (isset($currency->$collum)) {
            return $currency->$collum;
        }
        return $currency;
    }

    public function getCurrencyForMember($memberUniq) {
        if (!BillingExtServiceProvider::checkMemberUniq($memberUniq)) {
            return BillingLoggerServiceProvider::addBillingLogRow(
                'BillingServiceProvider:getCurrencyForMember',
                ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_MEMBER_UNIQ, 'uniq_member' => $memberUniq]
            );
        }

        $member   = _members::where('uniq', $memberUniq)->first();
        $currency = _billing_currences::where('uniq', $member->currency_uniq)->first();

        if (is_null($currency)) {
            _members::where('uniq', $memberUniq)->update(['currency_uniq' => BillingExtServiceProvider::DEFAULT_CURRENCY_UNIQ]);
            $currency = _billing_currences::where('uniq', BillingExtServiceProvider::DEFAULT_CURRENCY_UNIQ)->first();
        }

        return $currency->attributesToArray();
    }

    public function getCurrencyList() {
        return _billing_currences::whereNotNull('uniq')->get()->toArray();
    }

    public function getGateWayList() {
        return _billing_gateways::whereNotNull('uniq')->get()->toArray();
    }


    public function getUidProductByCodeAndValue($code, $advanced_value) {

        if (!BillingExtServiceProvider::checkProductCode($code)) {
            return BillingLoggerServiceProvider::addBillingLogRow(
                'BillingServiceProvider:getUidProductByCodeAndValue',
                ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_PRODUCT_CODE, 'code' => $code, 'advanced_value' => $advanced_value]
            );
        }

        if (!BillingExtServiceProvider::checkProductAdvancedValue($advanced_value)) {
            return BillingLoggerServiceProvider::addBillingLogRow(
                'BillingServiceProvider:getUidProductByCodeAndValue',
                ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_PRODUCT_ADVANCED_VALUE, 'code' => $code, 'advanced_value' => $advanced_value]
            );
        }

        $product = _billing_products::where('code', $code)->where('advanced_value', $advanced_value)->first();

        if ($product) {
            return ['success' => true, 'uidProduct' => $product->uid, 'code' => $code, 'advanced_value' => $advanced_value];
        } else {
            return BillingLoggerServiceProvider::addBillingLogRow(
                'BillingServiceProvider:getUidProductByCodeAndValue',
                ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_PRODUCT_NOT_FOUND, 'code' => $code, 'advanced_value' => $advanced_value]
            );
        }
    }


    public function getUidProductByCode($code) {
        if (!BillingExtServiceProvider::checkProductCode($code)) {
            return BillingLoggerServiceProvider::addBillingLogRow(
                'BillingServiceProvider:getUidProductByCode',
                ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_PRODUCT_CODE, 'code' => $code]
            );
        }

        $product = _billing_products::where('code', $code)->first();

        if ($product) {
            return ['success' => true, 'uidProduct' => $product->uid, 'code' => $code, 'advanced_value' => $advanced_value];
        } else {
            return BillingLoggerServiceProvider::addBillingLogRow(
                'BillingServiceProvider:getUidProductByCode',
                ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_PRODUCT_NOT_FOUND, 'code' => $code]
            );
        }
    }




}
