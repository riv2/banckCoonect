<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| ETM-System Software                                                         |
| Copyright (c) 2011-2016 E-Tickets Service GmbH <support@etm-system.ru>      |
| All rights reserved.                                                        |
|                                                                             |
| No part of this software or any of its contents may be reproduced, copied,  |
| modified or adapted, without the prior written consent of the author,       |
| unless otherwise indicated for stand-alone materials.                       |
+-----------------------------------------------------------------------------+
 */


class WtAppCommonInsurance extends WtAppCommon {


    function initialize(WtFuncArgs $args = null) {
        parent::initialize($args);
        $this->public_actions    = array();
        $this->protected_actions = array('browse' => '');
        $this->css_files         = array('insurance.css');
    }


    function default_public_action() {
        $this->header_location(WtConfig::Get('HTTP_ROOT'));

    }


    function default_protected_action() {

        $this->browse_action();

    }

    private function TES() {
        $operator = WtSession::Ref()->UserConfig('insurance');
        $inWork = WtDB::Ref()->InsuranceConfigRows(new WtMapArgs('fields', 'in_work', 'operator', $operator));
       // $inWork = array('avit','aviatur','wings');
        if ($inWork == 'Y') {
            $TES = new WtAlfastrahAPI ('work');
        } else {
            $TES = new WtAlfastrahAPI ();
        }
        return $TES;
    }

   public function browse_action() {

        $args = new WtFuncArgs(WtForm::Get());
        $orderId = $args->id;
        $type = $args->type;
        $pid = $args->pid;
        $flightCount = 0;
        $order['type'] = 'manually';

        if ($type == 'flight') {

            $passenger = WtDB::Ref()->OrderPassengersRow(new WtMapArgs('id', $pid));
            $passenger['birth_date'] = self::formatDateOther($passenger['birth_date']);
            $order        = WtDB::Ref()->OrdersRow(new WtMapArgs('id', $orderId));

            $order_tickets  = WtDB::Ref()->GetOrderTickets(new WtMapArgs('order_id', $orderId, 'passenger_id', $pid));

            if ($order_tickets) {
                $order['insuredTicketNumber'] = $order_tickets[0]['number'];
            } else {
                $order['insuredTicketNumber'] = '';
            }

            $result_ids = explode('_',$order['result_ids']);
            $segmentsfromDB = WtDB::Ref()->TransactionsResultRows(new WtMapArgs('ids', $result_ids));
            $segments = array();
            $i = 1;
            foreach ($segmentsfromDB as $one) {

                if ($one['first_departure_date'] != null) {
                    $segments[$i]['departure_date'] = self::formatDateOther($one['first_departure_date']);
                    $segments[$i]['departure_time'] = substr ($one['first_departure_time'], 0, 5);
                    $segments[$i]['arrival_date'] = self::formatDateOther($one['first_arrival_date']);
                    $segments[$i]['arrival_time'] = substr ($one['first_arrival_time'], 0, 5);
                    $segments[$i]['flight_number'] = $one['first_flight_number'];
                    $segments[$i]['departure_airport'] = $one['first_departure_airport'];
                    $segments[$i]['arrival_airport'] = $one['first_arrival_airport'];
                    $segments[$i]['arrivalCountry'] =  WtDB::Ref()->CountryValue(new WtMapArgs('airport', $segments[$i]['arrival_airport']));
                    $segments[$i]['departureCountry'] =  WtDB::Ref()->CountryValue(new WtMapArgs('airport', $segments[$i]['departure_airport']));
                    $segments[$i]['operating_airline'] = $one['first_operating_airline'];
                    $i++;
                }

                if ($one['second_departure_date'] != null) {
                    $segments[$i]['departure_date'] = self::formatDateOther($one['second_departure_date']);
                    $segments[$i]['departure_time'] = substr ($one['second_departure_time'], 0, 5);
                    $segments[$i]['arrival_date'] = self::formatDateOther($one['second_arrival_date']);
                    $segments[$i]['arrival_time'] = substr ($one['second_arrival_time'], 0, 5);
                    $segments[$i]['flight_number'] = $one['second_flight_number'];
                    $segments[$i]['departure_airport'] = $one['second_departure_airport'];
                    $segments[$i]['arrival_airport'] = $one['second_arrival_airport'];
                    $segments[$i]['arrivalCountry'] =  WtDB::Ref()->CountryValue(new WtMapArgs('airport', $segments[$i]['arrival_airport']));
                    $segments[$i]['departureCountry'] =  WtDB::Ref()->CountryValue(new WtMapArgs('airport', $segments[$i]['departure_airport']));
                    $segments[$i]['operating_airline'] = $one['second_operating_airline'];
                    $i++;
                }

                if ($one['third_departure_date'] != null) {
                    $segments[$i]['departure_date'] = self::formatDateOther($one['third_departure_date']);
                    $segments[$i]['departure_time'] = substr ($one['third_departure_time'], 0, 5);
                    $segments[$i]['arrival_date'] = self::formatDateOther($one['third_arrival_date']);
                    $segments[$i]['arrival_time'] = substr ($one['third_arrival_time'], 0, 5);
                    $segments[$i]['flight_number'] = $one['third_flight_number'];
                    $segments[$i]['departure_airport'] = $one['third_departure_airport'];
                    $segments[$i]['arrival_airport'] = $one['third_arrival_airport'];
                    $segments[$i]['arrivalCountry'] =  WtDB::Ref()->CountryValue(new WtMapArgs('airport', $segments[$i]['arrival_airport']));
                    $segments[$i]['departureCountry'] =  WtDB::Ref()->CountryValue(new WtMapArgs('airport', $segments[$i]['departure_airport']));
                    $segments[$i]['operating_airline'] = $one['third_operating_airline'];
                    $i++;
                }
            }
            $flightCount = count($segments);

        }

        if ($type == 'railway') {
            $order_tmp        = WtDB::Ref()->RailwayOrdersReportInfo(new WtMapArgs('id', $orderId));
            $order = array();
            $order['type'] = 'rail';
            $order['passengers'][0]['firstname'] =  $order_tmp['tickets'][0]['name'];
            $order['passengers'][0]['lastname'] =  $order_tmp['tickets'][0]['last_name'];
            $order['passengers'][0]['patronymic'] =  $order_tmp['tickets'][0]['middle_name'];
            if ($order_tmp['tickets'][0]['doc_type'] == 33 ) $order['passengers'][0]['insuredDocumentType'] = 'PSP';
            $order['passengers'][0]['doc_number'] =  $order_tmp['tickets'][0]['doc_number'];
            $order['passengers'][0]['birth_date'] = $order_tmp['tickets'][0]['birthdate'];
            $order_number['number'] = $order_tmp['tickets'][0]['ticket_number'];
            $order['buyer_phonenumber'] =  WtCrypt::Ref()->decode($order_tmp['buyer_phonenumber']);
            $order['buyer_email'] = WtCrypt::Ref()->decode($order_tmp['buyer_email']);
            $order['buyer_address'] =  WtCrypt::Ref()->decode($order_tmp['buyer_address']);
            $order['buyer_address'] =  WtCrypt::Ref()->decode($order_tmp['buyer_address']);


           if ($order_tmp['tickets'][0]['gender'] == 'M') $order['passengers'][0]['title'] = 'MR';
           if ($order_tmp['tickets'][0]['gender'] == 'F')  $order['passengers'][0]['title'] = 'MS';

            $order['train_number'] = $order_tmp['train_number'];
            $order['placeNumber'] =  $order_tmp['tickets'][0]['seat'];
            $departureDate = explode(' ',$order_tmp['dep_datetime']);
            $order['departureTime'] = $departureDate[2];
            $order['departureTime'] = explode('<br>',$order['departureTime']);
            $mounth =  self::formatDateMounth($departureDate[1]);
            $order['departureDate'] = $departureDate[0].'.'.$mounth.'.'.$order['departureTime'][0];
            $order['departureTime'] = $order['departureTime'][1];

            $arrDate = explode(' ',$order_tmp['arr_datetime']);
            $order['arrDate'] = $arrDate[0];
            $order['arrTime'] = $arrDate[2];
            $order['arrTime'] = explode('<br>',$order['arrTime']);
            $mounth =  self::formatDateMounth($arrDate[1]);
            $order['arrDate'] = $arrDate[0].'.'.$mounth.'.'.$order['arrTime'][0];
            $order['arrTime'] = $order['arrTime'][1];

            $order['station0'] = $order_tmp['station0'];
            $order['station1'] = $order_tmp['station1'];
            $order['car_number'] = $order_tmp['car_number'];
            $order['car_type'] = $order_tmp['car_type'];
            $order['car_class'] = $order_tmp['car_class'];

        }

        $operator = WtSession::Ref()->UserConfig('insurance');
        $products = WtDB::Ref()->InsuranceConfigRows(new WtMapArgs('operator', $operator));
        $productsList = array();

        $i = 0;

        foreach ($products as $item) {

            if ($item['type'] == 'no') { // for not ready products
                continue;
            }

            if ($item['type'] == 'NSP' and $order['status'] != 'T' ) { // for NSP products
                continue;
            }

            if ($type == 'flight') { // for flight products

            }

            if ($type == 'railway') { // for railway products

            }

            $productsList[$i]['name'] = $item['name'];
            $productsList[$i]['code'] = $item['data'];
            $productsList[$i]['type'] = $item['type'];

         $i++;

        }

        $order['type'] = $type;

        $this->js_files  = array('insurance.js','datepicker.js','validator.js');
        $tplArgs = new WtMapArgs(
                                'menu_active', 'search',
                                'sub_menu_active', 'insurance',
                                'pg_title',  array(FuncLang::value('lbl_orders'), FuncLang::value('lbl_insurance_search')),
                                'products', $productsList,
                                'country', WtDB::Ref()->InsuranceCountryRows(),
                                'operator', $operator,
                                'order', $order,
                                'flightCount', $flightCount,
                                'passenger', $passenger,
                                'segments', json_encode($segments)
                            );

        $this->js_cfg_file       = 'insurance';
        $this->publish(new WtPublishParams('insurance/form'), new WtPublishArgs($tplArgs));

   }


