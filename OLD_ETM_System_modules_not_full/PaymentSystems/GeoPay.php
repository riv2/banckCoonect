<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| ETM-System Software                                                         |
| Copyright (c) 2011-2013 UIMG LTD <webmaster@uimg.com.ua>                    |
| All rights reserved.                                                        |
|                                                                             |
| No part of this software or any of its contents may be reproduced, copied,  |
| modified or adapted, without the prior written consent of the author,       |
| unless otherwise indicated for stand-alone materials.                       |
+-----------------------------------------------------------------------------+
 */



class PaymentGeoPay extends PaymentBase {

    protected $passKey = '';
    protected $linkGeoPay = 'https://payment.geopaysoft.com/result/maviage/pay.php';
    protected $currencyAllow = array('GEL');
    protected $langs = array('ka','en');

    public function __construct($orderId) {
        $this->pid     = 20;
        $this->orderId = $orderId;
        parent::__construct();
    }

    /**
     * Start payment GeoPay
     * @param
     * @return array
     */
    public function run() {
        if (!$this->orderId) {
            return array(
                'status' => 'error',
                'response' => FuncLang::value('err_order_not_found')
            );
        }

        try {
            $isSite = 0;
            $siteId = WtDB::Ref()->OrdersValue(new WtMapArgs('field', 'site_id', 'id', $this->orderId));
            if (!empty($siteId)) {
                $isSite = 1;
            }
            $mtranzaction = $this->orderId.'_'.$isSite;

            $rsp   = array('status' => '',
                           'response' => '');

            $currency = $this->order['customer_currency'];

            if (!in_array($currency, $this->currencyAllow)) {
                $rsp   = array('status' => 'error',
                               'response' => 'Not allowed Currency');
            }

            $price = $this->getPrice();
            $price    = sprintf('%.2f', $price) * 100;

            if (!$price || $price <= 0) {
                $rsp = array('status' => 'error',
                             'response' => 'Can`t get price');
            }

            $langcode = WtSession::Ref()->locale;

            if (!in_array($langcode, $this->langs)) {
                $langcode = 'EN';
            }

            $hash = md5($mtranzaction . $currency . $langcode . $price . $this->passKey);

            $params = array(
                'mtranzaction' => $mtranzaction,
                'currency'     => $currency,
                'langcode'     => $langcode,
                'amount'       => $price,
                'hash'         => $hash
            );

            WtDB::Ref()->OrderLogInsert(new WtFuncArgs(array(
                'oid'     => $this->orderId,
                'action'  => 'psp',
                'details' => array(
                    'type'  => 'GeoPay register request',
                    'value' => array(
                        'merchantOrderNumber' => $this->orderId,
                        'amount'              => $price,
                        'currency'            => $currency,
                        'hash'                => $hash
                    )
                )
            )));

            if ($rsp['status'] == 'error') {
                return array(
                    'status' => 'error',
                    'response' => $rsp['response']
                );
            }

            $answer = $this->goQuery($params);

            if ($answer['error'] != '') {
                $rsp  = array('status' => 'error',
                              'response' => $answer['error']);
            } else {

                if (strstr($answer['answer'], "<html>")) {

                    $rsp  = array('status' => 'ok',
                                  'response' => $answer['answer']);

                    $postform = $this->bildForm($answer['answer']);

                    if (empty($postform['form'])) {
                        $rsp  = array('status' => 'error',
                                      'response' => 'Can`t find form');
                    }

                    if (empty($postform['transactionId'])) {
                        $rsp  = array('status' => 'error',
                                      'response' => 'Can`t find transactionId');
                    }

                } else {
                    $answer = XML2Array::createArray($answer['answer']);
                    $rsp  = array('status' => 'error',
                                  'response' => $answer['result']['error']['mtranzaction']);
                }
            }

           $log = serialize($rsp);

            if ($rsp['status'] == 'ok') {
                $log = 'status = ok'; 
                    WtDB::Ref()->OrderLogInsert(new WtFuncArgs(array(
                    'oid'     => $this->orderId,
                    'action'  => 'psp',
                    'details' => array('type'  => 'GeoPay response ok', 'value' => $log)
                )));
                $transactionId = str_replace("+", " ", $postform['transactionId']);
                WtDB::Ref()->OrderPaymentMethodUpdate(new WtFuncArgs(array(
                    'status'   => 'N',
                    'trans'    => $transactionId,
                    'order_id' => $this->orderId
                )));

                return array(
                    'status' => 'form',
                    'result' => $postform['form']
                );

            }

            WtDB::Ref()->OrderLogInsert(new WtFuncArgs(array(
                'oid'     => $this->orderId,
                'action'  => 'psp',
                'details' => array('type'  => 'GeoPay response error', 'value' => $log)
            )));

            $bid = WtDB::Ref()->OrderBooksValue(new WtMapArgs('order', $this->orderId));
            FuncOrder::cancelBills($bid, true, false);
            $delete = WtDB::Ref()->OrderPaymentMethodDelete(new WtMapArgs('oid', $this->orderId));
           // dumpLog ($delete, 'run $delete', 'geo_cback.log');
            return array(
                'status'   => 'error',
                'response' => $rsp['response']
            );

        } catch (SoapFault $e) {
            $bid = WtDB::Ref()->OrderBooksValue(new WtMapArgs('order', $this->orderId));
            FuncOrder::cancelBills($bid, true, false);
            $delete = WtDB::Ref()->OrderPaymentMethodDelete(new WtMapArgs('oid', $this->orderId));
           // dumpLog ($delete, 'run $delete2', 'geo_cback.log');
            WtDB::Ref()->OrderLogInsert(new WtFuncArgs(array(
                'oid'     => $this->orderId,
                'action'  => 'psp',
                'details' => array(
                    'type'  => 'GeoPay register catch Exception',
                    'value' => array(
                        'code'   => $e->faultcode,
                        'string' => $e->faultstring
                    )
                )
            )));

            return array(
                'status'   => 'error',
                'response' => $e->faultcode . ': ' . $e->faultstring
            );
        }
    }

