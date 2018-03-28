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



class WtGabrielTicket extends BaseProvider {

    /**
     * XML SOAP client
     * @var WtGabrielDriver
     */
    private $client;

    protected $currency;
    public $GDS = 'GABRIEL';
    private $Office;
    private $CountryCode;
    private $CityCode;
    private $GroupCode = 115;
    private $AirlineID = 'S7';
    private $RequestorID;

    private $login;
    private $password;
    private $agency;

    private $POS;
    private $TicketPOS;

    protected $couponsInfo;

    private $log   = true;
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

    public function __construct($args) {
        $start = microtime(true);
        $args = parent::__construct($args);

        $this->login       = $args['login'];
        $this->password    = $args['password'];
        $this->agency      = $args['agency'];
        $this->currency    = $args['currency'];
        $this->Office      = $args['officeid'];
        $this->CountryCode = $args['country'];
        $this->CityCode    = substr($this->Office, 0, 3);

        switch($this->Office) {
            case 'OMS901':
                $this->RequestorID = '42130126';
                $this->CityCode = 'OMS'; // это не нужно смотри строку 79 !!!
                break;

            case 'WWW802':
                $this->RequestorID = '42165152';
                $this->CityCode = 'OVB';
                break;

            case 'FRU902':
                $this->RequestorID = '42130303';
                $this->CityCode = 'NYC';
                break;

            case 'TJM900':
                $this->RequestorID = '42168302';
                break;

            case 'KEJ901':
                $this->RequestorID = '42180471';
                break;

            case 'DYU901':
                $this->RequestorID = '42134540';
                $this->CityCode = 'NYC';
                break;

            case 'KZN901':
                $this->RequestorID = '42132871';
                break;

            case 'SIP901':
                $this->RequestorID = '42115566';
                $this->CityCode = 'OVB';
                break;
            case 'BAK901':
                $this->RequestorID = '42154276';
                $this->CityCode = 'BAK'; // это не нужно смотри строку 79 !!!
                break;
            case 'KRR901':
               // $this->RequestorID = '42110062';
                $this->RequestorID = '42185054';
                $this->CityCode = 'KRR'; // это не нужно смотри строку 79 !!!
                break;
            case 'EVN901':
                $this->RequestorID = '42120643';
                $this->CityCode = 'EVN'; // это не нужно смотри строку 79 !!!
                break;
        }

        $sourceAttr = 'ERSP_UserID="' . $this->login . '/' . $this->password .
                      '" AgentSine="' . $this->agency .
                      '" PseudoCityCode="' . $this->Office .
                      '" AgentDutyCode="' . $this->GroupCode .
                      '" ISOCountry="' . $this->CountryCode .
                      '" AirlineVendorID="' . $this->AirlineID .
                      '" AirportCode="' . $this->CityCode . '"';
        $this->POS = '<POS><Source ' . $sourceAttr . '/></POS>';
        $this->TicketPOS = '<POS><Source ' . $sourceAttr . '><RequestorID Type="5" ID="'.$this->RequestorID.'"/></Source></POS>';

        $this->client = new WtGabrielDriver(null, array(
            'soap_version'   => SOAP_1_1,
//            'location'     => 'https://sws.qa.sita.aero/sws/', // test url
            'location'       => 'https://sws.sita.aero/sws/',
            'uri'            => 'http://www.opentravel.org/OTA/2003/05',
            'stream_context' => stream_context_create(array('ssl' => array('ciphers'=>'RC4-SHA'))),
            'trace'          => 1 // need for $this->client->__getLastResponse() !!!!!!!
        ));

        define('SITA_PRIVATE_KEY', SITA_KEYS_PATH . $this->Office . '.key.pem');
        define('SITA_CERT_FILE',   SITA_KEYS_PATH . $this->Office . '.cert.pem');

        if ($this->log) {
            TicketLog::Ref()->set_log_file(WtSession::Ref()->sid());
        }
        $this->longQueryLog($start, '__construct');
    }