   public function ajax_action() {

    $args = new WtFuncArgs(WtForm::Get());

    if ($args->type == 'calculatePolicy') { // Запрос на расчет стоимости страховки

        $policyParameters = self::preparePolicyParameters($args->args);

        if (isset($policyParameters['riskValueSum'])) {
            $policyParameters['riskValue'] = array("value" => $policyParameters['riskValueSum'], "riskType" => $policyParameters['riskInsuranceType']);
        }

        if (isset ($policyParameters['PNR'])) {
            $policyParameters['PNR'] = $policyParameters['PNR'][0]['value'];
            if (isset ($policyParameters['stamp_ticket'])) {
                $stamp_ticket = explode(' ',$policyParameters['stamp_ticket']);
                $stamp_ticket = $stamp_ticket[0];
                $policyParameters['ticketInformation'] = array ('ticketTotalValue' => $policyParameters['total_fare'], 'ticketIssueDate' => $stamp_ticket);
            }
        }

        $operator = $policyParameters['operator']; unset ($policyParameters['operator']); // вытаскиваем отдельно оператора
        $product = $policyParameters['product']; unset ($policyParameters['product']); // вытаскиваем отдельно продукт

        $TES = self::TES();
        $Policy = $TES -> calculatePolicy($operator, $product, $policyParameters);

        if ($Policy->returnCode->code == 'OK') {

            $result['code'] = $Policy->returnCode->code;
            $result['policyId'] = $Policy->policyId;
            $result['premium'] = $Policy->calculationResult->premium;
            $result['currency'] = $Policy->calculationResult->currency;

            $this->responseSuccess($result);

        } else {
            $errorLog = array('Policy' => $Policy,
                              'Operator' => $operator,
                              'Product' => $product,
                              'PolicyParameters' => $policyParameters);

            dumpLog($errorLog,'calculatePolicy','insurance_error.log');

            $result['code'] = $Policy->returnCode->code;
            $result['errorMessage'] = $Policy->returnCode->errorMessage;
            $result['errorMessageID'] = $Policy->returnCode->errorMessageID;
            $this->responseSuccess($result);

        }
    }

    if ($args->type == 'createPolicy') { // Запрос на создание страховки

                $policyParameters = self::preparePolicyParameters($args->args);

                if (isset ($policyParameters['PNR'])) {
                    $policyParameters['PNR'] = $policyParameters['PNR'][0]['value'];
                  if (isset ($policyParameters['stamp_ticket'])) {
                      $stamp_ticket = explode(' ',$policyParameters['stamp_ticket']);
                      $stamp_ticket = $stamp_ticket[0];
                      $policyParameters['ticketInformation'] = array ('ticketTotalValue' => $policyParameters['total_fare'], 'ticketIssueDate' => $stamp_ticket);
                  }
                }

                $policyParameters['riskValue'] = array("value" => $policyParameters['riskValueSum'], "riskType" => $policyParameters['riskInsuranceType']);

                $operator = $policyParameters['operator']; unset ($policyParameters['operator']); // вытаскиваем отдельно оператора
                $product = $policyParameters['product']; unset ($policyParameters['product']); // вытаскиваем отдельно продукт

                 $TES = self::TES();
                    $Policy = $TES -> createPolicy($operator, $product, $policyParameters);


            if ($Policy->returnCode->code == 'OK') {

                $result['createPolicyCode'] = $Policy->returnCode->code;
                $result['createPolicyId'] = $Policy->policyId;
                $result['createPolicyPremium'] = $Policy->calculationResult->premium;
                $result['createPolicyCurrency'] = $Policy->calculationResult->currency;

                $rule = new RulesInsuranceBase(new WtFuncArgs(array(
                    'request_id'     => null,
                    'currency'       => 'RUB',
                    'provider_id'    => null,
                    'transaction_id' => null,
                    'order_id'       => null,
                    'summ'           => $result['createPolicyPremium']
                )));

                $res = $rule->execute(array());
                if ($res) {
                    $policyParameters['commission_agent']    = $res['agent_com'];
                    $policyParameters['commission_subagent'] = $res['subagent_com'];
                }

                self::createPolicyDb($operator, $product, $policyParameters,$result);
                WtDB::Ref()->OrderPassengersUpdate(new WtMapArgs('id', $policyParameters['pass_id'], 'insurance_id', $result['createPolicyId']));

                $this->responseSuccess($result);

            } else {

                $errorLog = array('PolicyParameters' => $policyParameters,
                                  'Operator' => $operator,
                                  'Product' => $product,
                                  'Policy' => $Policy);

                dumpLog($errorLog,'createPolicy','insurance_error.log');

                $result['code'] = $Policy->returnCode->code;
                $result['errorMessage'] = $Policy->returnCode->errorMessage;
                $result['errorMessageID'] = $Policy->returnCode->errorMessageID;
                $this->responseSuccess($result);

            }
    }

    if ($args->type == 'confirmPolicy') { // Запрос на подтверждение страховки

        $TES = self::TES();
        $confirmPolicy = $TES->confirmPolicy($args->operator, $args->policyId);

        if ($confirmPolicy->returnCode->code == 'OK') {

            $result['confirmPolicyCode']       = $confirmPolicy->returnCode->code;
            $result['confirmPolicySeries']     = $confirmPolicy->series;
            $result['confirmPolicyFullNumber'] = $confirmPolicy->fullNumber;
            $result['confirmPolicyDocument']   = $confirmPolicy->policyDocument->url;
            $result['status'] = 'C';

            self::confirmPolicyDb($args->policyId, $result, $args->order_id, $args->incurance_type, $args->pass);

            $order =  WtDB::Ref()->InsuranceOrdersRow(new WtMapArgs('policyId', $args->policyId));
            self::billing($order['id']);

            $this->responseSuccess($result);
        } else {

            $errorLog = array('PolicyId' => $args->policyId,
                              'Flight_order_id' => $args->flight_order_id,
                              'ConfirmPolicy' => $confirmPolicy);

            dumpLog($errorLog,'confirmPolicy','insurance_error.log');

            $result['code']           = $confirmPolicy->returnCode->code;
            $result['errorMessage']   = $confirmPolicy->returnCode->errorMessage;
            $result['errorMessageID'] = $confirmPolicy->returnCode->errorMessageID;
            $this->responseSuccess($result);
        }
    }

    if ($args->type == 'refundСreatePolicy') { // Запрос на отмену не подтвержденой страховки

       if ($args->policyId) {
                $refund =array();
                $refund['status'] = 'V';
                $refund['createPolicyId'] = $args->policyId;
                $refund['incurance_type'] = $args->incurance_type;
                self::refundPolicyDb($refund);
                $this->responseSuccess($refund);

            } else {
               $refund =array();
               $refund['status'] = 'Error';
               $this->responseSuccess($refund);
            }
        }

    if ($args->type == 'refundConfirmPolicy') { // Запрос на отмену подтвержденой страховки

        $declarationNumber = '000001';
        $declarationDate = date('Y-m-d');
        $TES = self::TES();
        $refundPolicy = $TES->refundPolicy($args->operator, $args->policyId, $declarationNumber, $declarationDate);

        if (isset ($refundPolicy->returnCode->errorMessage) and  $refundPolicy->returnCode->errorMessage == 'This method is not applicable from this product') {

            $errorLog = array('RefundPolicy' => $refundPolicy,
                              'Args' => $args);

            dumpLog($errorLog,'refundConfirmPolicy','insurance_error.log');

            return; }


            if ($refundPolicy->returnCode->code == 'OK') {
                $refund =array();
                $refund['status'] = 'R';
                $refund['insurance_type'] = $args->insurance_type;
                $refund['createPolicyId'] = $args->policyId;
                self::refundPolicyDb($refund);

                $order =  WtDB::Ref()->InsuranceOrdersRow(new WtMapArgs('policyId', $args->policyId));
                self::billing($order['id']);
                $this->responseSuccess($refund);

            } else {
                $refund =array();
                $refund['status'] = 'Error';

                $errorLog = array('Operator' => $args->operator,
                                  'PolicyId' => $args->policyId,
                                  'RefundPolicy' => $refundPolicy);

                dumpLog($errorLog,'RefundPolicy','insurance_error.log');

                $this->responseSuccess($refund);

            }
    }

    if ($args->type == 'voidInsurance') { // Запрос на отмену подтвержденой страховки

        $declarationNumber = '000001';
        $declarationDate   = date('Y-m-d');
        $operator          = $args->args['operator'];
        $orderId           = $args->args['orderId'];
        $passegers = WtDB::Ref()->OrderPassengersRows(new WtMapArgs('orderid', $orderId));

        $TES = self::TES();

        foreach ($passegers as $passeger) {
            $policyId = $passeger['insurance_id'];

            $refundPolicy = $TES->refundPolicy($operator, $policyId, $declarationNumber, $declarationDate);

            if (isset ($refundPolicy->returnCode->errorMessage) and  $refundPolicy->returnCode->errorMessage == 'This method is not applicable from this product') {

                $errorLog = array('RefundPolicy' => $refundPolicy,
                                  'OrderId' => $orderId);

                dumpLog($errorLog,'voidInsurance','insurance_error.log');

                continue; }

                $refund                         = array();
                $refund['status']               = 'R';
                $refund['insurance_type']       = 'flight';
                $refund['createPolicyId'] = $policyId;
                self::refundPolicyDb($refund);
                $order = WtDB::Ref()->InsuranceOrdersRow(new WtMapArgs('policyId', $policyId));
                self::billing($order['id']);

            if ($refundPolicy->returnCode->code != 'OK') {

                $errorLog = array('RefundPolicy' => $refundPolicy,
                                  'Order' => $order,
                                  'OrderId' =>  $orderId );

                dumpLog($errorLog,'voidInsurance','insurance_error.log');

            }
        }

        $this->responseSuccess('OK');
    }

}

