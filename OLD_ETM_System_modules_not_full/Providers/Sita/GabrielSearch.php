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


class WtGabrielSearch {

    /**
     * XML SOAP client
     * @var WtGabrielDriver
     */
    private $client;

    private $provider;
    private $Currency;
    private $TimeZone;
    private $GDS = 'GABRIEL';
    private $Office;
    private $CountryCode;
    private $CityCode;
    private $GroupCode = 115;
    private $AirlineID = 'S7';
    private $RequestorID = ''; // для тестовой зоны ''

    private $login;
    private $password;
    private $agency;

    private $POS;
    private $AirfarePOS;

    private $rlS7 = true;

    public $orderId = null;

    private $log = true;
    private $logDB = true;

    /**
     * Catch long query
     * @var boolean
     */
    private $longQueryCatch = true;

    /**
     * long query time
     * @var int - seconds
     */
    private $longQueryTime = 25;

    function __construct($providerOid) {
        $start = microtime(true);
        $this->provider    = WtDB::Ref()->ProvidersOfficeProcess(new WtMapArgs('oid', $providerOid));
        $this->TimeZone    = WtFunc::getTimeZone($this->provider['time_zone']);
        $this->Currency    = $this->provider['currency'];
        $this->login       = $this->provider['login'];
        $this->password    = $this->provider['password'];
        $this->agency      = $this->provider['agency'];
        $this->Office      = $this->provider['officeid'];
        $this->CountryCode = $this->provider['country'];

        switch ($this->Office) {
            case 'WWW801':
                $this->CityCode = 'OMS';
                break;

            case 'WWW802':
                $this->CityCode = 'OVB';
                break;

            case 'WWW826':
                $this->CityCode = 'NYC';
                break;

            case 'TJM900':
                $this->CityCode = 'TJM';  // это не нужно смотри default
                break;

            case 'KEJ901':
                $this->CityCode = 'KEJ';  // это не нужно смотри default
                break;

            case 'DYU901':
                $this->CityCode = 'NYC';
                break;

            case 'KZN901':
                $this->CityCode = 'KZN';  // это не нужно смотри default
                break;

            case 'SIP901':
                $this->CityCode = 'OVB';
                break;

            default:
                $this->CityCode = substr($this->Office, 0, 3);
                break;
        }

        $this->client = new WtGabrielDriver(null, array(
            'soap_version'   => SOAP_1_1,
            //            'location'     => 'https://sws.qa.sita.aero/sws/', // test url
            'location'       => 'https://sws.sita.aero/sws/',
            'uri'            => 'http://www.opentravel.org/OTA/2003/05',
            'stream_context' => stream_context_create(array('ssl' => array('ciphers' => 'RC4-SHA'))),
            'trace'          => 1 // need for $this->client->__getLastResponse() !!!!!!!
        ));

        define('SITA_PRIVATE_KEY', SITA_KEYS_PATH . $this->Office . '.key.pem');
        define('SITA_CERT_FILE', SITA_KEYS_PATH . $this->Office . '.cert.pem');

        $sourceAttr       = 'ERSP_UserID="' . $this->login . '/' . $this->password .
            '" AgentSine="' . $this->agency .
            '" PseudoCityCode="' . $this->Office .
            '" AgentDutyCode="' . $this->GroupCode .
            '" ISOCountry="' . $this->CountryCode .
            '" AirlineVendorID="' . $this->AirlineID .
            '" AirportCode="' . $this->CityCode . '"';
        $this->POS        = '<POS><Source ' . $sourceAttr . '/></POS>';
        $this->AirfarePOS = '<POS><Source ' . $sourceAttr . '/><RequestorID Type="6" ID="' . $this->RequestorID . '" ID_Context="Airfare"/></POS>';
        $this->FlightSeatMapPOS = '<POS><Source ' . $sourceAttr . '/><Source> <BookingChannel Type="5"/> </Source></POS>';
        if ($this->log) {
            TicketLog::Ref()->set_log_file(WtSession::Ref()->sid());
        }
        $this->longQueryLog($start, '__construct');
    }

    /**
     * Write request and response to log file
     *
     * @param string $actionName
     * @param string $functionName
     * @param array  $result
     */
    private function writeLog($actionName, $functionName, $result) {
        $start = microtime(true);
        if ($this->log) {
            TicketLog::write($this->client->__getLastRequest(), 'Request ' . $actionName, $functionName);
            TicketLog::write($this->client->__getLastResponse(), 'Response ' . $actionName, $functionName);
            TicketLog::write($result, $actionName, $functionName);
        }
        if ($this->logDB && isset($this->orderId)) {
            WtDB::Ref()->OrderLogInsert(new WtFuncArgs(array(
                'area'    => 'booking',
                'oid'     => $this->orderId,
                'action'  => $actionName,
                'details' => array(
                    'function' => $functionName,
                    'action'   => $actionName,
                    'request'  => $this->client->__getLastRequest(),
                    'response' => $this->client->__getLastResponse(),
                    'result'   => $result
                )
            )));
        }
        $this->longQueryLog($start, 'writeLog');
    }

    public function SessionClose() {
        return true;
    }

    /**
     * GetRL
     */
    public function GetRL() { //Задача 836 - отображение возвратного локатора авиакомпании для бронирований в GABRIEL
        $start = microtime(true);
        $this->logDB = false;
        $airlineRL   = array();
        $bookId = WtDB::Ref()->OrderBookExist(new WtMapArgs('pnr_number', $this->pnrNumber));

        $this->client->uri = 'http://sita.aero/SITA_ReadRQ/3/1';
        $reqPayloadString  = $this->POS . '<UniqueID Type="0" ID="' . $this->pnrNumber . '"/>';
        $this->client->OTA_ReadRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
        $result            = $this->client->__getLastResponse();
        $result            = str_replace('common:', '', $result);
        $result            = XML2Array::createArray($result);

        $result = isset($result['SOAP-ENV:Envelope']['SOAP-ENV:Body']) ? $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'] : $result['Envelope']['Body'];

        if (isset($result['OTA_AirBookRS']['Errors'])) { // Обрабодка ошибок
            $error = $result['OTA_AirBookRS']['Errors']['Error']['@value'];

            if ($bookId) {
                $rl = array('Error' => $error);
                WtDB::Ref()->OrderBooksUpdate(new WtMapArgs('rl_number', serialize($rl), 'id', $bookId));
                $this->longQueryLog($start, 'GetRL error');
                return $rl;
            }
        }

        if (isset($result['OTA_AirBookRS']['AirReservation']['BookingReferenceID'])) {

            $BookingReferenceIDTemp = $result['OTA_AirBookRS']['AirReservation']['BookingReferenceID'];
            $rlTemp = array();
            $Temp   = array();
            $i      = 0;

            if (!isset ($BookingReferenceIDTemp['@value'])) {
                foreach ($BookingReferenceIDTemp as $item) {
                    $Temp = explode(' ', $item['@attributes']['FlightRefNumberRPHList']);
                    foreach ($Temp as $one) {
                        $rlTemp[$i]['RPH']                = $one;
                        $rlTemp[$i]['BookingReferenceID'] = $item['@attributes']['ID'];
                        $i++;
                    }
                }
            } else {
                $Temp['BookingReferenceID'] = $BookingReferenceIDTemp['@attributes']['ID'];
                $Temp['RPH']                = $BookingReferenceIDTemp['@attributes']['FlightRefNumberRPHList'];
                $Temp['RPH']                = explode(' ', $Temp['RPH']);

                foreach ($Temp['RPH'] as $item) {
                    $rlTemp[$i]['RPH']                = $item;
                    $rlTemp[$i]['BookingReferenceID'] = $Temp['BookingReferenceID'];
                    $i++;
                }
            }

            $airlines = 0;
            $rls      = 0;
            $rlsForDb = 0;
            $OperatingAirlineTemp = array();
            foreach ($result['OTA_AirBookRS']['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'] as $segment) {

                $OperatingAirline = $segment['FlightSegment']['OperatingAirline']['@attributes']['Code'];
                $RPH              = $segment['FlightSegment']['@attributes']['RPH'];
                $rlsForDb++;
                if (in_array($OperatingAirline, $OperatingAirlineTemp)) {
                    continue;
                }

                $OperatingAirlineTemp[] = $OperatingAirline;

                if (!in_array($OperatingAirline, array('S7', 'GH'))) {
                        $airlines++;
                }

                foreach ($rlTemp as $item) {
                    if ($RPH == $item['RPH']) {
                        $rls++;
                        if (!in_array($OperatingAirline, array('S7', 'GH'))) {
                            $airlineRL[$OperatingAirline] = $item['BookingReferenceID'];
                        }
                    }
                }
            }

            if ($airlines == 0) { // Если в сегментах все s7 то берем operating_airline из базы.
                $rls = $rlsForDb;
                $transactions = WtDB::Ref()->GetTransactionsResults(new WtMapArgs('request_id', $this->request_id));
                $OperatingAirline = array();
                foreach ($transactions as $one) {

                        if ($one['first_operating_airline'] != null and !in_array($one['first_operating_airline'], array('S7', 'GH'))) {
                            $OperatingAirline[] = $one['first_operating_airline'];
                            $airlines++;
                        }
                        if ($one['second_operating_airline'] != null and !in_array($one['second_operating_airline'], array('S7', 'GH'))) {
                            $OperatingAirline[] = $one['second_operating_airline'];
                            $airlines++;
                        }
                        if ($one['third_operating_airline'] != null and !in_array($one['third_operating_airline'], array('S7', 'GH'))) {
                            $OperatingAirline[] = $one['third_operating_airline'];
                            $airlines++;
                        }
                }

                if ($airlines != 0) {
                    $i = 0;
                    foreach ($rlTemp as $item) {
                        $airlineRL[$OperatingAirline[$i]] = $item['BookingReferenceID'];
                        $i++;
                    }
                }
            }

      //      if ($rls != $airlines) {
       //         $airlineRL = array();
        //    } else {
                $result = array(); // Убираем дубли компаний и rl если такие есть.
                foreach ($airlineRL as $key => $value) {
                    if (array_key_exists($key, $result)) {
                        if (!$result[$key] == $value) {
                            $result[$key] = $value;
                        }
                    }
                    $result[$key] = $value;
                }
                $airlineRL = $result;
          //  }
        }

        if (!empty($airlineRL)) {
            if ($this->log) {
                TicketLog::write($airlineRL, 'airline RL', 'GetRL');
            }

            if ($bookId) {
                WtDB::Ref()->OrderBooksUpdate(new WtMapArgs('rl_number', serialize($airlineRL), 'id', $bookId));
            }
        }
        $this->longQueryLog($start, 'GetRL');
        return $airlineRL;
    }

