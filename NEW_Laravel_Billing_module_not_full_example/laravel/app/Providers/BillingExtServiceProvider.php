<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Models\_billing_currences;
use App\Models\_billing_transactions;
use App\Models\_billing_products;
use App\Models\_members;

class BillingExtServiceProvider extends ServiceProvider
{
    const FREE_SUBSCRIBE_UID               = 2;
    const LIMIT_ALL                        = 999999;
    const MANUAL_GATEWAY_UNIQ              = 'MANUAL';
    const DEFAULT_GATEWAY                  = 'Robokassa';
    const ROBOKASSA_GATEWAY                = 'Robokassa';

    const DEFAULT_CURRENCY_UNIQ            = 'RUB';

    const TABLE_SUBSCRIBE                  = '_subscribes';
    const TABLE_PRODUCTS                   = '_billing_products';

    const RENDER_OPTION_NAME               = 'RENDER_AMOUNT';
    const AREA_COST_OPTION_NAME            = 'AREA_COST';
    const RENDER_COST_OPTION_NAME          = 'RENDER_COST';

    const ERROR_MEMBER_UNIQ                = 'ERROR_MEMBER_UNIQ';
    const ERROR_INIT_MEMBER_UNIQ           = 'ERROR_INIT_MEMBER__UNIQ';
    const ERROR_GATEWAY                    = 'ERROR_GATEWAY';
    const ERROR_CURRENCY                   = 'ERROR_CURRENCY';
    const ERROR_TYPE_TRANSACTION           = 'ERROR_TYPE_TRANSACTION';
    const ERROR_TYPE_CUSTOMER              = 'ERROR_TYPE_CUSTOMER';
    const ERROR_ADD_BALANCE                = 'ERROR_ADD_BALANCE';
    const ERROR_RECALC_BALANCE             = 'ERROR_RECALC_BALANCE';
    const ERROR_UPDATE_BALANCE             = 'ERROR_UPDATE_BALANCE';
    const ERROR_GET_BALANCE                = 'ERROR_GET_BALANCE';
    const ERROR_TOTAL_SUM                  = 'ERROR_TOTAL_SUM';
    const ERROR_PRODUCT                    = 'ERROR_PRODUCT';
    const ERROR_PRODUCT_UID                = 'ERROR_PRODUCT_UID';
    const ERROR_PRODUCT_TYPE               = 'ERROR_PRODUCT_TYPE';
    const ERROR_TRANSACTION_SUM            = 'ERROR_TRANSACTION_SUM';
    const ERROR_PRODUCT_COST               = 'ERROR_PRODUCT_COST';
    const ERROR_PRODUCT_COST_UID           = 'ERROR_PRODUCT_COST_UID';
    const ERROR_UPDATE_PRODUCT_COST        = 'ERROR_UPDATE_PRODUCT_COST';
    const ERROR_ADD_COST_PRODUCT           = 'ERROR_ADD_COST_PRODUCT';
    const ERROR_INSUFFICIENT_FUNDS         = 'ERROR_INSUFFICIENT_FUNDS';
    const ERROR_COUNT_PRODUCT              = 'ERROR_COUNT_PRODUCT';
    const ERROR_SUMM_ZERO                  = 'ERROR_PRODUCT_COST_EQ_ZERO';
    const ERROR_REQUEST                    = 'ERROR_REQUEST';
    const ERROR_GROUP_SIGNATURE            = 'ERROR_GROUP_SIGNATURE';
    const ERROR_PRODUCT_TABLE              = 'ERROR_PRODUCT_TABLE';
    const ERROR_MUST_BE_COST               = 'ERROR_MUST_BE_COST';
    const ERROR_FIRST_STEP                 = 'ERROR_FIRST_STEP';
    const ERROR_ADD_TRANSACTION            = 'ERROR_ADD_TRANSACTION';
    const ERROR_ADVANCED_TRANSACTION       = 'ERROR_ADVANCED_TRANSACTION';
    const ERROR_USAGE                      = 'ERROR_USAGE';
    const ERROR_MEMBER_UNIQ_NOT_FOUND      = 'ERROR_MEMBER_UNIQ_NOT_FOUND';
    const ERROR_PRODUCT_CODE               = 'ERROR_PRODUCT_CODE';
    const ERROR_PRODUCT_CODE_NOT_FOUND     = 'ERROR_PRODUCT_CODE_NOT_FOUND';
    const ERROR_PRODUCT_NOT_FOUND          = 'ERROR_PRODUCT_NOT_FOUND';
    const ERROR_PRODUCT_ADVANCED_VALUE     = 'ERROR_PRODUCT_ADVANCED_VALUE';