    static function calculatePolicySearch ($product, $flightSegmentsCount) { // расчет стоимости страховки через поиск

        $params['flightSegmentsCount'] = $flightSegmentsCount;
        $TES = self::TES();
        $Policy = $TES -> calculatePolicy(WtSession::Ref()->UserConfig('insurance'), $product, $params);

        if ($Policy->returnCode->code == 'OK') {

        return($Policy->calculationResult->premium);

        } else {

            $result['code'] = $Policy->returnCode->code;
            $result['errorMessage'] = $Policy->returnCode->errorMessage;
            $result['errorMessageID'] = $Policy->returnCode->errorMessageID;

            $errorLog = array('Policy' => $Policy,
                              'Operator' => WtSession::Ref()->UserConfig('insurance'),
                              'Product' => $product,
                              'FlightSegmentsCount' => $params);

            dumpLog($errorLog,'calculatePolicy','insurance_error.log');

            return false;

        }
    }


    static function createInsuranceSearchFlight ($orderId, $operator, $product) {

        $order        = WtDB::Ref()->OrdersRow(new WtMapArgs('id', $orderId));
        $result_ids = explode('_',$order['result_ids']);
        $segmentsfromDB = WtDB::Ref()->TransactionsResultRows(new WtMapArgs('ids', $result_ids));
        $passengers = $order['passengers'];
        $segments = array();
        $i = 1;
        foreach ($segmentsfromDB as $one) {

            if ($one['first_departure_date'] != null) {
            $segments[$i]['departure_date'] = self::formatDateOther($one['first_departure_date']);
            $segments[$i]['departure_time'] = substr ($one['first_departure_time'], 0, 5);
            $segments[$i]['arrival_date'] = self::formatDateOther($one['first_arrival_date']);
            $segments[$i]['arrival_time'] = substr ($one['first_arrival_time'], 0, 5);
            $segments[$i]['flight_number'] = $one['first_flight_number'];
            $segments[$i]['departure_airport'] = $one['first_departure_airport'];
            $segments[$i]['arrival_airport'] = $one['first_arrival_airport'];
            $segments[$i]['arrivalCountry'] =  WtDB::Ref()->CountryValue(new WtMapArgs('airport', $segments[$i]['arrival_airport']));
            $segments[$i]['departureCountry'] =  WtDB::Ref()->CountryValue(new WtMapArgs('airport', $segments[$i]['departure_airport']));
            $segments[$i]['operating_airline'] = $one['first_operating_airline'];
            $i++;
            }

            if ($one['second_departure_date'] != null) {
                $segments[$i]['departure_date'] = self::formatDateOther($one['second_departure_date']);
                $segments[$i]['departure_time'] = substr ($one['second_departure_time'], 0, 5);
                $segments[$i]['arrival_date'] = self::formatDateOther($one['second_arrival_date']);
                $segments[$i]['arrival_time'] = substr ($one['second_arrival_time'], 0, 5);
                $segments[$i]['flight_number'] = $one['second_flight_number'];
                $segments[$i]['departure_airport'] = $one['second_departure_airport'];
                $segments[$i]['arrival_airport'] = $one['second_arrival_airport'];
                $segments[$i]['arrivalCountry'] =  WtDB::Ref()->CountryValue(new WtMapArgs('airport', $segments[$i]['arrival_airport']));
                $segments[$i]['departureCountry'] =  WtDB::Ref()->CountryValue(new WtMapArgs('airport', $segments[$i]['departure_airport']));
                $segments[$i]['operating_airline'] = $one['second_operating_airline'];
                $i++;
            }

            if ($one['third_departure_date'] != null) {
                $segments[$i]['departure_date'] = self::formatDateOther($one['third_departure_date']);
                $segments[$i]['departure_time'] = substr ($one['third_departure_time'], 0, 5);
                $segments[$i]['arrival_date'] = self::formatDateOther($one['third_arrival_date']);
                $segments[$i]['arrival_time'] = substr ($one['third_arrival_time'], 0, 5);
                $segments[$i]['flight_number'] = $one['third_flight_number'];
                $segments[$i]['departure_airport'] = $one['third_departure_airport'];
                $segments[$i]['arrival_airport'] = $one['third_arrival_airport'];
                $segments[$i]['arrivalCountry'] =  WtDB::Ref()->CountryValue(new WtMapArgs('airport', $segments[$i]['arrival_airport']));
                $segments[$i]['departureCountry'] =  WtDB::Ref()->CountryValue(new WtMapArgs('airport', $segments[$i]['departure_airport']));
                $segments[$i]['operating_airline'] = $one['third_operating_airline'];
                $i++;
            }
        }
        $flightCount = count($segments);
        $proporties = array();
        $insurer = array();
        $i = 0;
        foreach ($passengers as $passenger) {

            if (WtAuth::Ref()->usertype == 'A') {

                $proporties['agent_id'] = WtAuth::Ref()->base_uid;
            } elseif (WtAuth::Ref()->usertype == 'S') {
                $proporties['agent_id'] = WtAuth::Ref()->parent;
                $proporties['subagent_id'] = WtAuth::Ref()->base_uid;
            } elseif (WtAuth::Ref()->usertype == 'X') {
                $proporties['agent_id'] =  WtAuth::Ref()->parent;
                $proporties['corporate_id'] = WtAuth::Ref()->base_uid;
            }

            if (!empty(WtAuth::Ref()->owner)) {
                $proporties['member_id'] =  WtAuth::Ref()->id;
            }

            $proporties['order_id'] = $orderId;
            $proporties['provider_id'] = $order['provider_id'];
            $proporties['pass_id'] = $passenger['id'];

            $proporties['product'] = $product;
            $proporties['operator'] = $operator;
            $proporties['subagentCode'] = WtAuth::Ref()->base_uid;
            $proporties['insuredFirstName'] = $insurer[$i]['insurerFirstName'] = $passenger['firstname'];
            $proporties['insuredLastName'] = $insurer[$i]['insurerLastName'] = $passenger['lastname'];
            $proporties['insuredPatronymic'] = '';
            $proporties['insuredBirthDate'] = $insurer[$i]['insurerBirthDate'] = $passenger['birth_date'];
            $proporties['insuredDocumentType'] = $insurer[$i]['insurerDocumentType'] = self::formatdoc_type($passenger['doc_type']);
            $proporties['insuredDocumentNumber'] = $insurer[$i]['insurerDocumentNumber'] = $passenger['doc_number'];
            $proporties['insuredEmail'] = $insurer[$i]['insurerEmail'] = $order['buyer_email'];
            $proporties['insuredPhone'] = $insurer[$i]['insurerPhone'] = $order['buyer_phonenumber'];
            $proporties['insuredPhoneType'] = 'MOBILE';
            if ($passenger['title'] == 'MS') {$proporties['insuredSex'] = $insurer[$i]['insurerSex'] = 'FEMALE';} else {$proporties['insuredSex'] = $insurer[$i]['insurerSex'] = 'MALE';}
            $proporties['insuredAddress'] = $insurer[$i]['insurerAddress'] = $order['buyer_address'];

            $proporties['insurerFirstName'] = $insurer[0]['insurerFirstName'];
            $proporties['insurerLastName'] = $insurer[0]['insurerLastName'];
            $proporties['insurerPatronymic'] = '';
            $proporties['insurerBirthDate'] = $insurer[0]['insurerBirthDate'];
            $proporties['insurerDocumentType'] = $insurer[0]['insurerDocumentType'];
            $proporties['insurerDocumentNumber'] = $insurer[0]['insurerDocumentNumber'];
            $proporties['insurerEmail'] = $insurer[0]['insurerEmail'];
            $proporties['insurerPhone'] = $insurer[0]['insurerPhone'];
            $proporties['insurerPhoneType'] = 'MOBILE';
            $proporties['insurerSex'] = $insurer[0]['insuredSex'];
            $proporties['insurerAddress'] = $insurer[0]['insurerAddress'];

            $proporties['paymentType'] = 'deposit';
            $proporties['PNR'] = $order['pnr_number'];
            $proporties['flightSegmentsCount'] = $flightCount;

            $k = 0;
            foreach ($segments as $segment) {

                $proporties['flightSegmentTransportOperatorCode'][$k] = array ('seqNo' => $k, 'value' => $segment['operating_airline']);
                $proporties['flightSegmentFlightNumber'][$k] = array ('seqNo' => $k, 'value' => $segment['flight_number']);
                $proporties['flightSegmentDepartureDate'][$k] = array ('seqNo' => $k, 'value' => self::formatDate($segment['departure_date']));
                $proporties['flightSegmentDepartureTime'][$k] = array ('seqNo' => $k, 'value' => $segment['departure_time'].':00');
                $proporties['flightSegmentDepartureAirport'][$k] = array ('seqNo' => $k, 'value' => $segment['departure_airport']);
                $proporties['flightSegmentDepartureCountry'][$k] = array ('seqNo' => $k, 'value' => $segment['arrivalCountry']);
                $proporties['flightSegmentArrivalDate'][$k] = array ('seqNo' => $k, 'value' => self::formatDate($segment['arrival_date']));
                $proporties['flightSegmentArrivalTime'][$k] = array ('seqNo' => $k, 'value' => $segment['arrival_time'].':00');
                $proporties['flightSegmentArrivalAirport'][$k] = array ('seqNo' => $k, 'value' => $segment['arrival_airport']);
                $proporties['flightSegmentArrivalCountry'][$k] = array ('seqNo' => $k, 'value' => $segment['arrivalCountry']);
                $k++;
            }

            $i++;

            $TES = self::TES();
            $Policy = $TES -> createPolicy($operator, $product, $proporties);

            if ($Policy->returnCode->code == 'OK') {

                $createdPolicy['createPolicyCode']     = $Policy->returnCode->code;
                $createdPolicy['createPolicyId'] = $Policy->policyId;
                $createdPolicy['createPolicyPremium']  = $Policy->calculationResult->premium;
                $createdPolicy['createPolicyCurrency'] = $Policy->calculationResult->currency;

                $rule = new RulesInsuranceBase(new WtFuncArgs(array(
                    'request_id'     => null,
                    'currency'       => 'RUB',
                    'provider_id'    => null,
                    'transaction_id' => null,
                    'order_id'       => null,
                    'summ'           => $createdPolicy['createPolicyPremium']
                )));
                $res = $rule->execute(array());
                if ($res) {
                    $proporties['commission_agent']    = $res['agent_com'];
                    $proporties['commission_subagent'] = $res['subagent_com'];
                }
                self::createPolicyDb($operator, $product, $proporties, $createdPolicy);
                WtDB::Ref()->OrderPassengersUpdate(new WtMapArgs('id', $passenger['id'], 'insurance_id', $createdPolicy['createPolicyId']));

            } else {

                $errorLog = array('Policy' => $Policy,
                                  'Operator' => $operator,
                                  'Product' => $product,
                                  'Proporties' => $proporties);

                dumpLog($errorLog,'createPolicySearch','insurance_error.log');

            }

        }

         return;
    }