    /**
     * Write request and response to log file
     * @param string $actionName
     * @param string $functionName
     * @param array $result
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
                'area'    => 'ticketing',
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

    /**
     * Write exception request and response to log file
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
                'area'    => 'ticketing',
                'oid'     => $this->orderId,
                'action'  => 'exception',
                'details' => array(
                    'function' => $functionName,
                    'request'  => $this->client->__getLastRequest(),
                    'response' => $this->client->__getLastResponse(),
                    'exception' => $exception
                )
            )));
        }
        $this->longQueryLog($start, 'writeException');
    }

    function Connect() {
        $start = microtime(true);
        $this->longQueryLog($start, 'Connect');
        return isset($this->client);
    }
/*
    function SignIn() {
        try {
            $this->client->uri = 'http://www.opentravel.org/OTA/2003/05';
            $reqPayloadString = $this->POS;
            $result = $this->client->SITA_SignInRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = XML2Array::createArray($this->client->__getLastResponse());
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];

            return isset($result['SITA_SignInRS']['Success']) ? $result['SITA_SignInRS']['pid'] : false;

        } catch (SoapFault $fault) {
            dump($fault);
            return false;
        }
    }

    function SignOut($pid) {
        try {
            $this->client->uri = 'http://www.opentravel.org/OTA/2003/05';
            $reqPayloadString = $this->POS . '<pid>' . $pid . '</pid>';
            $result = $this->client->SITA_SignOutRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = XML2Array::createArray($this->client->__getLastResponse());
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];

            return isset($result['SITA_SignOutRS']['Success']) ? $result['SITA_SignOutRS'] : false;

        } catch (SoapFault $fault) {
            dump($fault);
            return false;
        }
    }
*/
    /**
     * Выполнение терминальной команды
     * В габриеле нельзя выполнить несколько команд в пакете, т.к. не работает режим сессий и функции SignIn и SignOut!!!
     * @param string $command
     * @param boolean $log
     * @return array|boolean
     */
    function RunCommand($command, $log=true) {
        $start = microtime(true);
        if ($log) TicketLog::write($command, "command", 'RunCommand');

        try {
            $command = str_replace(';',"\r",$command);
            $response = array();

            $transactionIdentifier = $this->getTransactionIdentifier();
            $this->client->uri = 'http://sita.aero/SITA_ScreenTextRQ/3/0';
            $reqPayloadString = $this->POS . '<ScreenEntry>' . $command . '</ScreenEntry>';

            $this->client->actionAttr['OTA_ScreenTextRQ'] = array(
//              'EchoToken' => WtSession::Ref()->sid(),
                'QuantityGroup'         => '2',
//              'OmitBlankLinesIndicator' => 'true',
//              'MergeScreenIndicator'  => 'true'
            );

            if ($transactionIdentifier) {
                $this->client->actionAttr['OTA_ScreenTextRQ']['TransactionIdentifier'] = $transactionIdentifier;
            } else {
                $this->longQueryLog($start, 'RunCommand no transactionIdentifier');
                return false;
            }

            for ($i = 0; $i < 10; $i++) {

                if ($i != 0) {
                    $reqPayloadString = $this->POS . '<ScreenEntry>PN</ScreenEntry>';
                }

                $this->client->OTA_ScreenTextRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
                $result = $this->client->__getLastResponse();

                $result = str_replace('common:', '',$result);
                $result = XML2Array::createArray($result);

                $result = isset($result['SOAP-ENV:Envelope']['SOAP-ENV:Body']) ? $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'] : $result['Envelope']['Body'];
                $this->writeLog('OTA_ScreenTextRQ', 'RunCommand', $result);

                $text = is_array($result['OTA_ScreenTextRS']['TextScreens']['TextScreen']['TextData']) ? $result['OTA_ScreenTextRS']['TextScreens']['TextScreen']['TextData'] : array($result['OTA_ScreenTextRS']['TextScreens']['TextScreen']['TextData']);

                if (end($text) == end($response)) {
                    break;
                }

                foreach($text as $line) {
                    $response[] = $line;
                }

            }

            if (!empty($response)) {
                $this->longQueryLog($start, 'RunCommand');
                return array(
                    'output'  => implode("\r", $response),
                    'session' => ''
                );
            }
            $this->longQueryLog($start, 'RunCommand no Success');
            return false;

        } catch (SoapFault $exception) {
            $this->writeException('RunCommand', $exception);
            $this->longQueryLog($start, 'RunCommand exception');
            return false;
        }
    }

    private function getTransactionIdentifier() {
        $start = microtime(true);
        try {
            $this->client->uri = 'http://sita.aero/SITA_SignInRQ/3/0';
            $reqPayloadString = $this->POS;
            $this->client->SITA_SignInRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = $this->client->__getLastResponse();
            $result = str_replace('common:', '',$result);
            $result = XML2Array::createArray($result);
            $result = isset($result['SOAP-ENV:Envelope']['SOAP-ENV:Body']) ? $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'] : $result['Envelope']['Body'];
            if ($result['SITA_SignInRS']['pid']) {
                $response = $result['SITA_SignInRS']['pid'];
            } else {
                $response = false;
            }
            $this->longQueryLog($start, 'getTransactionIdentifier');
            return $response;

        } catch (SoapFault $exception) {
            $this->writeException('getTransactionIdentifier', $exception);
            $this->longQueryLog($start, 'getTransactionIdentifier exception');
            return false;
        }
    }