    /**
     * Send query to GeoPay
     * @param array
     * @return array
     */
    private function goQuery($params) {
        $response = array('answer' => '',
                          'error'  => '');
       // dumpLog ($this->linkGeoPay . '?' . WtFunc::buildQueryString($params), 'linkGeoPay goQuery', 'geo_cback.log');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->linkGeoPay . '?' . WtFunc::buildQueryString($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $response['answer'] = curl_exec($ch);
        $response['error']  = curl_error($ch);

        if (!empty($response['error'])) {
            WtDB::Ref()->OrderLogInsert(new WtFuncArgs(array(
                'oid'     => $this->orderId,
                'action'  => 'psp',
                'details' => array(
                    'type'  => 'GeoPay status curl error',
                    'value' => $response['error']
                )
            )));
        }
        return $response;
    }

    /**
     * Bild form for GeoPay
     * @param array
     * @return array
     */
    private function bildForm($form) {

        $action = 'https://securepay.ufc.ge/ecomm2/ClientHandler';
        $name = 'trans_id';

        preg_match_all('/<input[^>]+value=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/', $form, $matches);
        $value = $matches[2][0];

        if (!$value) {
            return false;
        }
        if (WtSession::Ref()->stype() == 'site') {
            $form = array(
                    'action' => $action,
                    'fields' => array(
                            $name => $value
                        ),
                );
        } else {
            $form = '<form method="POST" action="'. $action .'" id="psp-pay-frm" >';
            $form .= '<input type="hidden" name="' . $name . '" value="' . $value . '">';
            $form .= '</form>';
        }

        $return = array('form' => $form,
                        'transactionId' => $value);
//dumpLog ($return, 'bildForm bildForm', 'geo_cback.log');
        return $return;
    }