    static function voidInsuranceFlight($orderId) {
        $passegers = WtDB::Ref()->OrderPassengersRows(new WtMapArgs('orderid', $orderId));
        $declarationNumber = '000001';
        $declarationDate   = date('Y-m-d');

        $TES = self::TES();

        foreach ($passegers as $passeger) {
            $policyId = $passeger['insurance_id'];
            $order = WtDB::Ref()->InsuranceOrdersRow(new WtMapArgs('policyId', $policyId));
            $operator = $order['operator'];

            if (!$order) {
                continue;
            } else {

                if ($order['status'] == 'R' or $order['status'] == 'V') {
                    continue;
                }
            }

            if ($order['status'] != 'O') {
                $refundPolicy = $TES->refundPolicy($operator, $policyId, $declarationNumber, $declarationDate);
            }

            if (isset ($refundPolicy->returnCode->errorMessage) and  $refundPolicy->returnCode->errorMessage == 'This method is not applicable from this product') {

                $errorLog = array('RefundPolicy' => $refundPolicy,
                                  'OrderId' =>  $orderId );

                dumpLog($errorLog,'voidInsuranceFlight','insurance_error.log');

                continue; }

            $refund                         = array();
            $refund['status']               = 'R';
            $refund['insurance_type']       = 'flight';
            $refund['createPolicyId'] = $policyId;
            self::refundPolicyDb($refund);
            self::billing($order['id']);

            if ($refundPolicy->returnCode->code != 'OK' and $order['status'] != 'O') {

                $errorLog = array('RefundPolicy' => $refundPolicy,
                                  'Order' => $order,
                                  'OrderId' =>  $orderId );

                dumpLog($errorLog,'voidInsuranceFlight','insurance_error.log');

            }

          }
        return;

    }