    /**
     * Получение информации о брони
     * @see BaseProvider::GetBook()
     */
    function GetBook($log = false, $extended = true) {
        $start = microtime(true);
        try {
            $this->PNR = array();
            $this->client->uri = 'http://sita.aero/SITA_ReadRQ/3/1';
            $reqPayloadString = $this->POS . '<UniqueID Type="0" ID="' . $this->pnrNumber . '"/>';
            $this->client->OTA_ReadRQ(new SoapVar($reqPayloadString, XSD_ANYXML));

             $result = $this->client->__getLastResponse();
             $result = str_replace('common:', '',$result);
             $result = XML2Array::createArray($result);

            $result = isset($result['SOAP-ENV:Envelope']['SOAP-ENV:Body']) ? $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'] : $result['Envelope']['Body'];
            $this->writeLog('OTA_ReadRQ', 'GetBook', $result);

            if (isset($result['OTA_AirBookRS']['Errors'])) {
                $this->PNR_text = $result['OTA_AirBookRS']['Errors']['Error']['@value'];
                $this->longQueryLog($start, 'GetBook Error');
                return false;
            }
            if (isset($result['OTA_AirBookRS']['Success'])) {
                $PNR_text = $this->RunCommand('RT:' . $this->pnrNumber);
                $this->PNR_text = $PNR_text['output'];

                $segments = $result['OTA_AirBookRS']['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'];
                if (isset($segments['FlightSegment'])) {
                    $segments = array($segments);
                }
                foreach($segments as $segment) {
                    $this->PNR['coupons'][] = array(
                        'coupon_number'     => $segment['FlightSegment']['@attributes']['RPH'],
                        'departure_airport' => $segment['FlightSegment']['DepartureAirport']['@attributes']['LocationCode'],
                        'arrival_airport'   => $segment['FlightSegment']['ArrivalAirport']['@attributes']['LocationCode'],
                        'departure_datetime'=> str_replace('T', ' ', $segment['FlightSegment']['@attributes']['DepartureDateTime']),
                        'arrival_datetime'  => str_replace('T', ' ', $segment['FlightSegment']['@attributes']['ArrivalDateTime']),
                        'airline'           => $segment['FlightSegment']['OperatingAirline']['@attributes']['Code'],
                        'flight_number'     => $segment['FlightSegment']['@attributes']['FlightNumber'],
                        'booking_class'     => $segment['FlightSegment']['BookingClassAvails']['BookingClassAvail']['@attributes']['ResBookDesigCode'],
                        'fare_basis'        => '',
                        'status'            => $segment['FlightSegment']['@attributes']['Status'] == 'HK' ? 'OPEN' : $segment['FlightSegment']['@attributes']['Status']
                    );
                }

                $passengers = $result['OTA_AirBookRS']['AirReservation']['TravelerInfo']['AirTraveler'];

                if (isset($passengers['PersonName'])) {
                    $passengers = array($passengers);
                }

                foreach($passengers as $passenger) {
                    $pax = array(
                        'number'    => $passenger['TravelerRefNumber']['@attributes']['RPH'],
                        'type'      => $passenger['@attributes']['PassengerTypeCode'],
                        'title'     => $passenger['PersonName']['NamePrefix'],
                        'firstname' => $passenger['PersonName']['GivenName'],
                        'lastname'  => $passenger['PersonName']['Surname'],
                        'birthdate' => $passenger['@attributes']['BirthDate'],
//                      'email'     => $passenger['Email'],
//                      'phone'     => $passenger['Telephone']['@attributes']['PhoneNumber'],
//                      'address'   => $passenger['Address']['AddressLine']
                    );
                    if (isset($passenger['PersonName']['MiddleName']) && !empty($passenger['PersonName']['MiddleName'])) {
                        if (is_array($passenger['PersonName']['MiddleName'])) {
                            foreach ($passenger['PersonName']['MiddleName'] as $m) {
                                $pax['firstname'] .= ' ' . $m;
                            }
                        } else {
                            $pax['firstname'] .= ' ' . $passenger['PersonName']['MiddleName'];
                        }
                    }

                    $this->PNR['passengers'][] = $pax;
                }

                $this->longQueryLog($start, 'GetBook');
                $this->PNR['pnr_number'] = $this->pnrNumber;
            }
        } catch (SoapFault $exception) {
            $this->writeException('GetBook', $exception);
            $this->longQueryLog($start, 'GetBook exception');
            return false;
        }
    }

    /**
     * Get fare quotation for external PNR
     * @param boolean $bestBuy if true get fare quotation with best buy
     * @return array|boolean
     */
    public function GetFare($bestBuy = true, $withRebook = false) {
        $start = microtime(true);
        $this->longQueryLog($start, 'GetFare');
        // заглушка, т.к. в габриеле нет возможности оценить внешнюю бронь!!!
        return array('status' => 'ok', 'request' => array('provider_id' => $this->providerId));
    }

    /**
     * Get fare quotation by PNR
     * @param boolean $bestBuy if true get fare quotation with best buy
     * @return array
     * @access public
     */
    public function GetFareQuotation($bestBuy = true) {
        $start = microtime(true);
        $this->longQueryLog($start, 'GetFareQuotation');
        // заглушка, т.к. в габриеле нет возможности переоценки брони!!!
        $Response['pnr'] = $this->PNR;
        return $Response;
    }