    /**
     * Write exception request and response to log file
     *
     * @param string $functionName
     * @param object $exception
     */
    private function writeException($functionName, $exception) {
        $start = microtime(true);
        if ($this->log) {
            TicketLog::write($this->client->__getLastRequest(), 'Request', 'Exception ' . $functionName);
            TicketLog::write($this->client->__getLastResponse(), 'Response', 'Exception ' . $functionName);
            TicketLog::write($exception, 'Exception', $functionName);
        }
        if ($this->logDB && isset($this->orderId)) {
            WtDB::Ref()->OrderLogInsert(new WtFuncArgs(array(
                'area'    => 'booking',
                'oid'     => $this->orderId,
                'action'  => 'exception',
                'details' => array(
                    'function'  => $functionName,
                    'request'   => $this->client->__getLastRequest(),
                    'response'  => $this->client->__getLastResponse(),
                    'exception' => $exception
                )
            )));
        }
        $this->longQueryLog($start, 'writeException');
    }

    function CheckErrors($result) {
        $start = microtime(true);
        $return_result = false;
        if (!empty($result['Errors'])) {
            foreach ($result['Errors'] as $error) {
                $return_result['errors'][] = array(
                    'Id'      => $error['@attributes']['Code'],
                    'Message' => $error['@attributes']['Type'] . ': ' . $error['@value']
                );
            }
        }
        $this->longQueryLog($start, 'CheckErrors');
        return $return_result;
    }

    /**
     * Ping provides a simple echo to validate that SITA Reservations Web Services is working and
     * responding.
     *
     */
    function Ping() {
        $start = microtime(true);
        $this->longQueryLog($start, 'Ping');
        return true; // для оптимизации трафика и сокращения Look2Book функция убрана!!!
        /*        $string = 'Ping';
                try {
                    $reqPayloadString = $this->POS . '<EchoData>' . $string . '</EchoData>';
                    $this->client->OTA_PingRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
                    $this->writeLog('OTA_PingRQ', 'Ping', $result);

                    $this->pingData = array(
                        'request'  => htmlentities($this->client->__getLastRequest()),
                        'response' => htmlentities($this->client->__getLastResponse())
                    );

                    return isset($result['Success']) && $result['EchoData'] == $string;

                } catch (SoapFault $exception) {
                    $this->writeException('Ping', $exception);
                    return false;
                }*/
    }

    /**
     * The SITA_AirfareCalculateCurrencyRQ transaction converts monetary amounts from a given
     * currency into another currency using the specified rate type or exchange rate.
     *
     */
    function CurrencyConversion($args) {
        $start = microtime(true);
// для оптимизации трафика и сокращения Look2Book функция убрана!!!
// используем курсы валют провайдера Amadeus с той же валютой, что и у текущего провайдера
        $args['pid']    = WtDB::Ref()->ProvidersValue(new WtMapArgs('gds', 'Amadeus', 'status', 'Y', 'currency', $this->Currency));
        $RateOfExchange = WtDB::Ref()->CurrencyRateValue(new WtFuncArgs($args));
        if (!$RateOfExchange) {
            $args['pid']    = 17; // Amadeus DE
            $RateOfExchange = WtDB::Ref()->CurrencyRateValue(new WtFuncArgs($args));
        }
        $this->longQueryLog($start, 'CurrencyConversion');
        return $RateOfExchange;
        /*        try {
                    $RateOfExchange = 1;
                    $RateDate = date('Y-m-d');

                    $this->client->uri = 'http://www.sita.aero/PTS/fare/2005/12/CalculateCurrencyRQ';
                    $reqPayloadString = $this->POS .
                        '<POS><Source><RequestorID Type="6" ID="'.$this->RequestorID.'" ID_Context="Airfare"/></Source></POS>' .
                        '<CurrencyRQInfo FromCurrency="' . $args['from'] . '" ToCurrency="' . $args['to'] . '" RateDate="' . $RateDate . '"/>';

                    $result  = $this->client->SITA_AirfareCalculateCurrencyRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
                    $result = XML2Array::createArray($this->client->__getLastResponse());
                    $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
                    $this->writeLog('SITA_AirfareCalculateCurrencyRQ', 'CurrencyConversion', $result);

                    $ExchangeRates = $result['sita:SITA_AirfareCalculateCurrencyRS']['sita:ExchangeRates']['sita:ExchangeRate'];
                    if (isset($ExchangeRates['@value'])) $ExchangeRates = array($ExchangeRates);
                    foreach($ExchangeRates as $ExchangeRate) {
                        $RateOfExchange = $ExchangeRate['@attributes']['RateOfExchange'];
                        if ($ExchangeRate['@attributes']['ExchangeRateType'] == 'IATA_ClearingHouse') { // BankersBuyingRate, BankersSellingRate, IATA_ClearingHouse
                            break;
                        }
                    }

                    return isset($result['sita:SITA_AirfareCalculateCurrencyRS']['sita:Success']) ? $RateOfExchange : false;

                } catch (SoapFault $exception) {
                    $this->writeException('CurrencyConversion', $exception);
                    return false;
                }*/
    }

    /**
     * Search flight
     *
     * @param array $args
     *
     * @return array|boolean
     */
    function SearchFlight($args) {
        $start = microtime(true);
        $this->longQueryLog($start, 'SearchFlight');
        // для оптимизации трафика и сокращения Look2Book функция убрана!!!
        // результаты поиска предложений берем из Amadeus
        return false;
    }

    /**
     * Получение правил применения тарифа
     *
     * @param array $args
     *
     * @return boolean
     */
    function FlightPricing($args) {

       $start = microtime(true);
       $multy = strpos($args['ids'], '_');

       if ($multy === false) {
           $ids = array($args['ids']);
       } else {
           $ids = explode('_',$args['ids']);
       }

       $orders = WtDB::Ref()->GetTransactionsResults(new WtMapArgs('ids', $ids));

       $segments = array();
       $i = 1;
        foreach ($orders as $one) {

            $sita_response = unserialize($one['message']);

            if ($one['first_fare_basis'] != null) {
                $segments[$i]['fare_basis'] = $one['first_fare_basis'];
                $segments[$i]['marketing_airline'] = $one['first_marketing_airline'];
                $segments[$i]['DepartureDateTime'] = $one['first_departure_date'].'T'.$one['first_departure_time'];
                $segments[$i]['OriginLocation'] = $one['first_departure_airport'];
                $segments[$i]['DestinationLocation'] = $one['first_arrival_airport'];
                $segments[$i]['Airline'] = $one['first_operating_airline'];
                $i++;
            }

            if ($one['second_fare_basis'] != null) {
                $segments[$i]['fare_basis'] = $one['second_fare_basis'];
                $segments[$i]['marketing_airline'] = $one['second_marketing_airline'];
                $segments[$i]['DepartureDateTime'] = $one['second_departure_date'].'T'.$one['first_departure_time'];
                $segments[$i]['OriginLocation'] = $one['second_departure_airport'];
                $segments[$i]['DestinationLocation'] = $one['second_arrival_airport'];
                $segments[$i]['Airline'] = $one['second_operating_airline'];
                $i++;
            }

            if ($one['third_fare_basis'] != null) {
                $segments[$i]['fare_basis'] = $one['third_fare_basis'];
                $segments[$i]['marketing_airline'] = $one['third_marketing_airline'];
                $segments[$i]['DepartureDateTime'] = $one['third_departure_date'].'T'.$one['first_departure_time'];
                $segments[$i]['OriginLocation'] = $one['third_departure_airport'];
                $segments[$i]['DestinationLocation'] = $one['third_arrival_airport'];
                $segments[$i]['Airline'] = $one['third_operating_airline'];
                $i++;
            }
        }

        $FareInfos = $sita_response['Adult']['ota:AirItineraryPricingInfo']['ota:FareInfos']['ota:FareInfo'];
        $seg = array();

        if (isset($FareInfos['ota:FareReference'])) {

                $key = $FareInfos['ota:FareReference'];
                $seg[$key]['OriginLocation'] = $FareInfos['ota:DepartureAirport']['@attributes']['LocationCode'];
                $seg[$key]['DestinationLocation'] = $FareInfos['ota:ArrivalAirport']['@attributes']['LocationCode'];
                $seg[$key]['Airline'] = $FareInfos['ota:FilingAirline']['@attributes']['Code'];

        } else {

            foreach ($FareInfos as $FareInfo) {
                $key = $FareInfo['ota:FareReference'];
                $seg[$key]['OriginLocation'] = $FareInfo['ota:DepartureAirport']['@attributes']['LocationCode'];
                $seg[$key]['DestinationLocation'] = $FareInfo['ota:ArrivalAirport']['@attributes']['LocationCode'];
                $seg[$key]['Airline'] = $FareInfo['ota:FilingAirline']['@attributes']['Code'];
            }
        }

        $rule = array();

            foreach ($segments as $segment) {

                $key = $segment['fare_basis'];

                $request = array(
                    'DepartureDateTime'   => $segment['DepartureDateTime'],
                    'OriginLocation'      => $seg[$key]['OriginLocation'],
                    'DestinationLocation' => $seg[$key]['DestinationLocation'],
                    'FareReference'       => $segment['fare_basis'],
                    'Ref1'                => '',
                    'Ref2'                => '',
                    'Airline'             => $seg[$key]['Airline']
                );

                $fare = $segment['fare_basis'];
                $ruleBuffer = $this->AirfareRules($request);
                if (!is_array($ruleBuffer)) {

                    $key = $segment['fare_basis'].'R';

                    $request = array(
                        'DepartureDateTime'   => $segment['DepartureDateTime'],
                        'OriginLocation'      => $seg[$key]['OriginLocation'],
                        'DestinationLocation' => $seg[$key]['DestinationLocation'],
                        'FareReference'       => $segment['fare_basis'],
                        'Ref1'                => '',
                        'Ref2'                => '',
                        'Airline'             => $seg[$key]['Airline']
                    );

                    $fare = $segment['fare_basis'];
                    $ruleBuffer = $this->AirfareRules($request);
                }

                if (is_array($ruleBuffer)) {
                    $rule[$fare] = $ruleBuffer;
                } else {
                    $this->longQueryLog($start, 'FlightPricing !is_array(ruleBuffer');
                    return false;
                }
            }


        $seg_num = 1;
        foreach ($segments as $segment) {

            $fare = $segment['fare_basis'];

                $flightConditions = array(
                    'gds'               => $this->provider['gds'],
                    'fare_basis'        => $segment['fare_basis'],
                    'marketing_airline' => $segment['marketing_airline'],
                    'baggage'           => '',
                    'all_paragraphs'    => implode("\r", $rule[$fare]),
                    'segment'           => $seg_num,
                );

                if (!empty($args['order_id'])) {
                    $flightConditions['order'] = $args['order_id'];
                }

                if (WtDB::Ref()->FlightConditionsCheck(new WtMapArgs('order', $args['order_id'], 'airline', $segment['marketing_airline'], 'fare_basis', $segment['fare_basis'], 'gds', $this->provider['gds'], 'segment', $seg_num))) {
                    $seg_num++;
                    continue;
                }

                WtDB::Ref()->FlightConditionsInsert(new WtFuncArgs($flightConditions));
                $seg_num++;
            }
        $this->longQueryLog($start, 'FlightPricing');
        return true;
    }