    static function voidInsuranceFlightSingle($ticketid,$orderId) {

        $order = WtDB::Ref()->InsuranceOrdersRow(new WtMapArgs('orderId', $orderId));

        if (!$order) {
            return;
        } else {

            if ($order['status'] == 'R' or $order['status'] == 'V') {
                return;
            }
        }

        $ticket = WtDB::Ref()->OrderTicketsRow(new WtMapArgs('id', $ticketid));
        $passengerId = $ticket['passenger_id'];
        $passenger = WtDB::Ref()->OrderPassengersRow(new WtMapArgs('id', $passengerId));

        $declarationNumber = '000001';
        $declarationDate   = date('Y-m-d');
        $operator = $order['operator'];

        $TES = self::TES();

            $policyId = $passenger['insurance_id'];

            if ($order['status'] != 'O') {
                $refundPolicy = $TES->refundPolicy($operator, $policyId, $declarationNumber, $declarationDate);
            }

            if (isset ($refundPolicy->returnCode->errorMessage) and  $refundPolicy->returnCode->errorMessage == 'This method is not applicable from this product') {

                $errorLog = array('RefundPolicy' => $refundPolicy,
                                  'OrderId' =>  $orderId );

                dumpLog($errorLog,'voidInsuranceFlight','insurance_error.log');

                return; }

            $refund                         = array();
            $refund['status']               = 'R';
            $refund['insurance_type']       = 'flight';
            $refund['createPolicyId'] = $policyId;
            self::refundPolicyDb($refund);
            self::billing($order['id']);

            if ($refundPolicy->returnCode->code != 'OK' and $order['status'] != 'O') {

                $errorLog = array('RefundPolicy' => $refundPolicy,
                                  'Order' => $order,
                                  'OrderId' =>  $orderId );

                dumpLog($errorLog,'voidInsuranceFlightSingle','insurance_error.log');

            }

        return;

    }