    /**
     * Выписка билетов брони
     * @return array
     * @access public
     */
    public function TicketingBook() {
        $start = microtime(true);
        $this->initialize();

        $logParams = array_intersect_key($this->order, array_fill_keys(array('id','type','provider_id','transaction_currency','customer_currency','class','payment_method','base_fare','tax_fare','fee','total_fare'),1));
        TicketLog::write($logParams, 'start ticketing with params:', 'Ticketing');

        try {
            $statusLock = 'T';

            $this->GetBook();

            if (!WtDB::Ref()->OrderLockRow(new WtMapArgs('order_id', $this->order['id']))) { // Как оказалось не всегда может пройти выписка нормально и внестись в базу, начинаем делать сообшение если чтото пошло не так.
               WtDB::Ref()->OrderLockInsert(new WtMapArgs('order_id', $this->order['id'], 'status', $statusLock)); // Отдаем ошибку на интерфейс по умолчанию на случай если завалится по пути.
            }

            $this->Tickets = $this->order['passengers'];
            $this->PNR['segments'] = WtDB::Ref()->TransactionsResultRows(new WtFuncArgs(array(
                'fields'  => 'first_booking_class, message',
                'orderid' => $this->order['id'],
                'order'   => 'direction_ind'
            )));

            $countInfant = 0;
            $return_result = array();
            $error = array();
            $forLog = array();

            foreach($this->Tickets as $ticket) {
                $ticket['type'] = $ticket['type'] == 'ААТ' ? 'ADT' : $ticket['type']; // если было предложение из Сирены

                foreach($this->PNR['passengers'] as $p) {

                    if ($p['lastname'] == $ticket['lastname'] && $p['firstname'] == $ticket['firstname'] && $p['type'] == $ticket['type']) {
                        $RPH = $p['number'];
                    }
                }

                if ($ticket['type'] == 'INF') {
                    $countInfant++;
                }
                $TPA_Extensions = '';
                foreach($this->PNR['segments'] as $segment) {
                    $info = unserialize($segment['message']);
                    $this->couponsInfo = $info['AdditionalSegmentInfos'];

                    $AdditionalSegmentInfos = $TicketDesignators = '';
                    foreach($info as $key => $passenger) {
                        $price = $passenger['ota:AirItineraryPricingInfo']['ota:PTC_FareBreakdowns']['ota:PTC_FareBreakdown'];
                        switch($price['ota:PassengerTypeQuantity']['@attributes']['Code']) {
                            case 'ADT': $prefix = 'adult';
                                        $Passengers = '<PassengerName PassengerTypeCode="ADT" RPH="' . $RPH . '"/>';
                                        $ticket_type = 'ADT';
                                        break;
                            case 'CNN': $prefix = 'child';
                                        $Passengers = '<PassengerName PassengerTypeCode="CNN" RPH="' . $RPH . '"/>';
                                        $ticket_type = 'CHD';
                                        break;
                            case 'INF': $prefix = 'infant';
                                        $Passengers = '<PassengerName PassengerTypeCode="INF" RPH="' . $countInfant . '"/>';
                                        $ticket_type = 'INF';
                                        break;
                        }

                        if ($ticket['type'] != $ticket_type) continue;

                        if (isset($price['ota:TicketDesignators'])) {
                            $TicketDesignators = '<TicketDesignators>';
                            $Designators = isset($price['ota:TicketDesignators']['ota:TicketDesignator']['@attributes']) ? array($price['ota:TicketDesignators']['ota:TicketDesignator']) : $price['ota:TicketDesignators']['ota:TicketDesignator'];
                            foreach($Designators as $designator) {
                                $TicketDesignators .= '<TicketDesignator TicketDesignatorCode="' . $designator['@attributes']['TicketDesignatorCode'] . '" TicketDesignatorExtension="' . $designator['@attributes']['TicketDesignatorExtension'] . '" FlightRefRPH="' . $designator['@attributes']['FlightRefRPH'] . '"/>';
                            }
                            $TicketDesignators .= '</TicketDesignators>';
                        }

                        $Taxes = '';
                        if (isset($price['ota:PassengerFare']['ota:Taxes'])) {
                            $ticketTaxes = array();
                            $taxList = isset($price['ota:PassengerFare']['ota:Taxes']['ota:Tax']['@value']) ? array($price['ota:PassengerFare']['ota:Taxes']['ota:Tax']) : $price['ota:PassengerFare']['ota:Taxes']['ota:Tax'];
                            foreach($taxList as $tax) {
                                $Taxes .= '<Tax Amount="' . $tax['@attributes']['Amount'] . '" CurrencyCode="' . $tax['@attributes']['CurrencyCode'] . '" TaxCode="' . $tax['@attributes']['TaxCode'] . '"/>';
                                $ticketTaxes[] = array(
                                    'type' => $tax['@attributes']['TaxCode'],
                                    'rate' => $tax['@attributes']['Amount'],
                                    'currencyCode' => $tax['@attributes']['CurrencyCode']
                                );
                            }
                            $ticketId = WtDB::Ref()->OrderTicketsValue(new WtMapArgs('orderid', $ticket['order_id'], 'passid', $ticket['id']));
                            WtDB::Ref()->OrderTicketsUpdate(new WtMapArgs('taxes', serialize($ticketTaxes), 'id', $ticketId));
                        }
                        break;
                    }

                    $FareBasisCodes = '';
                    foreach($this->couponsInfo[$ticket_type] as $leg) {
                        $FareBasisCodes .= '<FareBasisCode FlightSegmentRPH="' . $leg['SegmentRPH'] . '">' . $leg['FareBasis'] . '</FareBasisCode>';
                    }


//                    $EquivFare = '<EquivFare Amount="' . $price['ota:PassengerFare']['ota:TotalFare']['@attributes']['Amount'] . '" CurrencyCode="' . $price['ota:PassengerFare']['ota:TotalFare']['@attributes']['CurrencyCode'] . '"/>';
                    $EquivFare = '';
                    if (isset($price['ota:PassengerFare']['ota:EquivFare']['@attributes']['Amount'])) {
                        $EquivFare = '<EquivFare Amount="' . $price['ota:PassengerFare']['ota:EquivFare']['@attributes']['Amount'] . '" CurrencyCode="' . $price['ota:PassengerFare']['ota:EquivFare']['@attributes']['CurrencyCode'] . '"/>';
                    }

                    $FareInfoExtension = '';
                    $FareInfos = isset($passenger['ota:AirItineraryPricingInfo']['ota:FareInfos']['ota:FareInfo']['ota:TPA_Extensions']) ? array($passenger['ota:AirItineraryPricingInfo']['ota:FareInfos']['ota:FareInfo']) : $passenger['ota:AirItineraryPricingInfo']['ota:FareInfos']['ota:FareInfo'];
                    foreach($FareInfos as $FareInfo) {
                          $FareInfoExtension .= '<FareInfo><SITA_FareInfoExtension FareRPH="' . $FareInfo['ota:TPA_Extensions']['sita:SITA_FareInfoExtension']['@attributes']['FareRPH'] . '" RuleNumber="' . $FareInfo['ota:TPA_Extensions']['sita:SITA_FareInfoExtension']['@attributes']['RuleNumber'] . '" TariffNumber="' . $FareInfo['ota:TPA_Extensions']['sita:SITA_FareInfoExtension']['@attributes']['TariffNumber'] . '">' .
                            '<SubjectToGovtApproval>false</SubjectToGovtApproval>' .
                             '<References>' .
                                 '<Ref1>' . htmlentities($FareInfo['ota:TPA_Extensions']['sita:SITA_FareInfoExtension']['sita:References']['sita:Ref1']) . '</Ref1>' .
                                 '<Ref2>' . htmlentities($FareInfo['ota:TPA_Extensions']['sita:SITA_FareInfoExtension']['sita:References']['sita:Ref2']) . '</Ref2>' .
                             '</References>' .
                             '<Directionality Code="' . $FareInfo['ota:TPA_Extensions']['sita:SITA_FareInfoExtension']['sita:Directionality']['@attributes']['Code'] . '"/>' .
                          '</SITA_FareInfoExtension></FareInfo>';
                    }

                    $TPA_Extensions .= '<PTC_FareBreakdown PricingSource="' . $price['@attributes']['PricingSource'] . '">' .
                        '<PassengerTypeQuantity Code="' . $price['ota:PassengerTypeQuantity']['@attributes']['Code'] . '"/>' .
                        '<FareBasisCodes>' . $FareBasisCodes . '</FareBasisCodes>' .
                        '<PassengerFare>' .
                            '<BaseFare Amount="' . $price['ota:PassengerFare']['ota:BaseFare']['@attributes']['Amount'] . '" CurrencyCode="' . $price['ota:PassengerFare']['ota:BaseFare']['@attributes']['CurrencyCode'] . '"/>' . $EquivFare .
                            '<Taxes>' . $Taxes . '</Taxes>' .
                            '<TotalFare Amount="' . $price['ota:PassengerFare']['ota:TotalFare']['@attributes']['Amount'] . '" CurrencyCode="' . $price['ota:PassengerFare']['ota:TotalFare']['@attributes']['CurrencyCode'] . '"/>' .
                            '<UnstructuredFareCalc>' . $price['ota:PassengerFare']['ota:UnstructuredFareCalc'] . '</UnstructuredFareCalc>' .
                            '<TPA_Extensions><FareInfos>' . $FareInfoExtension . '</FareInfos></TPA_Extensions>' .
                        '</PassengerFare>' .
                        $TicketDesignators .
                    '</PTC_FareBreakdown>';

                    $segBaggage = $info['Baggage'][$ticket_type];
                    foreach ($segBaggage as $key => $baggage) {
                        $AdditionalSegmentInfos .= '<AdditionalSegmentInfo SegmentRPH="' . $key .
                            '" StopoverPermitted="' . $info['AdditionalSegmentInfos'][$ticket_type][$key]['StopoverPermitted'] .
                            '" NotValidBefore="' . $info['AdditionalSegmentInfos'][$ticket_type][$key]['NotValidBefore'] .
                            '" NotValidAfter="' . $info['AdditionalSegmentInfos'][$ticket_type][$key]['NotValidAfter'] .
                            '" FreeBaggageAllowance="' . $baggage . '"/>';
                    }
                    break;
                }

                $TourCode = '';
                if (!empty($this->order['bonus_card'])) {
                    $TourCode = ' TourCode="' . $this->order['bonus_card'] . '"';
                }

                $this->client->actionAttr['SITA_AirDemandTicketRQ'] = array(
                    'Version' => '0'
                );

                $this->client->uri = 'http://sita.aero/SITA_AirDemandTicketRQ/3/0';
                $reqPayloadString = $this->TicketPOS .
                    '<DemandTicketDetail' . $TourCode . '>' . $Passengers .
                        '<TPA_Extensions>' . $TPA_Extensions . '</TPA_Extensions>' .
                        '<AdditionalItineraryData><AdditionalSegmentInfos>' . $AdditionalSegmentInfos . '</AdditionalSegmentInfos></AdditionalItineraryData>' .
                        '<PaymentInfo PaymentType="1"/>' .
                        '<BookingReferenceID ID="' . $this->pnrNumber . '" Type="14"/>' .
                    '</DemandTicketDetail>';

                $this->client->SITA_AirDemandTicketRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
                $result = $this->client->__getLastResponse();
                $result = str_replace('common:', '',$result);
                $result = XML2Array::createArray($result);
                $result = isset($result['SOAP-ENV:Envelope']['SOAP-ENV:Body']) ? $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'] : $result['Envelope']['Body'];
                $this->writeLog('SITA_AirDemandTicketRQ', 'TicketingBook', $result);

                $forLog[] = $result;

                if (isset($result['SITA_AirDemandTicketRS']['Success'])) {
                    $return_result[] = $result['SITA_AirDemandTicketRS']['TicketItemInfo'];
                } else {
                    $error[] = 'Something wrong in SITA'; // Могут не все билеты выписаться в цикле.
                    $statusLock = 'R';
                }
            }

            TicketLog::write($return_result, 'Results:', 'TicketingBook');

            $resultReturn = $this->SetResults($return_result);

            if (WtDB::Ref()->OrdersValue(new WtMapArgs('field', 'status', 'order', $this->order['id'])) == 'I') { // Если отработал SetResults, но статус заказа I - то чтото не так.
                $error[] = 'Something wrong in SetResults or addTickets';
                $statusLock = 'I';
            }

            if (!empty($error)) { // Если чтото пошло не так формируем лог ошибки
                $tickets = WtDB::Ref()->OrderTicketsRows(new WtMapArgs('order_id', $this->order['id']));
                $log     = array('Order'        => $this->order,
                                 'PNR'          => $this->PNR,
                                 'Tickets'      => $tickets,
                                 'Response'     => $resultReturn,
                                 'Error'        => $error,
                                 'ResponseSITA' => $forLog);

                WtDB::Ref()->OrderLockUpdate(new WtMapArgs('order_id', $this->order['id'], 'status', $statusLock)); // Отдаем ошибку на интерфейс
                WtDB::Ref()->OrderErrorInsert(new WtMapArgs('order_id', $this->order['id'], 'log', serialize($log))); // Заносим ошибку и все возможное для ее отлова в лог

                return array('status' => 'error', 'text' => FuncLang::value('lbl_gabriel_error_order'));
            }

            WtDB::Ref()->OrderLockDelete(new WtMapArgs('order_id', $this->order['id'])); // Если все ОК убираем сообшение об ошибке с интерфейса

            $TransactionsValue = WtDB::Ref()->TransactionsValueRows(new WtMapArgs('request',     $this->order['request_id'],         // Если потерялся ордер id для TransactionsValue
                                                                                  'variant',     $this->order['variant'],
                                                                                  'transaction', $this->order['transaction_id'],
                                                                                  'provider',    $this->order['provider_id'],
                                                                                  'sec_number',  $this->order['sec_number']));
            if ($TransactionsValue[0]['order_id'] == null) {
                WtDB::Ref()->TransactionsValueUpdate(new WtMapArgs('request',     $this->order['request_id'],
                                                                   'variant',     $this->order['variant'],
                                                                   'transaction', $this->order['transaction_id'],
                                                                   'provider',    $this->order['provider_id'],
                                                                   'sec_number',  $this->order['sec_number'],
                                                                   'order_upd',   $this->order['id']));
            }

            return $resultReturn;

        } catch (SoapFault $exception) {
            WtDB::Ref()->OrderErrorInsert(new WtMapArgs('order_id', $this->order['id'], 'log', serialize($exception))); // Заносим ошибку и все возможное для ее отлова в лог
            $this->writeException('TicketingBook', $exception);
            $this->longQueryLog($start, 'TicketingBook exception');
            return array('status' => 'error', 'text' => FuncLang::value('lbl_gabriel_error_order'));
        }
    }