    /**
     * The rule text message returns one or more SITA automated rules textual conditions and
     * regulations for the specified fare organized by category names.
     *
     */
    function AirfareRules($request) {

        $start = microtime(true);
        try {
            $this->client->uri = 'http://www.sita.aero/PTS/fare/2005/12/RulesRQ';
            $reqPayloadString  = '<OTA_AirRulesRQ>'
                . $this->AirfarePOS .
                '<RuleReqInfo>
                    <DepartureDate>' . $request['DepartureDateTime'] . '</DepartureDate>
                    <FareReference>' . $request['FareReference'] . '</FareReference>
                    <FilingAirline Code="' . $request['Airline'] . '"/>
                    <DepartureAirport LocationCode="' . $request['OriginLocation'] . '"/>
                    <ArrivalAirport LocationCode="' . $request['DestinationLocation'] . '"/>
                </RuleReqInfo>
            </OTA_AirRulesRQ>
            <AdditionalRulesRQData>
                <References>
                    <Ref1>' . $request['Ref1'] . '</Ref1>
                    <Ref2>' . $request['Ref2'] . '</Ref2>
                </References>
            </AdditionalRulesRQData>';

            $result = $this->client->SITA_AirfareRulesRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = $this->client->__getLastResponse();
            $result = str_replace('common:', '', $result);
            $result = XML2Array::createArray($result);
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
            $this->writeLog('SITA_AirfareRulesRQ', 'AirfareRules', $result);

            if (isset($result['ota:OTA_AirRulesRS']['ota:Success'])) {
                $rulesArr = array();
                $rules    = $result['ota:OTA_AirRulesRS']['ota:FareRuleResponseInfo']['ota:FareRules']['ota:SubSection'];

                foreach ($rules as $paragraph) {
                    if (isset($paragraph['ota:Paragraph']['ota:Text'])) {
                        $paragraph['ota:Paragraph'] = array($paragraph['ota:Paragraph']);
                    }
                    $paragraphText = '';
                    foreach ($paragraph['ota:Paragraph'] as $text) {
                        if (isset($text['ota:Text']['@value'])) {
                            $text['ota:Text'] = array($text['ota:Text']);
                        }
                        foreach ($text['ota:Text'] as $line) {
                            $paragraphText .= @$line['@value'] . "\r";
                        }
                        $rulesArr[$paragraph['@attributes']['SubSectionNumber']] = $paragraph['@attributes']['SubSectionNumber'] . ' ' . $paragraph['@attributes']['SubTitle'] . "\r" . $paragraphText;
                    }
                }

                return $rulesArr;
            } else {
                $errors = $result['ota:OTA_AirRulesRS']['ota:Errors']['ota:Error'];
                if (isset($errors['@value'])) {
                    $errors = array($errors);
                }
                $ErrorText = '';
                foreach ($errors as $error) {
                    $ErrorText .= $error['@value'] . ";\r";
                }
                $this->longQueryLog($start, 'AirfareRules');
                return $ErrorText;
            }
        } catch (SoapFault $exception) {
            $this->writeException('AirfareRules', $exception);
            $this->longQueryLog($start, 'AirfareRules exception');
            return false;
        }
    }

    /**
     * The Air Availability Request message requests Flight Availability for a specific city pair, on a
     * specific date and for a specific number of passengers and class of service. The request can be
     * narrowed down to display further availability for a specific airline, specific flight or a specific
     * booking class on a specific flight.
     *
     */
    function AirAvail($request) {
        $start = microtime(true);
        try {
            $this->client->uri = 'http://sita.aero/SITA_AirAvailRQ/3/0';

            $BookingClassPref = '';
            /*          switch ($request['class']) {
                            case 'E':   $classes = array('Y','B','H','K','L','M','Q','S','T','O','V','W','G','I','X');
                                        break;
                            case 'B':   $classes = array('C','D','J','I','Z');
                                        break;
                            case 'F':   $classes = array('F','P','A','J');
                                        break;
                        }
                        foreach ($classes as $class) {
                            $BookingClassPref .= '<BookingClassPref ResBookDesigCode="' . $class . '"/>';
                        }*/

            $offers = array();
            for ($i = 1; $i <= $request['flight_qty']; $i++) {
                $origin_code      = $request['departure_city_' . $i] ? $request['departure_city_' . $i] : $request['departure_airport_' . $i];
                $destination_code = $request['arrival_city_' . $i] ? $request['arrival_city_' . $i] : $request['arrival_airport_' . $i];
                $Segment          =
//                  '<OriginDestinationInformation RPH="' . $i . '">
                    '<OriginDestinationInformation>
                        <DepartureDateTime>' . $request['date_' . $i] . '</DepartureDateTime>
                        <OriginLocation LocationCode="' . $origin_code . '"/>
                        <DestinationLocation LocationCode="' . $destination_code . '"/>
                    </OriginDestinationInformation>';
                $SeatsRequested   = $request['adult_qty'] + $request['child_qty'];
//              $SeatsRequested += $request['infant_qty'];

                $reqPayloadString = $this->POS
                    . $Segment
                    . '<TravelerInfoSummary><SeatsRequested>'
                    . $SeatsRequested
                    . '</SeatsRequested></TravelerInfoSummary>'
                    . '<TravelPreferences><VendorPref Code="S7"/>' . $BookingClassPref . '</TravelPreferences>';

                $this->client->OTA_AirAvailRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
                $result = $this->client->__getLastResponse();
                $result = str_replace('common:', '', $result);
                $result = XML2Array::createArray($result);

                $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
                $this->writeLog('OTA_AirAvailRQ', 'AirAvail', $result);

                $results = $result['OTA_AirAvailRS']['OriginDestinationOptions']['OriginDestinationOption'];
                foreach ($results as $segment) {

                    if (isset($segment['FlightSegment']['@attributes'])) {
                        $segment['FlightSegment'] = array($segment['FlightSegment']);
                    }
                    $Legs = array();
                    foreach ($segment['FlightSegment'] as $leg) {

                        if ($leg['MarketingAirline']['@attributes']['Code'] != 'S7') {
                            continue;
                        }
                        if ($leg['@attributes']['Ticket'] != 'eTicket') {
                            continue;
                        }
                        $FlightSegment = array(
                            'RPH'               => $leg['@attributes']['RPH'],
                            'DepartureDateTime' => $leg['@attributes']['DepartureDateTime'],
                            'ArrivalDateTime'   => $leg['@attributes']['ArrivalDateTime'],
                            'FlightNumber'      => $leg['@attributes']['FlightNumber'],
                            'StopQuantity'      => $leg['@attributes']['StopQuantity'],
                            'Ticket'            => $leg['@attributes']['Ticket'],
                            'DepartureAirport'  => $leg['DepartureAirport']['@attributes']['LocationCode'],
                            'ArrivalAirport'    => $leg['ArrivalAirport']['@attributes']['LocationCode'],
                            'Equipment'         => $leg['Equipment']['@attributes']['AirEquipType'],
                            'MarketingAirline'  => $leg['MarketingAirline']['@attributes']['Code'],
                            'MarketingCabin'    => $leg['MarketingCabin']['@attributes']['Meal']
                        );
                        /*                      $bookClass = array();
                                                foreach($leg['BookingClassAvail'] as $class) {
                                                    if (intval($class['@attributes']['ResBookDesigQuantity']) < $SeatsRequested) continue;
                                                    $bookClass[$class['@attributes']['ResBookDesigCode']] = array(
                                                        'RPH'                       => $class['@attributes']['RPH'],
                                                        'ResBookDesigCode'          => $class['@attributes']['ResBookDesigCode'],
                                                        'ResBookDesigQuantity'      => $class['@attributes']['ResBookDesigQuantity'],
                                                        'ResBookDesigStatusCode'    => $class['@attributes']['ResBookDesigStatusCode']
                                                    );
                                                }
                                                $FlightSegment['BookingClassAvail'] = $bookClass;
                                                $FlightSegment['BookingClassAvail'] = array_keys($bookClass);*/
                        $Legs[] = $FlightSegment;
                    }

                    if (!empty($Legs)) {
                        $offer    = array(
                            'direction' => $i,
                            'segments'  => $Legs
                        );
                        $offers[] = $offer;
                    }
                }
            }

            $this->longQueryLog($start, 'AirAvail');
            return isset($result['OTA_AirAvailRS']['Success']) ? $offers : false;
        } catch (SoapFault $exception) {
            $this->writeException('AirAvail', $exception);
            $this->longQueryLog($start, 'AirAvail exception');
            return false;
        }
    }

    /**
     * Check availability flight
     *
     * @param array $args
     */
    function AvailabilityAirPrice($args) {
        $start = microtime(true);
        $this->longQueryLog($start, 'AirAvail AvailabilityAirPrice');
        return true; // для оптимизации трафика и сокращения Look2Book функция убрана!!!
    }