    /**
     * Do payment to ETM from GeoPay
     * @param array
     * @return array
     */
    public function payment($params) {

      //  dumpLog ($params, '$params payment', 'geo_cback.log');

        WtDB::Ref()->OrderLogInsert(new WtFuncArgs(array(
            'oid'     => $this->orderId,
            'action'  => 'psp',
            'details' => array('type'  => 'GeoPay payment process', 'value' => serialize($params))
        )));

        $ok = WtDB::Ref()->OrderPaymentMethodUpdate(new WtFuncArgs(array(
            'status'   => 'P',
            'order_id' => $this->orderId,
            'trans'     => $params['BANKTRANSACTIONID']
        )));

      //  dumpLog ($ok, '$ok payment', 'geo_cback.log');

        if ($ok) {

            WtDB::Ref()->OrderLogInsert(new WtFuncArgs(array(
                'oid'     => $this->orderId,
                'action'  => 'psp',
                'details' => array('type'  => 'GeoPay payment process success', 'value' => serialize($params))
            )));

            $ticket = WtDB::Ref()->OrderTicketsValue(new WtMapArgs('orderid', $this->orderId));
            WtBilling::init(new Billing($ticket));
            WtBilling::Ref()->PayBills($this->orderId);

            return true;

        } else {

            WtDB::Ref()->OrderLogInsert(new WtFuncArgs(array(
                'oid'     => $this->orderId,
                'action'  => 'psp',
                'details' => array('type'  => 'GeoPay payment process fail', 'value' => serialize($params))
            )));

            $bid = WtDB::Ref()->OrderBooksValue(new WtMapArgs('order', $this->orderId));
            FuncOrder::cancelBills($bid, true, false);
            $updateStatus = WtDB::Ref()->OrderPaymentMethodUpdate(new WtFuncArgs(array(
                'status'   => 'R',
                'order_id' => $this->orderId,
                'trans'     => $params['BANKTRANSACTIONID']
            )));
        //    dumpLog ($ok, '$updateStatus payment error', 'geo_cback.log');
            return false;

        }
    }


    /**
     * reverse payment from GeoPay
     * @param array
     * @return boolean
     */
    public function reverse() {
        return $this->reject(array('reverse'));
    }

    /**
     * refund payment from GeoPay
     * @param boolean
     * @return boolean
     */
    public function refund() {
        return $this->reject(array('refund'));
    }

    /**
     * reject payment from GeoPay
     * @param array
     * @return boolean
     */
    public function reject($params) {
        WtDB::Ref()->OrderLogInsert(new WtFuncArgs(array(
            'oid'     => $this->orderId,
            'action'  => 'psp',
            'details' => array('type'  => 'GeoPay response reject', 'value' => serialize($params))
        )));

       // dumpLog ($params, $this->orderId . ' $this->orderId reject', 'geo_cback.log');
        $bid = WtDB::Ref()->OrderBooksValue(new WtMapArgs('order', $this->orderId));
        $bills = FuncOrder::cancelBills($bid, true, false);
        $updateStatus = WtDB::Ref()->OrderPaymentMethodUpdate(new WtFuncArgs(array(
            'status'   => 'R',
            'order_id' => $this->orderId,
        )));

      //  dumpLog ($bid, ' $bid reject', 'geo_cback.log');
      //  dumpLog ($bills, ' $bills reject', 'geo_cback.log');
     //   dumpLog ($updateStatus, ' $updateStatus reject', 'geo_cback.log');

        return true;
    }

    /**
     * check payment from GeoPay
     * @param array
     * @return boolean
     */
    public function check($params) {
        WtDB::Ref()->OrderLogInsert(new WtFuncArgs(array(
            'oid'     => $this->orderId,
            'action'  => 'psp',
            'details' => array('type'  => 'GeoPay payment check', 'value' => serialize($params))
        )));

        if (!$this->orderId) {
            return false;
        }

        $trans = WtDB::Ref()->OrderPaymentMethodValue(new WtFuncArgs(array(
            'oid'    => $this->orderId,
            'trans'  => $params['BANKTRANSACTIONID'],
            'status' => 'N',
            'field'  => 'o.transaction_num'
        )));

     //   dumpLog ($params, '$params check', 'geo_cback.log');
     //   dumpLog ($params['BANKTRANSACTIONID'], 'BANKTRANSACTIONID check', 'geo_cback.log');

        if ($trans) {
            return true;
        }

    return false;

    }

    /**
     * Do check signature from GeoPay
     * @param array
     * @return boolean
     */
    public function checkSign($params) {
        WtDB::Ref()->OrderLogInsert(new WtFuncArgs(array(
            'oid'     => $this->orderId,
            'action'  => 'psp',
            'details' => array('type'  => 'GeoPay payment checkSign', 'value' => serialize($params))
        )));

        $hash = md5($params['MERCHANTTRANSACTIONID'] .
            $params['RESULT'] .
            $params['RESULTCODE'] .
            $params['RRN'] .
            $params['CARDNUMBER'] .
            $this->passKey);

      //  dumpLog ($hash, '$hash checkSign', 'geo_cback.log');
     //   dumpLog ($params['SIGNATURE'], 'SIGNATURE checkSign', 'geo_cback.log');

        if ($hash == $params['SIGNATURE']) {
            return true;
        }

        return false;
    }


}