    const SUCCESS_ADD_BALANCE              = 'SUCCESS_ADD_BALANCE';
    const SUCCESS_ADD_TRANSACTION          = 'SUCCESS_ADD_TRANSACTION';
    const SUCCESS_ADD_RENDERS              = 'SUCCESS_ADD_RENDERS';
    const SUCCESS_RECALC_BALANCE           = 'SUCCESS_RECALC_BALANCE';
    const SUCCESS_REQUEST                  = 'SUCCESS_REQUEST';
    const SUCCESS_UPDATE_PRODUCT_COST      = 'SUCCESS_UPDATE_PRODUCT_COST';
    const SUCCESS_UPDATE_BALANCE           = 'SUCCESS_UPDATE_BALANCE';
    const SUCCESS_GET_TRANSACTIONS         = 'SUCCESS_GET_TRANSACTIONS';

    const CODE_MANUAL_ADD_PRODUCT          = 'ADD_MANUAL_BALANCE';
    const CODE_GATEWAY_ADD_PRODUCT         = 'ADD_GATEWAY_BALANCE';
    const CODE_SUBSCRIBE_COST_PRODUCT      = 'SUBSCRIBE_COST';
    const CODE_REMAIN_ADD_PRODUCT          = 'ADD_REMAIN_BALANCE';
    const CODE_COINS_COST_PRODUCT          = 'COINS_COST';

    const CODE_COINS_SUBSCRIBE_AMOUNT      = 'COINS_AMOUNT';
    const CODE_PROJECT_SUBSCRIBE_AMOUNT    = 'PROJECT_AMOUNT';
    const CODE_RENDER_SUBSCRIBE_AMOUNT     = 'RENDER_AMOUNT';

    const STATUS_BUY_PRODUCT               = 'B'; // buy product
    const STATUS_ADD_BALANCE               = 'A'; // add balance
    const STATUS_REMAIN_PRODUCT            = 'R'; // refund product
    const STATUS_GATEWAY_ADD_BALANCE       = 'G'; // gateway add balance

    const DUBLICATE_TRANSACTION            = 'DUBLICATE_TRANSACTION';

    public static $typesCustomer    = ['C', 'M']; // C - company, M - member,
    public static $typesTransaction = ['A', 'B', 'R', 'G']; // A - add balance, B - buy product, R - refund product, G - gateway add balance

    public static $gateWaysList        = ['robokassa', 'manual'];
    public static $allowProductTables  = ['_subscribes', '_billing_products'];

    public function __construct()
    {
    }

    public static function getDateForTimestamp($onlyDate = false) { // todo
        $date = ($onlyDate) ? date('Y-m-d') : date('Y-m-d H:i:s');
        return $date;
    }

    public static function generateUniq() { // todo
        $uniq = rand(1, 1000) . '-' . rand(1, 1000);
        return $uniq;
    }

    public static function checkMemberUniq($memberUniq){
        $member = _members::where('uniq', $memberUniq)->first();
        if (!is_null($member)) {
            return true;
        }
        return false;
    }

    public static function checkInitmemberUniq($initmemberUniq){
        $initmemberUniq = _members::where('uniq', $initmemberUniq)->first();
        if (!is_null($initmemberUniq)) {
            return true;
        }
        return false;
    }

    public static function checkGateWayUniq($gateWayUniq){
        return true; //todo
    }

    public static function checkGateWay($gateWay){
        if(in_array(mb_strtolower($gateWay), BillingExtServiceProvider::$gateWaysList)) {
            return true; //todo
        }
        return false;
    }


    public static function checkCurrencyUniq($currencyUniq){
        $currency = _billing_currences::where('uniq', $currencyUniq)->first();
        if (!is_null($currency)) {
            return true;
        }
        return false;
    }

    public static function checkProductUid($uidProduct){
        $product = _billing_products::where('uid', $uidProduct)->first();
        if (!is_null($product)) {
            return true;
        }
        return false;
    }

    public static function checkProductUidCost($productUniqCost){
        if (!is_null($productUniqCost) && !empty($productUniqCost)) {
            return true;
        }

        return false;
    }

    public static function checkTypeTransaction($typeTransaction){
        if (in_array($typeTransaction, self::$typesTransaction)) {
            return true;
        }
        return false;
    }

    public static function checkTransactionSignature($signature){
        $result = _billing_transactions::where('signature', $signature)->first();
        if (is_null($result)) {
            return true;
        }
        return false;
    }

    public static function checkTransactionGroupSignature($groupSignature){
        if (!is_null($groupSignature) && !empty($groupSignature)) {
            return true;
        }
        return false;
    }

    public static function checkTransactionTypeProduct($type_product){
        if (!is_null($type_product) && !empty($type_product)) {
            return true;
        }
        return false;
    }

    public static function checkProductTable($table){
        if (in_array($table, self::$allowProductTables)) {
            return true;
        }
        return false;
    }

    public static function checkTransactionSum($sum) {
        if (is_numeric($sum)) {
            return true;
        }
        return false;
    }

    public static function checkProductCode($code) {
        //todo
        return true;
    }

    public static function checkProductAdvancedValue($advanced_value) {
        //todo
        return true;
    }










}