    /**
     * The itinerary pricing transaction allows the client to price a PNR returning the lowest price for
     * the specified passengers and itinerary. The itinerary pricing request is similar to the Airfare
     * terminal FSI entry.
     *
     */
    function AirfarePrice($request) {
        $start = microtime(true);
        try {
            $this->client->uri = 'http://www.sita.aero/PTS/fare/2005/11/PriceRQ';
            $Segments = $FareCurrencySelection = $PriceRequestInformation = '';
            $RPH = 0;
            foreach ($request['segments'] as $leg) {
                $airports = WtDB::Ref()->AirportsRows(new WtFuncArgs(array(
                    'codes'  => WtFunc::removeEmptyValues(array($leg['first_departure_airport'], $leg['first_arrival_airport'], $leg['second_arrival_airport'], $leg['third_arrival_airport'])),
                    'fields' => "a.code, a.country",
                    'map'    => 'code')));

                for ($i = 0; $i < $leg['segments']; $i++) {
                    switch ($i) {
                        case 0:
                            $pref = 'first';
                            break;
                        case 1:
                            $pref = 'second';
                            break;
                        case 2:
                            $pref = 'third';
                            break;
                    }
                    $RPH++;
                    $Segments .= '<OriginDestinationOption><FlightSegment ArrivalDateTime="' . $leg[$pref . '_arrival_date'] . 'T' . $leg[$pref . '_arrival_time']
                        . '" DepartureDateTime="' . $leg[$pref . '_departure_date'] . 'T' . $leg[$pref . '_departure_time'] . '" StopQuantity="' . 0
                        . '" FlightNumber="' . $leg[$pref . '_flight_number'] . '" ResBookDesigCode="' . $leg[$pref . '_booking_class'] . '" RPH="' . $RPH . '">'
                        . '<DepartureAirport LocationCode="' . $leg[$pref . '_departure_airport'] . '"/>'
                        . '<ArrivalAirport LocationCode="' . $leg[$pref . '_arrival_airport'] . '"/>'
                        . '<MarketingAirline Code="' . $leg[$pref . '_marketing_airline'] . '"/>'
                        . '</FlightSegment></OriginDestinationOption>';
                }

                $pref = $leg['stops'] == 2 ? 'third' : ($leg['stops'] == 1 ? 'second' : 'first');
                if (($leg['first_departure_airport'] == 'SIP' && $airports[$leg[$pref . '_arrival_airport']]['country'] == 'RU') ||
                    ($leg[$pref . '_arrival_airport'] == 'SIP' && $airports[$leg['first_departure_airport']]['country'] == 'RU') ||
                    ($this->CityCode == 'SIP')
                ) {
                    $FareCurrencySelection = 'FareCurrencySelection="RUB"';
//                   $FareCurrencySelection = 'CurrencyConversionCode="RUB"';
//                    if ($this->CityCode == 'SIP') {
//                        $PriceRequestInformation = '<PriceRequestInformation CurrencyCode="RUB"/>';
//                    }
                }
            }

            $AirTravelerAvail = '<PassengerTypeQuantity Code="ADT" Quantity="1" RPH="1"/>';
            $AirTravelerAvail .= $request['child_qty'] > 0 ? '<PassengerTypeQuantity Code="CNN" Quantity="1" RPH="1"/>' : '';
            $AirTravelerAvail .= $request['infant_qty'] > 0 ? '<PassengerTypeQuantity Code="INF" Quantity="1" RPH="1"/>' : '';
            $priceRequestInformation = '';
            $additionalPriceRQData = '<AdditionalPriceRQData ' . $FareCurrencySelection . ' MaxResponses="10" DoNotIncludeFBCInHFC="true" PointOfTicketing="' . $this->CityCode . '"/>';

            $faresBasis = array();
            foreach ($request['segments'] as $segment) { // Проверяем есть ли код тарифа за одно отдаем его в ФФ
                if ($segment['first_fare_basis'] != null) {
                    $faresBasis[] = $segment['first_fare_basis'];
                }
                if ($segment['second_fare_basis'] != null) {
                    $faresBasis[] = $segment['second_fare_basis'];
                }
                if ($segment['third_fare_basis'] != null) {
                    $faresBasis[] = $segment['third_fare_basis'];
                }
            }

            if (empty($faresBasis)) { // Если нет тарифа, то что-то не так с поиском было.
                $this->longQueryLog($start, 'AirfarePrice no fare');
                return array('status' => 'error', 'message' => FuncLang::value('lbl_gabriel_error_fasebase'));
            }

            $faresFF = array('BS', 'FL');
            if (in_array($request['segments']['0']['fare_family'], $faresFF)) {  // Если тариф ФФ, то делаем преоценку для ФФ
                $priceRequestInformation = '<PriceRequestInformation PricingSource="Both"/>';
                $additionalSegmentInfo = '';
                $i = 1;
                foreach ($faresBasis as $one) {
                    $additionalSegmentInfo = $additionalSegmentInfo .
                        '<AdditionalSegmentInfo SegmentRPH="'.$i.'" FareReference="'. $one .'" />';
                    $i++;
                }

                $additionalPriceRQData = '
                  <AdditionalPriceRQData ' . $FareCurrencySelection . ' MaxResponses="10" DoNotIncludeFBCInHFC="true" PointOfTicketing="' . $this->CityCode . '">
                    <AdditionalSegmentInfos>'
                    . $additionalSegmentInfo .
                    '</AdditionalSegmentInfos>
                  </AdditionalPriceRQData>';
            }

            $reqPayloadString = '<OTA_AirPriceRQ xmlns="http://sita.aero/SITA_AirDemandTicketRQ/3/0">'
                    . $this->AirfarePOS .
                    '<AirItinerary><OriginDestinationOptions>' . $Segments . '</OriginDestinationOptions></AirItinerary>
                    <TravelerInfoSummary>
                        '. $priceRequestInformation .'
                        <AirTravelerAvail>' . $AirTravelerAvail . '</AirTravelerAvail>'
                        . $PriceRequestInformation .
                    '</TravelerInfoSummary>
                </OTA_AirPriceRQ>'.
                $additionalPriceRQData;

            $this->client->SITA_AirfarePriceRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = $this->client->__getLastResponse();

            $result = str_replace('common:', '', $result);

            $result = XML2Array::createArray($result);
            $result = isset($result['SOAP-ENV:Envelope']['SOAP-ENV:Body']) ? $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'] : $result['Envelope']['Body'];
            $this->writeLog('SITA_AirfarePriceRQ', 'AirfarePrice', $result);

            $warnings = @$result['sita:SITA_AirfarePriceRS']['ota:OTA_AirPriceRS']['ota:Warnings']['ota:Warning'];
            if (empty($result['sita:SITA_AirfarePriceRS']['ota:OTA_AirPriceRS']['ota:PricedItineraries']) && !empty($warnings)) {
                $message  = '';
                $warnings = isset($warnings['@value']) ? array($warnings) : $warnings;
                foreach ($warnings as $warning) {
                    $message .= $warning['@value'] . "<br/>";
                }
                $this->longQueryLog($start, 'AirfarePrice Warning');
                return array('status' => 'error', 'message' => $message);
            }

            $errors = @$result['sita:SITA_AirfarePriceRS']['ota:OTA_AirPriceRS']['ota:Errors']['ota:Error'];
            if (empty($result['sita:SITA_AirfarePriceRS']['ota:OTA_AirPriceRS']['ota:PricedItineraries']) && !empty($errors)) {
                $message = '';
                $errors  = isset($errors['@value']) ? array($errors) : $errors;
                foreach ($errors as $error) {
                    $message .= $error['@value'] . "<br/>";
                }
                $this->longQueryLog($start, 'AirfarePrice error');
                return array('status' => 'error', 'message' => $message);
            }

            $PricedItinerary = array();
            $AirfarePrices   = array(
                'AdultBaseFare' => 0,
                'ChildBaseFare' => 0,
            );

            $PricedItineraries = isset($result['sita:SITA_AirfarePriceRS']['ota:OTA_AirPriceRS']['ota:PricedItineraries']['ota:PricedItinerary']['ota:AirItinerary']) ? array($result['sita:SITA_AirfarePriceRS']['ota:OTA_AirPriceRS']['ota:PricedItineraries']['ota:PricedItinerary']) : $result['sita:SITA_AirfarePriceRS']['ota:OTA_AirPriceRS']['ota:PricedItineraries']['ota:PricedItinerary'];
            foreach ($PricedItineraries as $price) {
                $FareBreakdown = $price['ota:AirItineraryPricingInfo']['ota:PTC_FareBreakdowns']['ota:PTC_FareBreakdown'];
                $FareInfo      = $price['ota:AirItineraryPricingInfo']['ota:FareInfos']['ota:FareInfo'];
                $References    = isset($FareInfo['ota:TPA_Extensions']) ? $FareInfo['ota:TPA_Extensions']['sita:SITA_FareInfoExtension']['sita:References'] : $FareInfo[0]['ota:TPA_Extensions']['sita:SITA_FareInfoExtension']['sita:References'];
                switch ($FareBreakdown['ota:PassengerTypeQuantity']['@attributes']['Code']) {
                    case 'ADT':
                        $prefix = 'Adult';
                        break;
                    case 'CNN':
                        $prefix = 'Child';
                        break;
                    case 'INF':
                        $prefix = 'Infant';
                        break;
                }

                if (isset($AirfarePrices[$prefix . 'TotalFare']) && $AirfarePrices[$prefix . 'TotalFare'] < $FareBreakdown['ota:PassengerFare']['ota:TotalFare']['@attributes']['Amount']) {
                    continue;
                }

                $AirfarePrices['PTC'][$price['@attributes']['SequenceNumber']] = $FareBreakdown['ota:PassengerTypeQuantity']['@attributes']['Code'] == 'CNN' ? 'CHD' : $FareBreakdown['ota:PassengerTypeQuantity']['@attributes']['Code'];
                if ($prefix == 'Adult') {
                    $AirfarePrices['Ref1']            = $References['sita:Ref1'];
                    $AirfarePrices['FareBasisCode']   = $FareBreakdown['ota:FareBasisCodes']['ota:FareBasisCode'];
                    $AirfarePrices['TicketTimeLimit'] = $price['ota:TicketingInfo']['@attributes']['TicketTimeLimit'];
                    $AirfarePrices['Currency']        = $FareBreakdown['ota:PassengerFare']['ota:TotalFare']['@attributes']['CurrencyCode'];
                }

                $AirfarePrices[$prefix . 'TotalFare'] = $FareBreakdown['ota:PassengerFare']['ota:TotalFare']['@attributes']['Amount'];
                if ($FareBreakdown['ota:PassengerFare']['ota:BaseFare']['@attributes']['CurrencyCode'] == $this->provider['currency']) {
                    $AirfarePrices[$prefix . 'BaseFare'] = $FareBreakdown['ota:PassengerFare']['ota:BaseFare']['@attributes']['Amount'];
                } elseif (isset($FareBreakdown['ota:PassengerFare']['ota:EquivFare']) &&
                    $FareBreakdown['ota:PassengerFare']['ota:EquivFare']['@attributes']['CurrencyCode'] == $this->provider['currency']
                ) {
                    $AirfarePrices[$prefix . 'BaseFare'] = $FareBreakdown['ota:PassengerFare']['ota:EquivFare']['@attributes']['Amount'];
                } else {
                    $AirfarePrices[$prefix . 'BaseFare'] = $FareBreakdown['ota:PassengerFare']['ota:BaseFare']['@attributes']['Amount'];
                    if (isset($FareBreakdown['ota:PassengerFare']['ota:BaseFare']['@attributes']['Rate'])) {
                        $AirfarePrices[$prefix . 'BaseFare'] *= $FareBreakdown['ota:PassengerFare']['ota:BaseFare']['@attributes']['Rate'];
                    }
                }

                $priceInfo = array();
                $Taxes     = array();
                if (isset($FareBreakdown['ota:PassengerFare']['ota:Taxes'])) {
                    $AirfarePrices[$prefix . 'TaxAmount'] = 0;
                    $PassengerTax                         = isset($FareBreakdown['ota:PassengerFare']['ota:Taxes']['ota:Tax']['@attributes']) ? array($FareBreakdown['ota:PassengerFare']['ota:Taxes']['ota:Tax']) : $FareBreakdown['ota:PassengerFare']['ota:Taxes']['ota:Tax'];
                    foreach ($PassengerTax as $tax) {
                        if (!is_array($tax) || isset($Taxes[$tax['@attributes']['TaxCode']])) {
//                            continue;
                        }
                        $Taxes[$tax['@attributes']['TaxCode']] = array(
                            'TaxCode'  => $tax['@attributes']['TaxCode'],
//                            'Amount'   => $tax['@attributes']['Amount'],
                            'Currency' => $tax['@attributes']['CurrencyCode']
                        );
                        $Taxes[$tax['@attributes']['TaxCode']]['Amount'] += $tax['@attributes']['Amount'];
                        $AirfarePrices[$prefix . 'TaxAmount'] += $tax['@attributes']['Amount'];
                    }
                }
                if ($prefix == 'Adult') {
                    $AirfarePrices['Taxes'] = $Taxes;
                }
//              $priceInfo['Notes'] = $price['ota:Notes'];
                $priceInfo['Ref2'] = $References['sita:Ref2'];

                $AirfarePrices['PassengersRef'][$prefix] = $priceInfo;

                $PricedItinerary[$prefix] = $price;
            }
            $AirfarePrices['TotalFare'] = ($AirfarePrices['AdultBaseFare'] * $request['adult_qty'])  + ($AirfarePrices['ChildBaseFare'] * $request['child_qty'])  + ($AirfarePrices['InfantBaseFare'] * $request['infant_qty']);
            $AirfarePrices['TotalTax']  = ($AirfarePrices['AdultTaxAmount'] * $request['adult_qty']) + ($AirfarePrices['ChildTaxAmount'] * $request['child_qty']) + ($AirfarePrices['InfantTaxAmount'] * $request['infant_qty']);

            if (isset($result['sita:SITA_AirfarePriceRS']['sita:AdditionalPriceRSData'])) {
                $Itineraries = isset($result['sita:SITA_AirfarePriceRS']['sita:AdditionalPriceRSData']['sita:AdditionalItinerariesData']['sita:AdditionalItineraryData']['sita:AdditionalSegmentInfos']) ? array($result['sita:SITA_AirfarePriceRS']['sita:AdditionalPriceRSData']['sita:AdditionalItinerariesData']['sita:AdditionalItineraryData']) : $result['sita:SITA_AirfarePriceRS']['sita:AdditionalPriceRSData']['sita:AdditionalItinerariesData']['sita:AdditionalItineraryData'];
                foreach ($Itineraries as $itinerary) {
                    $PTC          = $AirfarePrices['PTC'][$itinerary['@attributes']['SequenceNumber']];
                    $SegmentInfos = isset($itinerary['sita:AdditionalSegmentInfos']['sita:AdditionalSegmentInfo']['sita:RebookResBookDesigCodes']) ? array($itinerary['sita:AdditionalSegmentInfos']['sita:AdditionalSegmentInfo']) : $itinerary['sita:AdditionalSegmentInfos']['sita:AdditionalSegmentInfo'];
                    foreach ($SegmentInfos as $segment) {
                        $segNum                                                 = $segment['@attributes']['SegmentRPH'];
                        $AirfarePrices['Baggage'][$PTC][$segNum]                = $segment['@attributes']['FreeBaggageAllowance'];
                        $AirfarePrices['AdditionalSegmentInfos'][$PTC][$segNum] = $segment['@attributes'];
                    }
                }
            }

            $AirfarePrices['PricedItinerary'] = serialize(array_merge($PricedItinerary, $AirfarePrices));

//dump_die($AirfarePrices);
            $this->longQueryLog($start, 'AirfarePrice');
            return isset($result['sita:SITA_AirfarePriceRS']['ota:OTA_AirPriceRS']['ota:Success']) ? $AirfarePrices : false;
        } catch (SoapFault $exception) {
            $this->writeException('AirfarePrice', $exception);
            $this->longQueryLog($start, 'AirfarePrice exception');
            return false;
        }
    }