    static function confirmInsuranceFlight($orderId) {

        $passegers = WtDB::Ref()->OrderPassengersRows(new WtMapArgs('orderid', $orderId));

        $TES = self::TES();

        foreach ($passegers as $passeger) {
            $policyId = $passeger['insurance_id'];
            $order = WtDB::Ref()->InsuranceOrdersRow(new WtMapArgs('policyId', $policyId));
            $operator = $order['operator'];

            if (!$order) {
                continue;
            } else {

                if ($order['status'] != 'O') {
                    continue;
                }
            }

            $confirmPolicy = $TES->confirmPolicy($operator, $policyId);

            if ($confirmPolicy->returnCode->code == 'OK') {

                $confirmedPolicy['confirmPolicyCode']       = $confirmPolicy->returnCode->code;
                $confirmedPolicy['confirmPolicySeries']     = $confirmPolicy->series;
                $confirmedPolicy['confirmPolicyFullNumber'] = $confirmPolicy->fullNumber;
                $confirmedPolicy['confirmPolicyDocument']   = $confirmPolicy->policyDocument->url;
                $confirmedPolicy['status']                  = 'C';

                self::confirmPolicyDb($policyId, $confirmedPolicy, $order['id'], 'flight', $passeger['id']);
                self::billing($order['id']);
            } else {

                $errorLog = array('Operator'           => $operator,
                                  'CreatePolicyId'     => $policyId,
                                  'ConfirmPolicy'      => $confirmPolicy,
                                  'Order_insurance_id' => $policyId,
                                  'Passenger'          => $passeger['id']);

                dumpLog($errorLog, 'confirmInsuranceFlight', 'insurance_error.log');
            }
        }

       return;

    }

    static function getPdfInsuranceFlight ($orderId,$passId = null) {
        $insurancePdfLinks = array();
        $passegers = array();
        if ($passId) {
            $passegers[0] = WtDB::Ref()->OrderPassengersRow(new WtMapArgs('id', $passId));
        } else {
            $passegers = WtDB::Ref()->OrderPassengersRows(new WtMapArgs('orderid', $orderId));
        }
        foreach ($passegers as $passeger) {
            $policyId = $passeger['insurance_id'];
            $order = WtDB::Ref()->InsuranceOrdersRow(new WtMapArgs('policyId', $policyId));

            if (!$order) {
                continue;
            } else {

                if ($order['status'] != 'C') {
                    continue;
                }
            }
            $insurancePdfLinks[] = $order['policy_document'];
        }

        return $insurancePdfLinks;
    }