    /**
     * Занесение номеров билетов и купонов в базу
     * @param array $NumTickets
     * @return array
     */
    private function SetResults($NumTickets) {
        $start = microtime(true);
        $this->GetBook(false, false);

        $route = '';
        foreach ($this->PNR['coupons'] as $segment) {
            $route .= $segment['departure_airport'] . '-' . $segment['arrival_airport'] . ';';
        }

        foreach ($this->Tickets as &$ticket) {
            $ticket['type'] = $ticket['type'] == 'ААТ' ? 'ADT' : $ticket['type']; // если было предложение из Сирены
            $firstname = $ticket['firstname'];
            if (isset($NumTickets['@attributes']['TicketNumber'])) $NumTickets = array($NumTickets);
            foreach ($NumTickets as $fa) {
                if (isset($fa['PersonName']['MiddleName']) && !empty($fa['PersonName']['MiddleName'])) {
                    $fa['PassengerName']['GivenName'] .= ' ' . $fa['PersonName']['MiddleName'];
                } else {
                    $firstname = preg_replace('/([A-Z]+)\s*.*/', '$1', $firstname);
                }
                if ($fa['PassengerName']['GivenName'] == $firstname && $fa['PassengerName']['Surname'] == $ticket['lastname']) {
                    $ticket['eticket']  = '421-' . $fa['@attributes']['TicketNumber']; // 421 - S7
                    $ticket['totalsum'] = $fa['@attributes']['TotalAmount'];
                    $ticket['coupons']  = $this->PNR['coupons'];
                    $ticket['response'] = serialize($fa);
                    foreach ($ticket['coupons'] as $key => $coupon) {
                        $ticket['coupons'][$key]['fare_basis'] = $this->couponsInfo[$ticket['type']][$coupon['coupon_number']]['FareBasis'];
                        $ticket['coupons'][$key]['baggage'] = $this->couponsInfo[$ticket['type']][$coupon['coupon_number']]['FreeBaggageAllowance'];
                    }
                }
            }
            $ticket['route']    = $route;
            $ticket['class']    = $this->order['class'];
            $ticket['subclass'] = $this->PNR['coupons'][0]['booking_class'];
            $ticket['currency'] = $this->currency;
            $ticket['payment_owner'] = 'INV';//$this->payment_method;
        }
        TicketLog::write($this->Tickets, 'Tickets', 'Ticketing');

        $this->addTickets();

        $Response['output']             = $this->PNR_text;
        $Response['tickets']            = $this->Tickets;
//      $Response['payment_card']       = $payment_card;
//      $Response['warnings']           = $this->warnings;
        $this->longQueryLog($start, 'SetResults');
        return $Response;
    }