    /**
     * Формирование XML запроса на бронирование
     *
     * @param array $args
     *
     * @return string
     */
    function GetBookingRequest($args) {
        $start = microtime(true);
        $results    = WtDB::Ref()->GetTransactionsResults(new WtMapArgs('ids', explode('_', $args['result_ids'])));
        $INF        = $countInf = 0;
        $Passengers = $OSIText = $SSRText = $SeatText = '';
        $this->rlS7 = true;
        $PhoneCode  = WtDB::Ref()->CountryValue(new WtMapArgs('code', $args['buyer_phonecode'], 'value', 'c.phone'));
        $SSRTextInf = '';
        foreach ($args['passengers'] as $key => $passenger) {
            //$passenger['lastname'] = str_replace(' ', '_', $passenger['lastname']);
            //$passenger['firstname'] = str_replace(' ', '_', $passenger['firstname']);
            $RPH = $key + 1;
            switch ($passenger['type']) {
                case 'ADT':
                    $passengerCategoryCode = 'ADT';
                    break;
                case 'CHD':
                    $passengerCategoryCode = 'CNN';
                    break;
                case 'INF':
                    $passengerCategoryCode = 'INF';
                    $INF++;
                    $countInf++;
                    break;
            }
            $AccompaniedByInfant   = $passenger['type'] == 'INF' ? ' AccompaniedByInfant="false"' : '';
            $PassengerTypeQuantity = '<PassengerTypeQuantity PassengerTypeCode="' . $passengerCategoryCode . '" Quantity="1"/>';

            list($y, $m, $d) = explode('-', $passenger['birth_date']);
            $passengerBirthDate = strtoupper(date('dMy', mktime(0, 0, 0, $m, $d, $y)));
            list($y, $m, $d) = explode('-', $passenger['doc_expire']);
            $passengerDocExpire = strtoupper(date('dMy', mktime(0, 0, 0, $m, $d, $y)));
            $gender             = strtoupper($passenger['title']) == 'MR' ? 'M' : 'F';

            $middleName = '';
            preg_match('/(?P<firstname>[A-Z]+)\s*(?P<middlename>.+)?/', $passenger['firstname'], $matches);
            if ($matches) {
                $passenger['firstname'] = $matches['firstname'];
                $middleName             = $matches['middlename'];
            }
//          $SeatText = '<SeatRequest FlightRefNumberRPHList="1" TravelerRefNumberRPHList="1" SeatNumber="2A"/>';

            if ($passenger['type'] == 'INF') {
                $SSRTextInf .=
                    '<SpecialServiceRequest SSRCode="INFT" ServiceQuantity="1" Status="NN" FlightRefNumberRPHList="SegList" TravelerRefNumberRPHList="' . $INF . '">' .
                    '<Airline Code="YY"/>' .
                    '<Text>.' . $passenger['lastname'] . '/' . $passenger['firstname'] . ' ' . $passengerBirthDate . '</Text>' .
                    '</SpecialServiceRequest>';
            }

            if ($passenger['type'] == 'CHD') {
                $SSRText .=
                    '<SpecialServiceRequest SSRCode="CHLD" ServiceQuantity="1" Status="HK" TravelerRefNumberRPHList="' . $RPH . '">' .
                    '<Airline Code="YY"/><Text>/' . $passengerBirthDate . '</Text>' .
                    '</SpecialServiceRequest>';
                $passenger['title'] = $passenger['type'];
            }

            $sufix = '';
            if ($passenger['type'] == 'INF') {
                $gender .= 'I';
                $sufix = '/INF' . $INF;
                $RPH   = $INF;
            }

//            $SSRText .=
//                '<SpecialServiceRequest SSRCode="PSPT" ServiceQuantity="1" Status="HK" TravelerRefNumberRPHList="' . $RPH . '">' .
//                    '<Airline Code="S7"/><Text>/P ' . $passenger['doc_number'] . '/' . $passenger['citizenship'] . '/' . $passengerBirthDate . '/' . $passenger['lastname'] . '/' . $passenger['firstname'] . '/' . $gender . '/H</Text>' .
//                '</SpecialServiceRequest>';
            $SSRText .=
                '<SpecialServiceRequest SSRCode="DOCS" ServiceQuantity="1" Status="HK" TravelerRefNumberRPHList="' . $RPH . '">' .
                '<Airline Code="YY"/><Text>/P/' . $passenger['citizenship'] . '/' . $passenger['doc_number'] . '/' . $passenger['citizenship'] . '/' . $passengerBirthDate . '/' . $gender . '/' . $passengerDocExpire . '/' . $passenger['lastname'] . '/' . $passenger['firstname'] . '</Text>' .
                '</SpecialServiceRequest>';
            $SSRText .=
                '<SpecialServiceRequest SSRCode="FOID" Status="HK" TravelerRefNumberRPHList="' . $RPH . '">' .
                '<Airline Code="S7"/><Text>/PP' . $passenger['doc_number'] . $sufix . '</Text>' .
                '</SpecialServiceRequest>';

            $PassengerPhone = '';
            if ($key == 0) {
                $PassengerPhone =
                    '<Telephone PhoneNumber="' . $args['buyer_phonenumber'] . '" PhoneTechType="1" PhoneLocationType="7" AreaCityCode="' . $PhoneCode . '"/>' .
                    '<Email>' . $args['buyer_email'] . '</Email>' .
                    '<Address FormattedInd="false"><AddressLine>' . $passenger['address'] . '</AddressLine></Address>';
            }

            if ($passenger['type'] != 'INF') {
                $Passengers .=
                    '<AirTraveler BirthDate="' . $passenger['birth_date'] . '" PassengerTypeCode="' . $passenger['type'] . '"' . $AccompaniedByInfant . '>' .
                    '<PersonName>' .
                    '<NamePrefix>' . $passenger['title'] . '</NamePrefix>' .
                    '<GivenName>' . $passenger['firstname'] . '</GivenName>' .
                    '<MiddleName>' . $middleName . '</MiddleName>' .
                    '<Surname>' . $passenger['lastname'] . '</Surname>' .
                    '</PersonName>' .
                    $PassengerPhone .
                    '<Document DocID="' . $passenger['doc_number'] . '" DocType="' . $passenger['doc_type'] . '" DocHolderNationality="' . $passenger['citizenship'] . '">' .
                    '<DocHolderName>' . $passenger['title'] . ' ' . $passenger['firstname'] . ' ' . $passenger['lastname'] . '</DocHolderName>' .
                    '</Document>' .
                    $PassengerTypeQuantity .
                    '<TravelerRefNumber RPH="' . $RPH . '"/>' .
                    '</AirTraveler>';
            }
        }
        $countSeats = count($args['passengers']) - $countInf;

        $Segments               = $SPA = $StopQuantity = '';
        $FlightRefNumberRPHList = array();
        $RPH                    = 1;
        $listSegments           = '1';
        foreach ($results as $segment) {
            for ($i = 0; $i <= $segment['stops']; $i++) {
                if ($RPH != 1) { // for infant get list
                    $listSegments .= ' '.$RPH;
                }
                switch ($i) {
                    case 0:
                        $pref = 'first';
                        break;
                    case 1:
                        $pref = 'second';
                        break;
                    case 2:
                        $pref = 'third';
                        break;
                }

                if (!in_array($segment[$pref . '_operating_airline'], array('GH', 'S7'))) {
                    $this->rlS7 = false;
                }

                // члены альянса Oneworld – IB, BA, RJ, AB, HG, JL, CX, KA, AA, AY, QF, QR, MH, JJ, UL, с некоторыми из них есть SPA
                if (in_array($segment[$pref . '_marketing_airline'], array('IB', 'BA', 'RJ', 'AB', 'HG', 'JL', 'CX', 'KA', 'AA', 'AY', 'QF', 'QR', 'MH', 'JJ', 'UL'))) {
                    $SPA = 'DirectAccess="true" DirectAccessAirlineCode="' . $segment[$pref . '_marketing_airline'] . '"';
                    $StopQuantity = ' StopQuantity="0"';
                }

                if ($segment[$pref . '_operating_airline'] == 'GH') {
                    $segment[$pref . '_operating_airline'] = 'S7';
                }

                $flight_number = (string) $segment[$pref . '_flight_number'];

                if (strlen($flight_number) == 1) {
                    $flight_number = '00' . $flight_number;
                }
                if (strlen($flight_number) == 2) {
                    $flight_number = '0' . $flight_number;
                }

                $Segments .=
                    '<OriginDestinationOption ' . $SPA . '>' .
                    '<FlightSegment ArrivalDateTime="' . $segment[$pref . '_arrival_date'] . 'T' . $segment[$pref . '_arrival_time'] .
                        '" DepartureDateTime="' . $segment[$pref . '_departure_date'] . 'T' . $segment[$pref . '_departure_time'] .
                        '" FlightNumber="' . $flight_number .
                        '" RPH="' . $RPH . '"' .
                        $StopQuantity .
                        ' Ticket="eTicket" NumberInParty="1" ResBookDesigCode="' . $segment[$pref . '_booking_class'] . '">' .
                    '<DepartureAirport LocationCode="' . $segment[$pref . '_departure_airport'] . '"/>' .
                    '<ArrivalAirport LocationCode="' . $segment[$pref . '_arrival_airport'] . '"/>' .
                    '<MarketingAirline Code="' . $segment[$pref . '_marketing_airline'] . '"/>' .
//                    '<OperatingAirline Code="' . $segment[$pref . '_operating_airline'] . '" FlightNumber="' . sprintf('%04d', $segment[$pref . '_flight_number']) . '"/>' .
//                    '<OperatingAirline Code="S7" FlightNumber="' . sprintf('%04d', $segment[$pref.'_flight_number']) . '"/>' .
                    '<BookingClassAvails>' .
                    '<BookingClassAvail RPH="1" ResBookDesigCode="' . $segment[$pref . '_booking_class'] . '" ResBookDesigStatusCode="NN" ResBookDesigQuantity="' . $countSeats . '" />' .
                    '</BookingClassAvails>' .
                    '</FlightSegment>' .
                    '</OriginDestinationOption>'
                ;

                list($y, $m, $d) = explode('-', $segment[$pref . '_departure_date']);
                $departure_date           = strtoupper(date('dM', mktime(0, 0, 0, $m, $d, $y)));
                $SSR_segments             = $segment[$pref . '_departure_airport'] . $segment[$pref . '_arrival_airport'] . ' ' . $segment[$pref . '_flight_number'] . $segment[$pref . '_booking_class'] . $departure_date;
                $FlightRefNumberRPHList[] = $RPH;
                $RPH++;
            }
        }

        if (count($FlightRefNumberRPHList) > 1) {
            $FlightRefNumberRPHList = implode(',', $FlightRefNumberRPHList);
            $SSRText                = str_replace('FlightRefNumberRPHList="1">', 'FlightRefNumberRPHList="' . $FlightRefNumberRPHList . '">', $SSRText);
        }

        if ($SSRTextInf != '') {
            $SSRTextInf = str_replace('SegList', $listSegments, $SSRTextInf);
            $SSRTextInf .= $SSRText;
            $SSRText = $SSRTextInf;
        }

        $gds_name = strtoupper($this->provider['gds_name']);
        $gdsUserInfo = WtDB::Ref()->UserRow(new WtMapArgs('user_id', $this->provider['user_id']));
        $cityGds = WtDB::Ref()->CityRow(new WtFuncArgs(array('name' => $gdsUserInfo['city'],
                                                             'locale' => 'RU')));

        $phoneGds = preg_replace('/[^0-9]/', '', $gdsUserInfo['phone']);

        $OSIText .= '<OtherServiceInformation>
                       <Airline Code="S7"/>'.
                        '<Text>CTCT ' . $cityGds['code'] .' ' . $phoneGds . '/' . $gds_name . '</Text>
                    </OtherServiceInformation>';

        $OSIText = '<OtherServiceInformations>' . $OSIText . '</OtherServiceInformations>';

//      $SeatText = '<SeatRequests>' . $SeatText . '</SeatRequests>';

        $SSRText .=
            '<SpecialServiceRequest SSRCode="CTCM" Status="HK" TravelerRefNumberRPHList="1" ServiceQuantity="1">' .
            '<Airline Code="YY"/><Text>' . $args['buyer_phonenumber'] . '/' .$args['buyer_phonecode']. '</Text>' .
            '</SpecialServiceRequest>';

        $email = str_replace('@', '//', $args['buyer_email']);
        $email = str_replace('_', '..', $email);
        $SSRText .=
            '<SpecialServiceRequest SSRCode="CTCE" Status="HK" TravelerRefNumberRPHList="1" ServiceQuantity="1">' .
            '<Airline Code="YY"/><Text>' . $email . '</Text>' .
            '</SpecialServiceRequest>';

        $SSRText = '<SpecialServiceRequests>' . $SSRText . '</SpecialServiceRequests>';

        $Remarks = '<Remark RPH="1">ETM-SYSTEM ORDER ' . $args['id'] . '</Remark>' .
            '<Remark RPH="2">ETM-SYSTEM PASSENGERS/ADT/' . $args['adult_qty'] . '/CHD/' . $args['child_qty'] . '/INF/' . $args['infant_qty'] . '</Remark>' .
            '<Remark RPH="3">ETM-SYSTEM ADT/FARE/' . $args['adult_base_fare'] . '/TAX/' . $args['adult_tax_amount'] . '/CURRENCY/' . $args['transaction_currency'] . '</Remark>' .
            '<Remark RPH="4">ETM-SYSTEM CHD/FARE/' . $args['child_base_fare'] . '/TAX/' . $args['child_tax_amount'] . '/CURRENCY/' . $args['transaction_currency'] . '</Remark>' .
            '<Remark RPH="5">ETM-SYSTEM INF/FARE/' . $args['infant_base_fare'] . '/TAX/' . $args['infant_tax_amount'] . '/CURRENCY/' . $args['transaction_currency'] . '</Remark>' .
            '<Remark RPH="6">ETM-SYSTEM INTERNET BOOKING</Remark>';

        $externalId = WtDB::Ref()->UserValue(new WtMapArgs('fields', 'external_id', 'user_id', $args['user_id']));
        if (!$externalId) {
            $externalId = WtDB::Ref()->UserValue(new WtMapArgs('fields', 'external_id', 'user_id', $args['subagent_id']));
        }
        if (!$externalId) {
            $externalId = WtDB::Ref()->UserValue(new WtMapArgs('fields', 'external_id', 'user_id', $args['agent_id']));
        }
        if ($externalId) {
            $Remarks .= '<Remark RPH="7">ETM-SYSTEM AGENT ID ' . $externalId . '</Remark>';
        }
        $Remarks .= '<Remark RPH="8">ETM-SYSTEM USERID ' . $args['user_id'] . '</Remark>';

        //ремарка для проверки с какой базы была сделана бронь.
        $remarka = WtDB::Ref()->UserConfigValue(new WtMapArgs('uid', 1, 'name', 'DB_remark', 'status', 'A'));
        $Remarks .= '<Remark RPH="9">DB_TYPE ' . $remarka . '</Remark>';

        $request =
            '<AirItinerary>' .
            '<OriginDestinationOptions>'
            . $Segments .
            '</OriginDestinationOptions>'.
            '</AirItinerary>'.
            '<TravelerInfo>'
            . $Passengers .
            '<SpecialReqDetails>' .
//          $SeatText .
            $SSRText .
            $OSIText .
            '<Remarks>' . $Remarks . '</Remarks>' .
            '</SpecialReqDetails>' .
            '</TravelerInfo>' .
            '<Ticketing TicketType="eTicket"/>';// .
//          '<Queue QueueGroup="' . $args['rules']['booking_queue'] . '"/>';// DateTime="2013-03-18" Text="test add queue"/>';
        $this->longQueryLog($start, 'GetBookingRequest');

        return $request;
    }

