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

/**
 * Script Name, purpose
 *
 * @category   ETM System
 * @package    Providers
 * @subpackage Railway
 *
 * @author     Gusev Konstantin <gusev@etm-system.ru>
 * @author     Trotsenko Sergey <trotsenko@etm-system.ru>
 * @copyright  Copyright (c) 2001-2014 UIMG LTD <webmaster@uimg.com.ua>
 * @version    2.28
 */

class HahnAir {

    private $Type;
    private $curl;
    private $link;
    private $sslCert;
    private $sslPass;
    private $method;

    function __construct() {
        $this->sslCert = '1ed2e035-7338-4042-bc50-ffca4106c4f3';
        $this->sslPass = 'L0w$ecuritY';
        $this->method  = 'POST';
    }

    public function GoQuery($type = 'online', $args = null) {
        try {
            $this->Type   = $type;
            if ($this->Type == 'check') {
                $this->link    = 'https://qc-api-dev.hahnair.com/QcService.svc/json/Check';
            } elseif ($this->Type == 'online') {
                $this->link    = 'https://qc-api-dev.hahnair.com/QcService.svc/json/OnlineInfo';
            } else {
                return array('status' => 'error', 'msg' => 'Error type');
            }

            $responses = array();

            $this->curl = curl_init();
            curl_setopt($this->curl, CURLOPT_URL, $this->link);
            curl_setopt($this->curl, CURLOPT_SSLCERT, $this->sslCert);
            curl_setopt($this->curl, CURLOPT_SSLCERTPASSWD, $this->sslPass);
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

            if ($this->Type == 'check') {
                if (isset($args['transactions_ids'])) {

                    $segmentsfromDB = WtDB::Ref()->TransactionsResultRows(new WtMapArgs('ids', $args['transactions_ids']));

                } elseif (isset($args['orderid'])) {

                    $segmentsfromDB = WtDB::Ref()->TransactionsResultRows(new WtMapArgs('orderid', $args['orderid']));

                } else {
                    return array('status' => 'error', 'msg' => 'No args');
                }

                if (!$segmentsfromDB) {
                    return array('status' => 'error', 'msg' => 'No segmentsfromDB');
                }

                $segments = array();
                $paxs = array();
                $queryes = array();

                foreach ($segmentsfromDB as $one) {
                    if ($one['gds'] == 'AMADEUS') {
                        $gds = '1A';
                    } else {
                        return array('status' => 'error', 'msg' => 'No allowed GDS');
                    }

                    $provider = WtDB::Ref()->ProvidersRow(new WtMapArgs('id', $one['provider_id']));
                    $country = $provider['country'];

                    if (!$country) {
                        return array('status' => 'error', 'msg' => 'No Country');
                    }

                    if ($one['first_departure_date'] != null) {
                        $segments[] = array('AirM' => $one['first_marketing_airline'],
                                            'AirO' => $one['first_operating_airline'],
                                            'FaB'  => $one['first_fare_basis'],
                                            'FNo'  => $one['first_flight_number'],
                                            'BCl'  => $one['first_booking_class'],
                                            'BPo'  => $one['first_departure_airport'],
                                            'OPo'  => $one['first_arrival_airport']
                        );
                    }
                    if ($one['second_departure_date'] != null) {
                        $segments[] = array('AirM' => $one['second_marketing_airline'],
                                            'AirO' => $one['second_operating_airline'],
                                            'FaB'  => $one['second_fare_basis'],
                                            'FNo'  => $one['second_flight_number'],
                                            'BCl'  => $one['second_booking_class'],
                                            'BPo'  => $one['second_departure_airport'],
                                            'OPo'  => $one['second_arrival_airport']
                        );
                    }
                    if ($one['third_departure_date'] != null) {
                        $segments[] = array('AirM' => $one['third_marketing_airline'],
                                            'AirO' => $one['third_operating_airline'],
                                            'FaB'  => $one['third_fare_basis'],
                                            'FNo'  => $one['third_flight_number'],
                                            'BCl'  => $one['third_booking_class'],
                                            'BPo'  => $one['third_departure_airport'],
                                            'OPo'  => $one['third_arrival_airport']
                        );
                    }

                    if ($one['adult_qty'] != 0) {
                        $paxs[] = 'ADT';
                    }
                    if ($one['child_qty'] != 0) {
                        $paxs[] = 'CHD';
                    }
                    if ($one['infant_qty'] != 0) {
                        $paxs[] = 'INF';
                    }
                }


                foreach ($paxs as $pax) {
                    $queryes[] = json_encode(array("callingParameters" => array("Cou" => $country,
                                                                               "Gds" => $gds,
                                                                               "Pax" => $pax,
                                                                            "Segmts" => $segments)));
                    }

                foreach ($queryes as $key => $one) {
                    curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $this->method);
                    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $one);
                    curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($one))
                    );
                    $result = curl_exec($this->curl);
                    $responses[] = $result;
                }
                curl_close($this->curl);

            } else {
                $responses[] = curl_exec($this->curl);
                curl_close($this->curl);
            }

            $status = 'error';
            $msg    = 'Something wrong';
            $error  = false;

            foreach ($responses as $response) {

                if ($this->isJSON($response)) {

                    $hhResponse = json_decode($response);
                    if (!isset($hhResponse->OnlineInfoResult) && (!isset($hhResponse->CheckResult->Ok) || $hhResponse->CheckResult->Ok != true)) {
                        $error = true;
                    }

                    $msg = json_decode($response);

                } else {
                    $error = true;
                    $msg = 'Error decode result';
                }
            }

            if (!$error) {
                $status = 'ok';
            }

            return array('status' => $status /*, 'msg' => $msg*/);

        } catch (Exception $e) {
            return array('status' => 'error', 'msg' => 'Error exception '.$e->getMessage());
        }
    }

    public function MultiQuery($args = null) {
        try {
            $start      = microtime(true);
            $transactionsVariants = array();
            $check = false;
            $i = 0;
            $gds = '1A';

            foreach ($args as $arg) {
                if (WtSession::Ref()->UserConfig('avail_hh', $arg['provider_id']) == 'Y') { //Проверка в hh
                    $transactionsVariants[$i]['variant_id']  = $arg['id'];
                    if ($arg['segment1_id'] != null) $transactionsVariants[$i]['ids'][] = $arg['segment1_id'];
                    if ($arg['segment2_id'] != null) $transactionsVariants[$i]['ids'][] = $arg['segment2_id'];
                    if ($arg['segment3_id'] != null) $transactionsVariants[$i]['ids'][] = $arg['segment3_id'];
                    if ($arg['segment4_id'] != null) $transactionsVariants[$i]['ids'][] = $arg['segment4_id'];
                    if ($arg['segment5_id'] != null) $transactionsVariants[$i]['ids'][] = $arg['segment5_id'];
                    if ($arg['segment6_id'] != null) $transactionsVariants[$i]['ids'][] = $arg['segment6_id'];
                    $i++;
                }
            }

            if (!empty($transactionsVariants)) {
                $this->link = 'https://qc-api-dev.hahnair.com/QcService.svc/json/OnlineInfo';
                $this->curl = curl_init();
                curl_setopt($this->curl, CURLOPT_URL, $this->link);
                curl_setopt($this->curl, CURLOPT_SSLCERT, $this->sslCert);
                curl_setopt($this->curl, CURLOPT_SSLCERTPASSWD, $this->sslPass);
                curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($this->curl);
                if ($this->isJSON($response)) {
                    $hhResponse = json_decode($response);
                    if (isset($hhResponse->OnlineInfoResult)) {
                        $check = true;
                    } else {
                        dumpLog ($hhResponse, 'hhResponseMulti', 'hahnair.log');
                    }
                } else {
                    dumpLog ($response, 'responseMulti', 'hahnair.log');
                }
                curl_close($this->curl);
            }

            if ($check) {
                $queryes = array();
                $transactions = array();

                foreach ($transactionsVariants as $transactionsVariant) {
                    $transactions[$transactionsVariant['variant_id']] = WtDB::Ref()->TransactionsResultRows(new WtMapArgs('ids', $transactionsVariant['ids']));
                }

                foreach ($transactions as $key => $value) {
                    $provider = WtDB::Ref()->ProvidersRow(new WtMapArgs('id', $value[0]['provider_id']));
                    $country  = $provider['country'];
                    $segments = array();
                    $paxs     = array();

                    foreach ($value as $transaction) {
                        if ($transaction['first_departure_date'] != null) {
                            $segments[] = array('AirM' => $transaction['first_marketing_airline'],
                                                'AirO' => $transaction['first_operating_airline'],
                                                'FaB'  => $transaction['first_fare_basis'],
                                                'FNo'  => $transaction['first_flight_number'],
                                                'BCl'  => $transaction['first_booking_class'],
                                                'BPo'  => $transaction['first_departure_airport'],
                                                'OPo'  => $transaction['first_arrival_airport']
                            );
                        }
                        if ($transaction['second_departure_date'] != null) {
                            $segments[] = array('AirM' => $transaction['second_marketing_airline'],
                                                'AirO' => $transaction['second_operating_airline'],
                                                'FaB'  => $transaction['second_fare_basis'],
                                                'FNo'  => $transaction['second_flight_number'],
                                                'BCl'  => $transaction['second_booking_class'],
                                                'BPo'  => $transaction['second_departure_airport'],
                                                'OPo'  => $transaction['second_arrival_airport']
                            );
                        }
                        if ($transaction['third_departure_date'] != null) {
                            $segments[] = array('AirM' => $transaction['third_marketing_airline'],
                                                'AirO' => $transaction['third_operating_airline'],
                                                'FaB'  => $transaction['third_fare_basis'],
                                                'FNo'  => $transaction['third_flight_number'],
                                                'BCl'  => $transaction['third_booking_class'],
                                                'BPo'  => $transaction['third_departure_airport'],
                                                'OPo'  => $transaction['third_arrival_airport']
                            );
                        }

                        if ($transaction['adult_qty'] != 0) {
                            $paxs[] = 'ADT';
                        }
                        if ($transaction['child_qty'] != 0) {
                            $paxs[] = 'CHD';
                        }
                        if ($transaction['infant_qty'] != 0) {
                            $paxs[] = 'INF';
                        }
                    }

                    $paxs = array_unique($paxs);

                    foreach ($paxs as $pax) {
                        $newKey = $key.'|'.$pax;
                        $queryes[$newKey]  = json_encode(array("callingParameters" => array("Cou"    => $country,
                                                                                            "Gds"    => $gds,
                                                                                            "Pax"    => $pax,
                                                                                            "Segmts" => $segments)));
                    }
                }

                $this->link    = 'https://qc-api-dev.hahnair.com/QcService.svc/json/Check';
                $this->curl = curl_init();
                curl_setopt($this->curl, CURLOPT_URL, $this->link);
                curl_setopt($this->curl, CURLOPT_SSLCERT, $this->sslCert);
                curl_setopt($this->curl, CURLOPT_SSLCERTPASSWD, $this->sslPass);
                curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $this->method);
                $responses =array();

                foreach ($queryes as $key => $one) {
                    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $one);
                    curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($one))
                    );
                    $result = curl_exec($this->curl);
                    $responses[$key] = $result;
                }
                curl_close($this->curl);

                $forDelete = array();
                if (!empty($responses)) {
                    foreach ($responses as $key => $value) {
                        if ($this->isJSON($value)) {
                            $hhResponse = json_decode($value);
                            if (isset($hhResponse->CheckResult->Ok) && $hhResponse->CheckResult->Ok == true) {
                            } else {
                                $forDelete[] = preg_replace('/[^0-9]/', '', $key);
                            }
                        } else {
                            $forDelete[] = preg_replace('/[^0-9]/', '', $key);
                        }
                    }
                }

                if (!empty($forDelete)) {
                    WtDB::Ref()->TransactionsVariantDelete(new WtMapArgs('ids', array_unique($forDelete)));
                }

            }

            $time = round(microtime(true) - $start, 5);
            //dumpLog ($time, 'timeMulti', 'hahnair.log');
            return true;

         } catch (Exception $e) {
                dumpLog ($e, 'responseMulti e', 'hahnair.log');
                return false;
         }
    }

    private function isJSON($string) {
        return ((is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))))) ? true : false;
    }

}