     function preparePolicyParameters($args) { //Подготовка массива policyParameters

            $argsBuffer = explode('&',$args);
            $args = array();

        foreach ($argsBuffer as $value) {

            $arg = explode('=',$value);

            $seqNo = (int) $arg[0][0];

            if ($arg[1] != '') {

                $arg[1] = htmlspecialchars(urldecode($arg[1]));

                if ($seqNo > 0) {

                    $seqNo  = $seqNo - 1;
                    $arg[0] = substr($arg[0], 1);

                    if ($arg[0] == 'railwaySegmentDepartureDate') {
                        $arg[1] = self::formatDate($arg[1]);
                    }
                    if ($arg[0] == 'railwaySegmentArrivalDate') {
                        $arg[1] = self::formatDate($arg[1]);
                    }
                    if ($arg[0] == 'railwaySegmentDepartureTime') {
                        $arg[1] .= ':00';
                    }
                    if ($arg[0] == 'railwaySegmentArrivalTime') {
                        $arg[1] .= ':00';
                    }
                    if ($arg[0] == 'flightSegmentDepartureDate') {
                        $arg[1] = self::formatDate($arg[1]);
                    }
                    if ($arg[0] == 'flightSegmentArrivalDate') {
                        $arg[1] = self::formatDate($arg[1]);
                    }
                    if ($arg[0] == 'flightSegmentDepartureTime') {
                        $arg[1] .= ':00';
                    }
                    if ($arg[0] == 'flightSegmentArrivalTime') {
                        $arg[1] .= ':00';
                    }
                    if ($arg[0] == 'insuredBirthDate') {
                        $arg[1] = self::formatDate($arg[1]);
                    }

                    if ($arg[0][0] == 'i') { // если insured

                        $args[$arg[0]] = $arg[1];
                    } else { // если сегменты перелета или жд

                        $args[$arg[0]][] = array('seqNo' => $seqNo, 'value' => $arg[1]);
                    }
                } else {

                    if ($arg[0] == 'insurerBirthDate') {
                        $arg[1] = self::formatDate($arg[1]);
                    }
                    if ($arg[0] == 'beginDate') {
                        $arg[1] = self::formatDate($arg[1]);
                    }
                    if ($arg[0] == 'endDate') {
                        $arg[1] = self::formatDate($arg[1]);
                    }
                    $args[$arg[0]] = $arg[1];
                    $args['subagentCode'] = WtAuth::Ref()->base_uid;
                }
            }
        }

        if (WtAuth::Ref()->usertype == 'A') {

            $args['agent_id'] = WtAuth::Ref()->base_uid;
        } elseif (WtAuth::Ref()->usertype == 'S') {
            $args['agent_id'] = WtAuth::Ref()->parent;
            $args['subagent_id'] = WtAuth::Ref()->base_uid;
        } elseif (WtAuth::Ref()->usertype == 'X') {
            $args['agent_id'] =  WtAuth::Ref()->parent;
            $args['corporate_id'] = WtAuth::Ref()->base_uid;
        }

        if (!empty(WtAuth::Ref()->owner)) {
            $args['member_id'] =  WtAuth::Ref()->id;
        }

        return ($args);
    }

    function formatDate ($date) { // Приведение даты с вида d.m.Y в Y-m-d
        if ($date  != '') {
            $dateBuffer = explode('.', $date);
            $date       = $dateBuffer[2] . '-' . $dateBuffer[1] . '-' . $dateBuffer[0];
        }
       return $date;
    }

    function formatDateOther ($date) { // Приведение даты с вида d-m-Y в Y.m.d
        if ($date  != '') {
            $dateBuffer = explode('-', $date);
            $date       = $dateBuffer[2] . '.' . $dateBuffer[1] . '.' . $dateBuffer[0];
        }
        return $date;
    }

    function formatDateMounth ($date) { // Приведение даты с вида d-m-Y в Y.m.d
        if ($date  == 'Января') {$date = '01'; }
        if ($date  == 'Февраля') {$date = '02'; }
        if ($date  == 'Матра') {$date = '03'; }
        if ($date  == 'Апреля') {$date = '04'; }
        if ($date  == 'Мая') {$date = '05'; }
        if ($date  == 'Июня') {$date = '06'; }
        if ($date  == 'Июля') {$date = '07'; }
        if ($date  == 'Августа') {$date = '08'; }
        if ($date  == 'Сентября') {$date = '09'; }
        if ($date  == 'Октября') {$date = '10'; }
        if ($date  == 'Ноября') {$date = '11'; }
        if ($date  == 'Декабря') {$date = '12'; }
        return $date;
    }