    /**
     * Функция бронирования
     *
     * @param array $args
     *
     * @return array|boolean
     */
    function BookFlight($args) {
        $start = microtime(true);
        try {
            $this->orderId = $args['id']; // for order_log
            TicketLog::Ref()->set_log_file('order_' . $this->orderId . '.log');
            $request           = $this->GetBookingRequest($args);
            $this->client->uri = 'http://sita.aero/SITA_AirBookRQ/3/1';
            $this->client->actionAttr['OTA_AirBookRQ'] = array(
                'TransactionIdentifier' => ''
            );
            $reqPayloadString  = $this->POS . $request;

            $this->client->OTA_AirBookRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = $this->client->__getLastResponse();
            $result = str_replace('common:', '', $result);
            $result = XML2Array::createArray($result);

            $result = isset($result['SOAP-ENV:Envelope']['SOAP-ENV:Body']) ? $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'] : $result['Envelope']['Body'];
            $this->writeLog('OTA_AirBookRQ', 'BookFlight', $result);

            $order = array('id' => $args['id']);

            $checkErrors = $this->CheckErrors($result['OTA_AirBookRS']);
            if (is_array($checkErrors)) {
                $order['status'] = 'E';
                $errorsTxt       = '';
                $errorsMsg       = '';
                if (isset($checkErrors['errors'])) {
                    $errorsTxt = 'Errors:<br>';
                    $errorsMsg = 'ERROR:\n';

                    foreach ($checkErrors['errors'] as $error) {
                        $errorsTxt .= $error['Id'] . ': ' . $error['Message'] . '<br>';

                        $e = preg_replace('/^\s*ErrorNo:\s*(\d+|null)\s*-\s*\[HostError:\s*(\d+|null)?\s*/i', '', $error['Message']);
                        $e = preg_replace('/\]\s*-\s*ErrNo\s*(\d+|null).*$/i', '', $e);
                        $e = preg_replace('/ErrorNo:\s*(\d+|null)\s*-?\s*/i', '', $e);
                        $e = preg_replace('/HostError:\s*(\d+|null)?\s*/i', '', $e);
                        $e = preg_replace('/\s*\[ID:\w+\]/i', '', $e);
                        $e = preg_replace('/Fatal error:\s*/i', '', $e);
                        $errorsMsg .= $e . '\n';
                    }
                }

                $warningsTxt = '';
                $warningsMsg = '';
                if (isset($checkErrors['warnings'])) {
                    $warningsTxt = 'Warnings:<br>';
                    $warningsMsg = '\n\nWARNING:\n';

                    foreach ($checkErrors['warnings'] as $warning) {
                        $warningsTxt .= $warning['Id'] . ': ' . $warning['Message'] . '<br>';

                        $e = preg_replace('/^\s*ErrorNo:\s*(\d+|null)\s*-\s*\[HostError:\s*(\d+|null)?\s*/i', '', $warning['Message']);
                        $e = preg_replace('/\]\s*-\s*ErrNo\s*(\d+|null).*$/i', '', $e);
                        $e = preg_replace('/ErrorNo:\s*(\d+|null)\s*-?\s*/i', '', $e);
                        $e = preg_replace('/HostError:\s*(\d+|null)?\s*/i', '', $e);
                        $e = preg_replace('/\s*\[ID:\w+\]/i', '', $e);
                        $e = preg_replace('/Fatal error:\s*/i', '', $e);
                        $warningsMsg .= $e . '\n';
                    }
                }

                $qid = WtDB::Ref()->SysrequestInsert(new WtFuncArgs(array(
                    'subject' => 'Book Create Error (Order #' . $order['id'] . ')',
                    'order'   => $order['id'],
                    'body'    => '<p>' . $errorsTxt . "<br />" . $warningsTxt . '</p>',
                    'type'    => 'T',
                    'base'    => 1,
                    'role'    => 6
                )));

                WtDB::Ref()->OrderLogInsert(new WtFuncArgs(array(
                    'oid'     => $order['id'],
                    'action'  => 'order_error',
                    'details' => array('queue_id' => $qid,
                                       'error'    => $errorsTxt . "<br />" . $warningsTxt,
                                       'type'     => 'book')
                )));

                WtDB::Ref()->OrdersUpdate(new WtFuncArgs($order));
                $this->longQueryLog($start, 'GetBookingRequest error');
                return array(
                    'status' => 'error',
                    'result' => $errorsMsg . $warningsMsg
                );
            }

            $order['status']     = 'B';
            $order['book_uid']   = WtAuth::Ref()->id;
            $order['pnr_number'] = $result['OTA_AirBookRS']['AirReservation']['@attributes']['BookingReferenceID'];
            $this->pnrNumber     = $order['pnr_number'];

            WtDB::Ref()->OrdersUpdate(new WtFuncArgs($order));

            //if (strpos($request, 'DirectAccess="true"') === false) { // не SPA
            if ($this->rlS7) {
                $bookId = WtDB::Ref()->OrderBooksValue(new WtMapArgs('pnr', $this->pnrNumber));
                WtDB::Ref()->OrderBooksUpdate(new WtMapArgs('rl_number', serialize(array('S7' => $this->pnrNumber)), 'id', $bookId));
            }
// add queue
            /*            $args   = WtDB::Ref()->ProvidersOfficeRow(new WtMapArgs('pid', $this->provider['pid'], 'type', 'T', 'default', 'Y', 'active', 'Y'));
                        $module = WtDB::Ref()->ProvidersValue(new WtMapArgs('fields', 'ticket_module', 'id', $this->provider['pid']));
                        WtProvider::init(new $module($args));
                        if ( WtProvider::Ref()->Connect() ) {
                            $res = WtProvider::Ref()->RunCommand('ZZDUSZZS7;' . $order['pnr_number'] . 'YYYYY');
                        }
            */
            $this->longQueryLog($start, 'GetBookingRequest');
            return array(
                'status' => 'success',
                'result' => $this->pnrNumber
            );
        } catch (SoapFault $exception) {
            $this->writeException('BookFlight', $exception);
            $this->longQueryLog($start, 'GetBookingRequest exception');
            return false;
        }
    }

