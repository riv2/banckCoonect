<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Models\_billing_transactions;
use App\Models\_billing_products;
use App\Models\_billing_products_cost;

class TransactionServiceProvider extends ServiceProvider
{

    public function __construct()
    {
    }

    public function addTransaction($transactionArr) {

        $uniqMember      = isset($transactionArr['uniqMember'])      ? $transactionArr['uniqMember']      : null;
        $uidProduct      = isset($transactionArr['uidProduct'])      ? $transactionArr['uidProduct']      : null;
        $sum             = isset($transactionArr['sum'])             ? $transactionArr['sum']             : 0;
        $signature       = isset($transactionArr['signature'])       ? $transactionArr['signature']       : null;
        $typeTransaction = isset($transactionArr['typeTransaction']) ? $transactionArr['typeTransaction'] : null;
        $hideTransaction = isset($transactionArr['hideTransaction']) ? $transactionArr['hideTransaction'] : null;

        if (!BillingExtServiceProvider::checkMemberUniq($uniqMember)) {
            return ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_MEMBER_UNIQ, 'transactionArr' => $transactionArr];
        }

        if (!BillingExtServiceProvider::checkProductUid($uidProduct)) {
            return ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_PRODUCT_UID, 'transactionArr' => $transactionArr];
        }

        if (!is_null($signature) && !BillingExtServiceProvider::checkTransactionSignature($signature)) {
            return ['success' => false, 'msg' => BillingExtServiceProvider::DUBLICATE_TRANSACTION, 'transactionArr' => $transactionArr];
        }

        if (!BillingExtServiceProvider::checkTypeTransaction($typeTransaction)) {
            return ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_TYPE_TRANSACTION, 'transactionArr' => $transactionArr];
        }

        if (!BillingExtServiceProvider::checkTransactionSum($sum)) {
            return ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_TRANSACTION_SUM, 'transactionArr' => $transactionArr];
        }

        $sum          = (float)$sum;
        $product      = _billing_products::where('uid', $uidProduct)->get()->toArray();
        $product_cost = _billing_products_cost::where('uid_product', $uidProduct)->get()->toArray();

        $productSerialize = serialize(['product' => $product, 'product_cost' => $product_cost]);

        $transactionResult = _billing_transactions::create([
            'uniq_member'       => $uniqMember,
            'uid_product'       => $uidProduct,
            'sum'               => $sum,
            'date_created'      => BillingExtServiceProvider::getDateForTimestamp(),
            'date'              => BillingExtServiceProvider::getDateForTimestamp(true),
            'product_serialize' => $productSerialize,
            'signature'         => $signature,
            'type_transaction'  => $typeTransaction,
            'hide_transaction'  => $hideTransaction
        ]);

        if($transactionResult) {
            $transactionArr['transactionUid'] = $transactionResult->uid;
            return BillingLoggerServiceProvider::addBillingLogRow(
                'TransactionServiceProvider:addTransaction',
                ['success' => true, 'msg' => BillingExtServiceProvider::SUCCESS_ADD_TRANSACTION, 'transactionUid' => $transactionResult->uid, 'transactionArr' => $transactionArr],
                $uniqMember
            );
        }

        return BillingLoggerServiceProvider::addBillingLogRow(
            'TransactionServiceProvider:addTransaction',
            ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_ADD_TRANSACTION, 'transactionArr' => $transactionArr],
            $uniqMember
        );

    }

    public function getTransactionsByMember($uniqMember, $limit = BillingExtServiceProvider::LIMIT_ALL) {

        if (!BillingExtServiceProvider::checkMemberUniq($uniqMember)) {
            return ['success' => false, 'msg' => BillingExtServiceProvider::ERROR_MEMBER_UNIQ, 'uniqMember' => $uniqMember, 'limit' => $limit];
        }

        $transactions = _billing_transactions::where('uniq_member', $uniqMember)->whereNull('hide_transaction')->latest('date_created')->limit($limit)->get();

        return ['success' => true, 'msg' => BillingExtServiceProvider::SUCCESS_GET_TRANSACTIONS, 'transactions' => $transactions, 'limit' => $limit, 'uniqMember' => $uniqMember];

    }


}