    /**
     * Отмена брони
     * @see BaseProvider::VoidBook()
     */
    public function VoidBook($init = true) {
        $start = microtime(true);
        try {
            $this->initialize();

            $reqPayloadString = $this->POS . '<UniqueID Type="15" ID="' . $this->pnrNumber . '" Reason="Cancel PNR for RXA unit testing."/>';
            $this->client->OTA_CancelRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
             $result = $this->client->__getLastResponse();
             $result = str_replace('common:', '',$result);
             $result = XML2Array::createArray($result);
            $result = isset($result['SOAP-ENV:Envelope']['SOAP-ENV:Body']) ? $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'] : $result['Envelope']['Body'];
            $this->writeLog('OTA_CancelRQ', 'VoidBook', $result);

            if (isset($result['OTA_CancelRS']['Success'])) {
                $this->longQueryLog($start, 'VoidBook');
                return "ITINERARY CANCELLED";
            } else {
                $this->longQueryLog($start, 'VoidBook Errors');
                return $result['OTA_CancelRS']['Errors']['Error']['@attributes']['Code'] . ' (' .
                       $result['OTA_CancelRS']['Errors']['Error']['@attributes']['Type'] . ') - ' .
                       $result['OTA_CancelRS']['Errors']['Error']['@value'];
            }
        } catch (SoapFault $exception) {
            $this->writeException('VoidBook', $exception);
            $this->longQueryLog($start, 'VoidBook exception');
            return false;
        }
    }