    /**
     * Добавление ремарки в бронь
     *
     * @param string $text
     *
     * @return boolean
     */
    public function SetRemarks($text) {
        $start = microtime(true);
        try {
            $this->client->uri = 'http://sita.aero/SITA_AirBookModifyRQ/3/0';
            $reqPayloadString  = $this->POS . '
                <AirBookModifyRQ BookingReferenceID="' . $this->pnrNumber . '" ModificationType="5">
                    <TravelerInfo>
                        <SpecialReqDetails>
                            <Remarks><Remark Operation="Add" RPH="10">' . $text . '</Remark></Remarks>
                        </SpecialReqDetails>
                    </TravelerInfo>
                </AirBookModifyRQ>';

            $this->client->OTA_AirBookModifyRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = $this->client->__getLastResponse();
            $result = str_replace('common:', '', $result);
            $result = XML2Array::createArray($result);
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
            $this->writeLog('OTA_AirBookModifyRQ', 'SetRemarks', $result);
            $this->longQueryLog($start, 'SetRemarks');
            return true;
        } catch (SoapFault $exception) {
            $this->writeException('SetRemarks', $exception);
            $this->longQueryLog($start, 'SetRemarks exception');
            return false;
        }
    }


    public function AddSSR($typeSSR) {
       $start = microtime(true);
       $p                 = $this->passenger;
       $country           = WtDB::Ref()->CountryRow(new WtFuncArgs(array('code'   => $p['doca_country'],
                                                                         'locale' => 'EN')));
       $ssrRPH = $this->ssrRPH();
       $TravelerRefNumberRPHList = $ssrRPH['RPH'];
       $inf = $ssrRPH['inf'];

       if ($TravelerRefNumberRPHList == '') {
           $response['status']  = 'error';
           $response['message'] = 'Not find traveler';
           $this->longQueryLog($start, 'AddSSR Not find traveler');
           return $response;
       }

       switch($typeSSR) {
            case 'DOCA':
                $freeText = '/'.$p['doca_type'].'/'.$country['citizenship_code'].'/'.$p['doca_address'].'/'.$p['doca_city'].'/'.$country['name'].'/'.$p['doca_zip'];
                $freeText = strtoupper($freeText);
               // <Text>/D/FR/12412AQED/PARIS/FRANCE/2135124</Text>
                break;
            case 'DOCO':
                $issue_date = $p['doco_issue_date'];
                $issue_date = strtotime($issue_date);
                $issue_date = date("dMy", $issue_date);
                $freeText = '/'.$p['doco_birth_country'].' '.$p['doco_birth_address'].'/V/'.$p['doco_visa_number'].'/'.$p['doco_issue_place'].' '.$p['doco_issue_country'].'/'.$issue_date.'/'.$p['doco_travel_country'].$inf;
                $freeText = strtoupper($freeText);
               //  <Text>/RU SARATOV/V/14537/MOSCOW/21DEC12/RU</Text>
                break;
            default:
                $freeText = '';
       }

       try {
            $this->client->uri = 'http://sita.aero/SITA_AirBookModifyRQ/3/0';
            $reqPayloadString  = $this->POS . '
                <AirBookModifyRQ BookingReferenceID="' . $this->pnrNumber . '" ModificationType="5">
                    <TravelerInfo>
                        <SpecialReqDetails>
                         <SpecialServiceRequests>
                         <SpecialServiceRequest ServiceQuantity="1" Status="HK" SSRCode="' . $typeSSR . '" TravelerRefNumberRPHList="' .$TravelerRefNumberRPHList. '">
                             <Airline Code="YY" />
                             <Text>' . $freeText . '</Text>
                             </SpecialServiceRequest>
                         </SpecialServiceRequests>
                        </SpecialReqDetails>
                    </TravelerInfo>
                </AirBookModifyRQ>';

            $this->client->OTA_AirBookModifyRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = $this->client->__getLastResponse();
            $result = str_replace('common:', '', $result);
            $result = XML2Array::createArray($result);
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
            $this->writeLog('OTA_AirBookModifyRQ', 'SetRemarks', $result);

            $response = array();

            if (isset ($result['OTA_AirBookRS']['Errors'])) {
                $response['status'] = 'error';
                $response['message'] = $result['OTA_AirBookRS']['Errors']['Error']['@value'].' '.$result['OTA_AirBookRS']['Errors']['Error']['@attributes']['Code'].' '.$result['OTA_AirBookRS']['Errors']['Error']['@attributes']['Type'];
            } else {
                $response['status'] = 'ok';
            }
            $this->longQueryLog($start, 'AddSSR');
            return $response;

       } catch (Exception $exception) {
            $this->writeException('SetRemarks', $exception);
            $this->longQueryLog($start, 'AddSSR exception');
            return false;
       }
    }


    public function deleteExistSSR($typeSSR) {
        $start = microtime(true);
        $p = $this->passenger;
        $country = WtDB::Ref()->CountryRow(new WtFuncArgs(array('code' => $p['doca_country'],
                                                                'locale' => 'EN')));
        $ssrRPH = $this->ssrRPH();
        $TravelerRefNumberRPHList = $ssrRPH['RPH'];
        $inf = $ssrRPH['inf'];

        if ($TravelerRefNumberRPHList == '') {
            $response['status'] = 'error';
            $response['message'] = 'Not find traveler';
            $this->longQueryLog($start, 'deleteExistSSR Not find traveler');
            return $response;
        }

        switch($typeSSR) {
            case 'DOCA':
                $freeText = $p['doca_type'].'/'.$country['citizenship_code'].'/'.$p['doca_address'].'/'.$p['doca_city'].'/'.$country['name'].'/'.$p['doca_zip'];
                $freeText = strtoupper($freeText);
                // <Text>/D/FR/12412AQED/PARIS/FRANCE/2135124</Text>
                break;
            case 'DOCO':
                $issue_date = $p['doco_issue_date'];
                $issue_date = strtotime($issue_date);
                $issue_date = date("dMy", $issue_date);
                $freeText = $p['doco_birth_country'].' '.$p['doco_birth_address'].'/V/'.$p['doco_visa_number'].'/'.$p['doco_issue_place'].' '.$p['doco_issue_country'].'/'.$issue_date.'/'.$p['doco_travel_country'].$inf;
                $freeText = strtoupper($freeText);
                //  <Text>/RU SARATOV/V/14537/MOSCOW/21DEC12/RU</Text>
                break;
            default:
                $freeText = '';
        }

        try {
            $this->client->uri = 'http://sita.aero/SITA_AirBookModifyRQ/3/0';
            $reqPayloadString  = $this->POS . '
            <AirBookModifyRQ BookingReferenceID="' . $this->pnrNumber . '" ModificationType="5">
                <TravelerInfo>
                  <SpecialReqDetails>
                    <SpecialServiceRequests>
                      <SpecialServiceRequest SSRCode="' . $typeSSR . '" ServiceQuantity="1" Status="16" TravelerRefNumberRPHList="' .$TravelerRefNumberRPHList. '">
                        <Airline Code="S7" />
                        <Text>' . $freeText . '</Text>
                      </SpecialServiceRequest>
                    </SpecialServiceRequests>
                  </SpecialReqDetails>
                </TravelerInfo>
            </AirBookModifyRQ>';

            $this->client->OTA_AirBookModifyRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = $this->client->__getLastResponse();
            $result = str_replace('common:', '', $result);
            $result = XML2Array::createArray($result);
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
            $this->writeLog('OTA_AirBookModifyRQ', 'SetRemarks', $result);
            $response = array();

            if (isset ($result['OTA_AirBookRS']['Errors'])) {
                $response['status'] = 'error';
                $response['message'] = $result['OTA_AirBookRS']['Errors']['Error']['@value'].' '.$result['OTA_AirBookRS']['Errors']['Error']['@attributes']['Code'].' '.$result['OTA_AirBookRS']['Errors']['Error']['@attributes']['Type'];
            } else {
                $response['status'] = 'ok';
            }
            $this->longQueryLog($start, 'deleteExistSSR');
            return $response;

        } catch (SoapFault $exception) {
            $this->writeException('SetRemarks', $exception);
            $this->longQueryLog($start, 'deleteExistSSR exception');
            return false;
        }
    }