    function createPolicyDb ($operator, $product, $policyParameters, $createPolicyResult) { // Добавление в базу данных

        $user = WtAuth::Ref();
        $additional_info = array();
        $additional_info['type'] = 'railway';
        $insured = array();
        $insurer = array();
        $additional = array();
        $railway = array();
        $flight = array();
        $forDBRailway = array();
        $forDBFlight = array();
        $policyParameters['paymentType'] = 'deposit';

        foreach ($policyParameters as $key => $value) { // парсим в нужные массивы параметры

            if (strripos($key, 'insured') !== false) {
                $insured[$key] = $value;
            }

            else if (strripos($key, 'insurer') !== false) {
                $insurer[$key] = $value;
            }

            else if (strripos($key, 'railway') !== false) {
                $railway[$key] = $value;
            }

            else  if (strripos($key, 'flight') !== false) {
                $flight[$key] = $value;
            }

            else if (strripos($key, 'PNR') !== false) {
                $pnr = $value;
                $flight[$key] = $value;
                $additional_info['type'] = 'flight';
            }

            else {
                $additional[$key] = $value;
            }

        }

        $additional_info['user_id'] = $user->id;
        if (isset ($policyParameters['orderId'])) {  $additional_info['order_id'] = $policyParameters['orderId']; }
        if (isset ($policyParameters['pass_id'])) {  $additional_info['pass_id'] = $policyParameters['pass_id']; }
        $additional_info['operator'] = $operator;
        $additional_info['product'] = $product;
        if (isset ($policyParameters['provider_id'])) {$additional_info['provider_id'] = $policyParameters['provider_id'];}

        if (isset ($policyParameters['agent_id'])) {$additional_info['agent_id'] = $policyParameters['agent_id'];}
        $additional_info['user_id'] = WtAuth::Ref()->base_uid;
        if (isset ($policyParameters['subagent_id'])) {$additional_info['subagent_id'] = $policyParameters['subagent_id'];}
        if (isset ($policyParameters['corporate_id'])) {$additional_info['corporate_id'] = $policyParameters['corporate_id'];}
        if (isset ($policyParameters['member_id'])) {$additional_info['member_id'] = $policyParameters['member_id'];}


        $additional_info['createPolicyId'] = $createPolicyResult['createPolicyId'];
        $additional_info['createPolicyPremium'] = $createPolicyResult['createPolicyPremium'];
        $additional_info['createPolicyCurrency'] = $createPolicyResult['createPolicyCurrency'];
        $additional_info['serialize'] = serialize($policyParameters);
        $additional_info['TicketNumber'] = $insured['insuredTicketNumber'];

        $insurance_order = array_merge($additional_info,$additional);

        $order_id = WtDB::Ref()->InsuranceOrdersInsert(new WtFuncArgs($insurance_order));

        $insured['order_id'] = $order_id;
        $insurer['order_id'] = $order_id;

        WtDB::Ref()->InsuranceInsuredInsert(new WtFuncArgs($insured));
        WtDB::Ref()->InsuranceInsurerInsert(new WtFuncArgs($insurer));

        if (in_array($operator,array('TestRailwayOperator'))) {

            for ($i = 0;$i < $railway['railwaySegmentsCount'];$i++) {
                foreach ($railway as $key=>$value) {
                    if (is_array($value)) {
                        $forDBRailway[$i][$key] = $value[$i]['value'];
                    } else {
                        $forDBRailway[$i][$key] = $value;
                        $forDBRailway[$i]['seqNo'] = $i;
                        $forDBRailway[$i]['order_id'] = $order_id;
                    }
                } }

            foreach ($forDBRailway as $addSegment) {

                   WtDB::Ref()->InsuranceRailInsert(new WtFuncArgs($addSegment));

            }

        } else {

            for ($i = 0;$i < $flight['flightSegmentsCount'];$i++) {
                foreach ($flight as $key=>$value) {
                    if (is_array($value)) {
                        $forDBFlight[$i][$key] = $value[$i]['value'];
                    } else {
                        $forDBFlight[$i][$key] = $value;
                        $forDBFlight[$i]['seqNo'] = $i;
                        $forDBFlight[$i]['order_id'] = $order_id;
                    }
                } }

            foreach ($forDBFlight as $addSegment) {

                $addSegment['pnr'] = $pnr;

                WtDB::Ref()->InsuranceFlightInsert(new WtFuncArgs($addSegment));

            }

        }

        return $order_id;

    }



    function confirmPolicyDb ($createPolicyId, $result, $order_id, $incurance_type, $pass_id) {

        $updatePolicy = $result;
        $updatePolicy['createPolicyId'] = $createPolicyId;
        WtDB::Ref()->InsuranceOrdersUpdate(new WtFuncArgs($updatePolicy));

        if ($incurance_type == 'flight' or $incurance_type == 'FLIGHT') {
            WtDB::Ref()->OrderPassengersUpdate(new WtMapArgs('id', $pass_id, 'insurance_id', $createPolicyId));

        }
        if ($incurance_type == 'railway' or $incurance_type == 'RAILWAQY' ) {
            WtDB::Ref()->RailwayPassengersUpdate(new WtMapArgs('order_id', $order_id, 'insurance_id', $createPolicyId));
        }

    }

    function refundPolicyDb ($refund) {

        WtDB::Ref()->InsuranceOrdersUpdate(new WtFuncArgs($refund));


        if ($refund['insurance_type'] == 'FLIGHT' or $refund['insurance_type'] == 'flight') {

      }
        if ($refund['insurance_type'] == 'RAILWAY' or $refund['insurance_type'] == 'railway') {

            WtDB::Ref()->RailwayPassengersUpdate(new WtMapArgs('where_insurance_id', $refund['createPolicyId'], 'insurance_id', null));
        }

    }


    function downloadInsurance ($policyId,$link) { //Скачивание и сохранение страховки

        $pdf = file_get_contents($link);
        $insurance = WtConfig::Get('ROOT_PATH') . '/var/cache/'.$policyId.'.pdf';
        $f = @fopen($insurance, 'w+');
        fwrite($f,$pdf);
        fclose($f);

        $insurance = $_SERVER['SERVER_NAME'].'/etm-system/var/data/'.$policyId.'.pdf';

        return ($insurance);

    }


    function formatdoc_type ($doc_type) {

        if ($doc_type == 'PS') {$doc_type = 'PASSPORT';}
        else if ($doc_type == 'PSP') {$doc_type = 'INTERNATIONAL';}
        else if ($doc_type == 'VB') {$doc_type = 'MILITARY';}
        else if ($doc_type == 'NP') {$doc_type = 'IDCARD';}
        else if ($doc_type == 'SR') {$doc_type = 'BIRTHCERTIFICATE';}
        else {$doc_type = 'FOREIGNER';}

        return $doc_type;
    }



    function billing ($order_id) {

        $order =  WtDB::Ref()->InsuranceOrdersRow(new WtMapArgs('id', $order_id));

        $rates = WtDB::Ref()->CurrencyRatesMap(new WtMapArgs('from', $order['policy_currency'],'pid',$order['provider_id']));

        foreach($rates as $key=>$rate){
            $insurance_rates['base'][$key] = round ($order['policy_premium'] * $rate, 2);
            $insurance_rates['commission_agent'][$key] = round ($order['commission_agent'] * $rate, 2);
            $insurance_rates['commission_subagent'][$key] = round ($order['commission_subagent'] * $rate, 2);
        }
        $insurance_rates['base'][$order['policy_currency']] = $order['policy_premium'];

        $insurance_rates['commission_agent']['RUB'] = $order['commission_agent'];
        $insurance_rates['commission_subagent']['RUB'] = $order['commission_subagent'];

        $billing = new InsuranceBilling($order['id'],$insurance_rates);
        $billing->Calc($order['id']);
        $billing->Pay($order['id']);

    }




}