    /**
     * Отмена билета
     * @see BaseProvider::VoidTicket()
     */
    public function VoidTicket($ticketNumber) {
        $start = microtime(true);
        try {
            $this->initialize();

            $ticketNumber = substr(str_replace('-', '', $ticketNumber),3,10);

            $reqPayloadString = $this->TicketPOS . '<UniqueID Type="30" ID="' . $ticketNumber . '"/>';
            $this->client->OTA_CancelRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
             $result = $this->client->__getLastResponse();
             $result = str_replace('common:', '',$result);
             $result = XML2Array::createArray($result);
            $result = isset($result['SOAP-ENV:Envelope']['SOAP-ENV:Body']) ? $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'] : $result['Envelope']['Body'];
            $this->writeLog('OTA_CancelRQ', 'VoidTicket', $result);

            if (isset($result['OTA_CancelRS']['Success'])) {
                $this->longQueryLog($start, 'VoidTicket');
                return array(
                    'status' => 'ok',
                    'text'   => FuncLang::value('err_ticket_void_success')
                );
            } else {
                $this->longQueryLog($start, 'VoidTicket error');
                return array(
                    'status'  => 'error',
                    'message' => $result['OTA_CancelRS']['Errors']['Error']['@attributes']['Code'] . ' (' .
                                 $result['OTA_CancelRS']['Errors']['Error']['@attributes']['Type'] . ') - ' .
                                 $result['OTA_CancelRS']['Errors']['Error']['@value']
                );
            }
        } catch (SoapFault $exception) {
            $this->writeException('VoidTicket', $exception);
            $this->longQueryLog($start, 'VoidTicket exception');
            return false;
        }
    }