    private function ssrRPH() {
        $start = microtime(true);
        $p                 = $this->passenger;
        $inf               = '';
        $this->client->uri = 'http://sita.aero/SITA_ReadRQ/3/1';
        $reqPayloadString  = $this->POS . '<UniqueID Type="0" ID="' . $this->pnrNumber . '"/>';
        $this->client->OTA_ReadRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
        $result            = $this->client->__getLastResponse();
        $result            = str_replace('common:', '', $result);
        $result            = XML2Array::createArray($result);
        $result            = isset($result['SOAP-ENV:Envelope']['SOAP-ENV:Body']) ? $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'] : $result['Envelope']['Body'];

        if (isset($result['OTA_AirBookRS']['AirReservation']['TravelerInfo']['AirTraveler'])) {
            $travelerInfo = $result['OTA_AirBookRS']['AirReservation']['TravelerInfo']['AirTraveler'];
        } else {
            $response['status']  = 'error';
            $response['message'] = 'Error OTA_ReadRQ, can`t find TravelerInfo';
            $this->longQueryLog($start, 'AddSSR can`t find TravelerInfo');
            return $response;
        }

        $TravelerRefNumberRPHList = '';
        $firstname                = strtoupper($p['firstname']);
        $lastname                 = strtoupper($p['lastname']);

        if (isset ($travelerInfo['PersonName'])) {
            if ($p['type'] == 'INF') {
                $infTravelerInfo = $travelerInfo['SpecialReqDetails']['OtherServiceInformations']['OtherServiceInformation']['Text'];
                $infTravelerInfo = explode(' ', $infTravelerInfo);
                $infTravelerInfo = explode('/', $infTravelerInfo[1]);
                if ($firstname == $infTravelerInfo[1] and $lastname == $infTravelerInfo[0]) {
                    $TravelerRefNumberRPHList = $travelerInfo['TravelerRefNumber']['@attributes']['RPH'];
                    $inf                      = '/I';
                }
            } else {
                if ($firstname == $travelerInfo['PersonName']['GivenName'] and $lastname == $travelerInfo['PersonName']['Surname']) {
                    $TravelerRefNumberRPHList = $travelerInfo['TravelerRefNumber']['@attributes']['RPH'];
                }
            }

        } else {

            foreach ($travelerInfo as $traveler) {
                if ($p['type'] == 'INF') {
                    $infTravelerInfo = $traveler['SpecialReqDetails']['OtherServiceInformations']['OtherServiceInformation']['Text'];
                    $infTravelerInfo = explode(' ', $infTravelerInfo);
                    $infTravelerInfo = explode('/', $infTravelerInfo[1]);
                    if ($firstname == $infTravelerInfo[1] and $lastname == $infTravelerInfo[0]) {
                        $TravelerRefNumberRPHList = $traveler['TravelerRefNumber']['@attributes']['RPH'];
                        $inf                      = '/I';
                    }
                } else {
                    if ($firstname == $traveler['PersonName']['GivenName'] and $lastname == $traveler['PersonName']['Surname']) {
                        $TravelerRefNumberRPHList = $traveler['TravelerRefNumber']['@attributes']['RPH'];
                    }
                }
            }
        }

        $this->longQueryLog($start, 'ssrRPH');

        return array('RPH' => $TravelerRefNumberRPHList, 'inf' => $inf);

    }

    public function FlightSeatMap($args) {

        $start = microtime(true);
        $return_result = array();
        try {
            $this->client->uri = 'http://sita.aero/SITA_AirSeatMapRQ/3/0';
            $reqPayloadString  = $this->FlightSeatMapPOS . '
                <SeatMapRequests>
                  <SeatMapRequest>
                     <FlightSegmentInfo DepartureDateTime="' . $args["departure_date"] . 'T' . $args["departure_time"] . '" FlightNumber="' . $args["flight_number"] . '">
                         <DepartureAirport LocationCode="' . $args["departure_airport"] . '"/>
                         <ArrivalAirport LocationCode="' . $args["arrival_airport"] . '"/>
                         <MarketingAirline Code="' . $args["marketing_airline"] . '"/>
                     </FlightSegmentInfo>
                      <SeatDetails>
                        <CabinClass CabinType="' . $args["class_service"] . '"/>
                      </SeatDetails>
                   </SeatMapRequest>
                </SeatMapRequests>';

            $this->client->OTA_AirSeatMapRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = $this->client->__getLastResponse();
            $result = str_replace('common:', '', $result);
            $result = XML2Array::createArray($result);
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body']['OTA_AirSeatMapRS'];
            $this->writeLog('OTA_AirSeatMapRQ', 'AirSeatMap', $result);

            if (!isset($result['Success'])) {
                $this->longQueryLog($start, 'FlightSeatMap no Success');
                throw new WtGabrielSearchException('OTA_AirSeatMapRS', 'SITA Error response');
            }

            $result = $result['SeatMapResponses']['SeatMapResponse'];
            $AirRows = $result['SeatMapDetails']['CabinClass']['AirRows']['AirRow'];

            $result_columns = array();
            $i = (int)$AirRows[0]['@attributes']['RowNumber'];

            foreach ($AirRows as $row) {

                $RowNumber = (int)$row['@attributes']['RowNumber'];
                $cols = $row['AirSeats']['AirSeat'];

                $rowsA[$i]['number'] = $RowNumber;
                $rowsA[$i]['occupation'] = $cols[0]['@attributes']['SeatAvailability'] == 1 ? 'N' : 'Y';
                $rowsA[$i]['seat'] = FuncConfig::GabrielCharacteristics($cols[0]['@attributes']['SeatCharacteristics']);

                $rowsB[$i]['number'] = $RowNumber;
                $rowsB[$i]['occupation'] = $cols[1]['@attributes']['SeatAvailability'] == 1 ? 'N' : 'Y';
                $rowsB[$i]['seat'] = FuncConfig::GabrielCharacteristics($cols[1]['@attributes']['SeatCharacteristics']);

                $rowsC[$i]['number'] = $RowNumber;
                $rowsC[$i]['occupation'] = $cols[2]['@attributes']['SeatAvailability'] == 1 ? 'N' : 'Y';
                $rowsC[$i]['seat'] = FuncConfig::GabrielCharacteristics($cols[2]['@attributes']['SeatCharacteristics']);

                $rowsD[$i]['number'] = $RowNumber;
                $rowsD[$i]['occupation'] = $cols[3]['@attributes']['SeatAvailability'] == 1 ? 'N' : 'Y';
                $rowsD[$i]['seat'] = FuncConfig::GabrielCharacteristics($cols[3]['@attributes']['SeatCharacteristics']);

                $rowsE[$i]['number'] = $RowNumber;
                $rowsE[$i]['occupation'] = $cols[4]['@attributes']['SeatAvailability'] == 1 ? 'N' : 'Y';
                $rowsE[$i]['seat'] = FuncConfig::GabrielCharacteristics($cols[4]['@attributes']['SeatCharacteristics']);

                $rowsF[$i]['number'] = $RowNumber;
                $rowsF[$i]['occupation'] = $cols[5]['@attributes']['SeatAvailability'] == 1 ? 'N' : 'Y';
                $rowsF[$i]['seat'] = FuncConfig::GabrielCharacteristics($cols[5]['@attributes']['SeatCharacteristics']);

             $i++;
            }

            $result_columns['A']['key'] = '0';
            $result_columns['A']['name'] = 'A';
            $result_columns['A']['side'] = 'left';
            $result_columns['A']['rows'] = $rowsA;

            $result_columns['B']['key'] = '0';
            $result_columns['B']['name'] = 'B';
            $result_columns['B']['side'] = 'left';
            $result_columns['B']['rows'] = $rowsB;

            $result_columns['C']['key'] = '0';
            $result_columns['C']['name'] = 'C';
            $result_columns['C']['side'] = 'left';
            $result_columns['C']['rows'] = $rowsC;

            $result_columns['D']['key'] = '0';
            $result_columns['D']['name'] = 'D';
            $result_columns['D']['side'] = 'right';
            $result_columns['D']['rows'] = $rowsD;

            $result_columns['E']['key'] = '0';
            $result_columns['E']['name'] = 'E';
            $result_columns['E']['side'] = 'right';
            $result_columns['E']['rows'] = $rowsE;

            $result_columns['F']['key'] = '0';
            $result_columns['F']['name'] = 'F';
            $result_columns['F']['side'] = 'right';
            $result_columns['F']['rows'] = $rowsF;


            $return_result['cabins']['eco']['cabin_class'] = 'eco';
            $return_result['cabins']['eco']['occupation_default'] = '';
            $return_result['cabins']['eco']['min_row_number'] = (int)$AirRows[0]['@attributes']['RowNumber'];
            $return_result['cabins']['eco']['max_row_number'] = (int)$AirRows[count($AirRows)-1]['@attributes']['RowNumber'];
            $return_result['cabins']['eco']['count_rows'] = count($AirRows);
            $return_result['cabins']['eco']['cabin_location'] = '';
            $return_result['cabins']['eco']['start_overwing'] = '9';
            $return_result['cabins']['eco']['end_overwing'] = '15';

            $return_result['cabins']['eco']['columns'] = $result_columns;

        if ($this->log) {
                TicketLog::write($return_result, 'Result:', 'FlightSeatMap');
            }
            $this->longQueryLog($start, 'FlightSeatMap');
            return $return_result;
        } catch (SoapFault $exception) {
            $this->writeException('FlightSeatMap', $exception);
            $this->longQueryLog($start, 'FlightSeatMap exception');
            return false;
        }

    }

    private function longQueryLog($start, $funcName, $time = 0) {
        if ($this->longQueryCatch) {
            $var = number_format(microtime(true)- $start, 4);
            $maxTime = ($time == 0) ? $this->longQueryTime : $time;
            if ($var > $maxTime) {
                $args = 'method = '.$funcName.'; time = '.$var.'; ';
                $args .= (isset($this->orderId))          ? 'orderId = '.$this->orderId.'; '                   : '';
                $args .= (isset($this->providerId))       ? 'providerId = '.$this->providerId.'; '             : '';
                $args .= (isset($this->officeid))         ? 'officeid = '.$this->officeid.'; '                 : '';
                $args .= (isset($this->pnrNumber) && $this->pnrNumber != '') ? 'pnrNumber = '.$this->pnrNumber.'; ' : '';

                if (isset ($this->order) && !empty($this->order)) {
                    $args .= 'request_id = '.$this->order['request_id'].'; ';
                    $args .= 'transaction_id = '.$this->order['transaction_id'].'; ';
                    $args .= 'provider_id = '.$this->order['provider_id'].'; ';
                    $args .= 'user_id = '.$this->order['user_id'].'; ';
                    $args .= 'route = '.$this->order['route'].'; ';
                }
                dumpLog($args, 'GabrielSearch', 'gabriel_long_query.log');
            }
        }
    }


}