    /**
     * Set RM
     * @param string|array $args
     */
    function SetRM($args) {
        $start = microtime(true);
        try {
            if (!is_array($args)) {
                $args = array($args);
            }
            $Remarks = '';
            foreach($args as $key => $text) {
                $Remarks .= '<Remark Operation="Add" RPH="' . ($key + 10) . '">' . $text . '</Remark>';
            }

             $this->client->uri = 'http://sita.aero/SITA_AirBookModifyRQ/3/0';
            $reqPayloadString = $this->POS . '
                <AirBookModifyRQ BookingReferenceID="' . $this->pnrNumber . '" ModificationType="5">
                    <TravelerInfo><SpecialReqDetails><Remarks>' . $Remarks . '</Remarks></SpecialReqDetails></TravelerInfo>
                </AirBookModifyRQ>';

             $this->client->OTA_AirBookModifyRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
             $result = $this->client->__getLastResponse();
             $result = str_replace('common:', '',$result);
             $result = XML2Array::createArray($result);
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
            $this->writeLog('OTA_AirBookModifyRQ', 'SetRemarks', $result);
            $this->longQueryLog($start, 'SetRM');
            return true;

        } catch (SoapFault $exception) {
            $this->writeException('SetRM', $exception);
            $this->longQueryLog($start, 'SetRM exception');
            return false;
        }
    }

    /**
     * Add SSR and seats to book
     * @param array $args
     * @throws AmadeusWSException
     * @return array|boolean
     */
    function addSSR($args) {
        $start = microtime(true);
        try {
            $services = WtDB::Ref()->PassengerServicesRows(new WtMapArgs('ids', $args));

            if (!$services) {
                $this->longQueryLog($start, 'addSSR no services');
                return false;
            }

            $service['id'] = $services[0]['id'];

            $this->client->uri = 'http://sita.aero/SITA_AirBookModifyRQ/3/0';
            $reqPayloadString  = $this->POS . '
                <AirBookModifyRQ BookingReferenceID="' . $this->pnrNumber . '" ModificationType="5">
                    <TravelerInfo>
                        <SpecialReqDetails>
                              <SeatRequests>
                                <SeatRequest FlightRefNumberRPHList="1" SeatNumber="'.$services['0']['text'].'"
                                TravelerRefNumberRPHList="1"/>
                              </SeatRequests>
                        </SpecialReqDetails>
                    </TravelerInfo>
                </AirBookModifyRQ>';

            $this->client->OTA_AirBookModifyRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = $this->client->__getLastResponse();
            $result = str_replace('common:', '', $result);
            $result = XML2Array::createArray($result);
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
            $this->writeLog('OTA_AirBookModifyRQ', 'SetRemarks', $result);

            if (isset ($result['OTA_AirBookRS']['Errors'])) {
                $service['status'] = 'N';
                WtDB::Ref()->PassengerServicesUpdate(new WtFuncArgs($service));
                $this->longQueryLog($start, 'addSSR Errors');
                return false;
            } else {
                $service['status'] = 'Y';
                WtDB::Ref()->PassengerServicesUpdate(new WtFuncArgs($service));
                $this->longQueryLog($start, 'addSSR');
                return 'SITA.SeatAdd';
            }

        } catch (SoapFault $exception) {
            $this->writeException('addSSR', $exception);
            $this->longQueryLog($start, 'addSSR exception');
            return false;
        }
    }



    public function GetDataForRevalidationTicket($ticketNumber) {
        $start = microtime(true);
        $this->longQueryLog($start, 'GetDataForRevalidationTicket');
    }

    public function RevalidationTicket(WtFuncArgs $args) {
        $start = microtime(true);
        $this->longQueryLog($start, 'RevalidationTicket');
    }

    public function GetDataForExchangeTicket($ticketNumber) {
        $start = microtime(true);
        $this->longQueryLog($start, 'GetDataForExchangeTicket');
    }

    public function ExchangeTicket(WtFuncArgs $args) {
        $start = microtime(true);
        $this->longQueryLog($start, 'ExchangeTicket');
    }

    public function GetDataForRefundTicket($ticketNumber) {
        $start = microtime(true);
        $this->longQueryLog($start, 'GetDataForRefundTicket');
    }

    public function RefundTicket(WtFuncArgs $args) {
        $start = microtime(true);
        $this->longQueryLog($start, 'RefundTicket');
    }


    private function longQueryLog($start, $funcName, $time = 0) {
        if ($this->longQueryCatch) {
            $var = number_format(microtime(true)- $start, 4);
            $maxTime = ($time == 0) ? $this->longQueryTime : $time;
            if ($var > $maxTime) {
                $args = 'method = '.$funcName.'; time = '.$var.'; ';
                $args .= (isset($this->orderId))          ? 'orderId = '.$this->orderId.'; '                   : '';
                $args .= (isset($this->providerId))       ? 'providerId = '.$this->providerId.'; '             : '';
                $args .= (isset($this->Office))           ? 'officeid = '.$this->Office.'; '                 : '';
                $args .= (isset($this->pnrNumber) && $this->pnrNumber != '') ? 'pnrNumber = '.$this->pnrNumber.'; ' : '';

                if (isset ($this->order) && !empty($this->order)) {
                    $args .= 'request_id = '.$this->order['request_id'].'; ';
                    $args .= 'transaction_id = '.$this->order['transaction_id'].'; ';
                    $args .= 'provider_id = '.$this->order['provider_id'].'; ';
                    $args .= 'user_id = '.$this->order['user_id'].'; ';
                    $args .= 'route = '.$this->order['route'].'; ';
                }
                dumpLog($args, 'GabrielTicket', 'gabriel_long_query.log');
            }
        }
    }



}
