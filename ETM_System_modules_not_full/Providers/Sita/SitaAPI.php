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

/*
 * @deprecated
 */
class SITASoap extends SoapClient {

    public $actionAttr = array();

    function __doRequest($request, $location, $saction, $version) {
        $action = substr($saction, strpos($saction, '#')+1);
        if (!empty($this->actionAttr) && array_key_exists($action, $this->actionAttr)) {
            $actionAttr = '';
            foreach ($this->actionAttr[$action] as $attr => $value) {
                $actionAttr .= $attr . '="' . $value . '" ';
            }
            $request = str_replace("<SOAP-ENV:Body><ns1:$action>", "<SOAP-ENV:Body><ns1:$action " . $actionAttr . ">", $request);
        }

        $dom = new DOMDocument();
        $dom->loadXML($request);

        $objWSSE = new WSSESoap($dom);

        /* create new XMLSec Key using RSA SHA-1 and type is private key */
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type' => 'private'));

        /* load the private key from file - last arg is bool if key in file (TRUE) or is string (FALSE) */
        $objKey->loadKey(PRIVATE_KEY, TRUE);

        /* Sign the message - also signs appropraite WS-Security items */
        $objWSSE->signSoapDoc($objKey);

        /* Add certificate (BinarySecurityToken) to the message and attach pointer to Signature */
        $token = $objWSSE->addBinaryToken(file_get_contents(CERT_FILE));
        $objWSSE->attachTokentoSig($token);

        $request = $objWSSE->saveXML();

        return parent::__doRequest($request, $location, $saction, $version);
    }
}

/***************************************************
* SITA API
***************************************************/

/*
 * @deprecated
 */
class WtSitaAPI {

    private $client;
    private $POS;
    private $AirfarePOS;
    private $provider;
    private $Currency;
    private $login;
    private $password;
    private $agency;

    function __construct($providerOid, $session = NULL) {
        $this->provider = WtDB::Ref()->ProvidersOfficeProcess(new WtMapArgs('oid', $providerOid));
        $this->login    = $this->provider['login'];
        $this->password = $this->provider['password'];
        $this->agency   = $this->provider['agency'];
        $this->Currency = $this->provider['currency'];
        $this->GDS      = strtoupper($this->provider['gds']);  // SITA
        $this->Office      = 'DUS900';
        $this->GroupCode   = '115';
        $this->CountryCode = 'DE';
        $this->CityCode    = 'DUS';
        $this->AirlineID   = 'S7';
        $this->RequestorID = '';

        $this->POS = '
            <POS>
                <Source ERSP_UserID="'.$this->login.'/'.$this->password.'" AgentSine="'.$this->agency.'" PseudoCityCode="'.$this->Office.'" AgentDutyCode="'.$this->GroupCode.'" ISOCountry="'.$this->CountryCode.'" AirlineVendorID="'.$this->AirlineID.'" AirportCode="'.$this->CityCode.'"/>
            </POS>';
        $this->AirfarePOS = '
            <POS>
                <Source ERSP_UserID="'.$this->login.'/'.$this->password.'" AgentSine="'.$this->agency.'" PseudoCityCode="'.$this->Office.'" AgentDutyCode="'.$this->GroupCode.'" ISOCountry="'.$this->CountryCode.'" AirlineVendorID="'.$this->AirlineID.'" AirportCode="'.$this->CityCode.'"/>
                <Source><RequestorID Type="6" ID="'.$this->RequestorID.'" ID_Context="Airfare"/></Source>
            </POS>';
//  <Source><RequestorID Type="7" ID="42174053E" ID_Context="Airfare"/></Source>

        $this->client = new SITASoap(null, array(
            'soap_version' => SOAP_1_1,
//            'location'   => 'https://sws.qa.sita.aero/sws/', // test url
            'location'     => 'https://sws.sita.aero/sws/',
            'uri'          => 'http://www.opentravel.org/OTA/2003/05',
            'trace'        => 1 // need for $this->client->__getLastResponse() !!!!!!!
        ));

        $this->log = true;
        if ($this->log) {
            TicketLog::Ref()->set_log_file(WtSession::Ref()->sid());
        }
//      $this->SignIn();
    }

    function CheckErrors($result) {
        $return_result = false;
        if (!empty($result['Errors'])) {
            foreach ($result['Errors'] as $error) {
                $return_result['errors'][] = array(
                    'Id'      => $error['@attributes']['Code'],
                    'Message' => $error['@attributes']['Type'] . ': ' . $error['@value']
                );
            }
        }
        return $return_result;
    }

    /**
    * Ping provides a simple echo to validate that SITA Reservations Web Services is working and
    * responding.
    *
    */
    function Ping() {
        $string = 'Ping';
        try {
            $reqPayloadString = $this->POS . '<EchoData>' . $string . '</EchoData>';
            $result = $this->client->OTA_PingRQ(new SoapVar($reqPayloadString, XSD_ANYXML));

            $this->pingData = array(
                'request'  => htmlentities($this->client->__getLastRequest()),
                'response' => htmlentities($this->client->__getLastResponse())
            );

            return isset($result['Success']) && $result['EchoData'] == $string;
        } catch (SoapFault $fault) {
            TicketLog::write($this->client->__getLastRequest(), 'Request:', 'Exception Ping');
            TicketLog::write($this->client->__getLastResponse(), 'Response:', 'Exception Ping');
            TicketLog::write($fault, 'Exception:', 'Ping');
            return false;
        }
    }

    /**
    * The ping transaction provides verification that the Airfare application and interface
    * components are responding to requests, it also returns an indication of when the transaction
    * was processed, the interface and software version.
    *
    */
/*  function AirfarePing() {
        try {
            $this->client->uri = 'http://www.sita.aero/PTS/fare/2005/12/PingRQ';
            $reqPayloadString = '<POS><Source AirlineVendorID="S7" AirportCode="DUS"/></POS>';
            $result  = $this->client->SITA_PingRQ(new SoapVar($reqPayloadString, XSD_ANYXML));

            echo "<pre>\n\n";
            echo "REQUEST HEADERS:\n" . $this->client->__getLastRequestHeaders() . "\n";
            echo "Request :\n";
            echo htmlentities($this->client->__getLastRequest())."\n";
            echo "</pre>";
            dump($result);

            return isset($result['Success']) && $result['EchoData'] == $string;
        } catch (SoapFault $fault) {
            dump($fault);
            return false;
        }
    }
*/

    /**
    * The SITA_AirfareCalculateCurrencyRQ transaction converts monetary amounts from a given
    * currency into another currency using the specified rate type or exchange rate.
    *
    */
    function CurrencyConversion($args) {
        try {
            $RateOfExchange = 1;
            $RateDate = date('Y-m-d');

            $this->client->uri = 'http://www.sita.aero/PTS/fare/2005/12/CalculateCurrencyRQ';
            $reqPayloadString = $this->POS . '
                <POS><Source><RequestorID Type="6" ID="'.$this->RequestorID.'" ID_Context="Airfare"/></Source></POS>
                <CurrencyRQInfo FromCurrency="' . $args['from'] . '" ToCurrency="' . $args['to'] . '" RateDate="' . $RateDate . '"/>';

            $result  = $this->client->SITA_AirfareCalculateCurrencyRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = XML2Array::createArray($this->client->__getLastResponse());
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
            if ($this->log) {
                TicketLog::write($this->client->__getLastRequest(), 'Request SITA_AirfareCalculateCurrency:', 'CurrencyConversion');
                TicketLog::write($this->client->__getLastResponse(), 'Response SITA_AirfareCalculateCurrency:', 'CurrencyConversion');
//                TicketLog::write($result, 'SITA_AirfareCalculateCurrency:', 'CurrencyConversion');
            }

            $ExchangeRates = $result['sita:SITA_AirfareCalculateCurrencyRS']['sita:ExchangeRates']['sita:ExchangeRate'];
            if (isset($ExchangeRates['@value'])) $ExchangeRates = array($ExchangeRates);
            foreach($ExchangeRates as $ExchangeRate) {
                $RateOfExchange = $ExchangeRate['@attributes']['RateOfExchange'];
                if ($ExchangeRate['@attributes']['ExchangeRateType'] == 'IATA_ClearingHouse') { // BankersBuyingRate, BankersSellingRate, IATA_ClearingHouse
                    break;
                }
            }

            return isset($result['sita:SITA_AirfareCalculateCurrencyRS']['sita:Success']) ? $RateOfExchange : false;
        } catch (SoapFault $fault) {
            TicketLog::write($this->client->__getLastRequest(), 'Request:', 'Exception CurrencyConversion');
            TicketLog::write($this->client->__getLastResponse(), 'Response:', 'Exception CurrencyConversion');
            TicketLog::write($fault, 'Exception:', 'CurrencyConversion');
            return false;
        }
    }

    /**
    * The SITA_AirfareRateOfExchangeRQ exchange transaction returns current or historic
    * Bankers Buying Rate (BBR), Bankers Selling Rate (BSR) and IATA Clearing House rates of
    * exchange for one or more specified currencies.
    *
    */
    function RateOfExchange($args) {
        try {
            $RateDate = date('Y-m-d');

            $this->client->uri = 'http://www.sita.aero/PTS/fare/2005/12/RateOfExchangeRQ';
            $reqPayloadString = $this->POS . '
                <POS><Source><RequestorID Type="6" ID="'.$this->RequestorID.'" ID_Context="Airfare"/></Source></POS>
                <RateOfExchangeRQFilter AirportCity="' . $args['AirportCity'] . '"/>';

            $this->client->actionAttr['SITA_AirfareRateOfExchangeRQ'] = array(
                'RequestType'   => 'IATA_RateOfExchange',       // BankersBuyingRate, BankersSellingRate, IATA_RateOfExchange
                'TicketDate'    => $RateDate
            );

            $result  = $this->client->SITA_AirfareRateOfExchangeRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = XML2Array::createArray($this->client->__getLastResponse());
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
            if ($this->log) {
                TicketLog::write($this->client->__getLastRequest(), 'Request SITA_AirfareRateOfExchange:', 'RateOfExchange');
                TicketLog::write($this->client->__getLastResponse(), 'Response SITA_AirfareRateOfExchange:', 'RateOfExchange');
//                TicketLog::write($result, 'SITA_AirfareRateOfExchange:', 'RateOfExchange');
            }

            $ExchangeRates = $result['sita:SITA_AirfareRateOfExchangeRS']['sita:ExchangeRates']['sita:ExchangeRate'];
            if (isset($ExchangeRates['sita:DomesticRounding'])) $ExchangeRates = array($ExchangeRates);
            foreach($ExchangeRates as $ExchangeRate) {
                $RateOfExchange = $ExchangeRate['@attributes']['RateOfExchange'];
                if ($ExchangeRate['@attributes']['ExchangeRateType'] == 'IATA_RateOfExchange') { // BankersBuyingRate, BankersSellingRate, IATA_RateOfExchange
                    break;
                }
            }

            return isset($result['sita:SITA_AirfareRateOfExchangeRS']['sita:Success']) ? $RateOfExchange : false;
        } catch (SoapFault $fault) {
            TicketLog::write($this->client->__getLastRequest(), 'Request:', 'Exception RateOfExchange');
            TicketLog::write($this->client->__getLastResponse(), 'Response:', 'Exception RateOfExchange');
            TicketLog::write($fault, 'Exception:', 'RateOfExchange');
            return false;
        }
    }

    function SignIn() {
        try {
            $reqPayloadString = $this->POS . '<AirlineUserId>S7</AirlineUserId>';
            $result  = $this->client->SITA_SignInRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            if ($this->log) {
                TicketLog::write($this->client->__getLastRequest(), 'Request SITA_SignIn:', 'SignIn');
                TicketLog::write($this->client->__getLastResponse(), 'Response SITA_SignIn:', 'SignIn');
//                TicketLog::write($result, 'SITA_SignIn:', 'SignIn');
            }

            return isset($result['Success']) ? $result['pid'] : $result;
        } catch (SoapFault $fault) {
            TicketLog::write($this->client->__getLastRequest(), 'Request:', 'Exception SignIn');
            TicketLog::write($this->client->__getLastResponse(), 'Response:', 'Exception SignIn');
            TicketLog::write($fault, 'Exception:', 'SignIn');
            return false;
        }
    }

    /**
    * The SITA Airfare Price Fare Display returns all applicable fares for the request parameters.
    *
    */
/*  function AirfareFareDisplay($request) {
        $Currency = $request['currency'];
        switch ($request['class']) {
            case 'E':   $classes = array('Y','B','H','K','L','M','Q','S','T','O','V','W','G','I','X');
                        break;
            case 'B':   $classes = array('C','D','J','I','Z');
                        break;
            case 'F':   $classes = array('F','P','A','J');
                        break;
        }
        $BookClasses = '';
        foreach ($classes as $class) {
            $BookClasses .= '<BookingClassPref ResBookDesigCode="' . $class . '"/>';
        }

        $Segments = '';
        for ($i = 1; $i <= $request['flight_qty']; $i++) {
            if (empty($request['departure_city_'.$i])) {
                $origin_code  = $request['departure_airport_'.$i];
            } else {
                $origin_code = $request['departure_city_'.$i];
            }
            if (empty($request['arrival_city_'.$i])) {
                $destination_code  = $request['arrival_airport_'.$i];
            } else {
                $destination_code  = $request['arrival_city_'.$i];
            }
            $Segments .=
                '<OriginDestinationInformation>
                    <DepartureDateTime WindowBefore="P1M" WindowAfter="P7D">' . $request['date_'.$i] . '</DepartureDateTime>
                    <OriginLocation LocationCode="' . $origin_code . '"/>
                    <DestinationLocation LocationCode="' . $destination_code . '"/>
                </OriginDestinationInformation>';
        }

        $FareTypePref = '';
        if ($request['flight_qty'] == 1) {
            $FareTypePref = '<FareApplicationTypePref FareApplicationType="OneWay"/>';
        }
        if ($request['flight_qty'] == 2 && $request['departure_city_1'] == $request['arrival_city_2']) {
            $FareTypePref = '<FareApplicationTypePref FareApplicationType="Return"/>';
        }

        $Passengers = '<TravelerInfoSummary><PassengerTypeQuantity Code="ADT" CodeContext="SITA" Quantity="' . $request['adult_qty'] . '"/>';
        if ($request['child_qty'] > 0) {
            $Passengers .= '<PassengerTypeQuantity Code="CNN" CodeContext="SITA" Quantity="' . $request['child_qty'] . '"/>';
        }
        if ($request['infant_qty'] > 0) {
            $Passengers .= '<PassengerTypeQuantity Code="INF" CodeContext="SITA" Quantity="' . $request['infant_qty'] . '"/>';
        }
        $Passengers .= '</TravelerInfoSummary>';

        $Airlines = '<VendorPref Code="S7"/>';
//                  <VendorPref Code="SU"/>
//                  <VendorPref Code="VV"/>
//                  <VendorPref Code="PS"/>';

        $reqPayloadString = '<OTA_AirFareDisplayRQ DisplayOrder="ByPriceLowtoHigh" EchoToken="MySession123456789" MaxResponses="0" Target="Test">'
                            . $this->AirfarePOS
                            . $Segments
                            . '<TravelPreferences>'
                            . $Airlines
                            . '<FareRestrictPref PreferLevel="Only" FareDisplayCurrency="' . $Currency . '"/>'
                            . $BookClasses
                            . $FareTypePref // OneWay, Return, HalfReturn
                            . '<PricingPrefs IncludeTaxInd="true"/>'
                            . '</TravelPreferences>'
                            . $Passengers
                            . '</OTA_AirFareDisplayRQ>'
                            . '<AdditionalFareDisplayRQData IndicateAlternateAirlines="false" PricingSource="Published">
                                    <FareTypeInfos>
                                    </FareTypeInfos>
                                </AdditionalFareDisplayRQData>';
        try {
            $this->client->uri = 'http://www.sita.aero/PTS/fare/2005/12/FareDisplayRQ';
            $result = $this->client->SITA_AirfareFareDisplayRQ(new SoapVar($reqPayloadString, XSD_ANYXML));

            $result = XML2Array::createArray($this->client->__getLastResponse());
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
//dump_die($result);
            $fares = $result['sita:SITA_AirfareFareDisplayRS']['ota:OTA_AirFareDisplayRS']['ota:FareDisplayInfos']['ota:FareDisplayInfo'];
            if (isset($fares['ota:TravelDates'])) $fares = array($fares);

            $faresInfo = array();
            foreach($fares as $fare) {
//dump_die($fare);
                $faresInfo[] = array(
                    'FareReference'     => $fare['ota:FareReference'],
                    'Airline'           => $fare['ota:FilingAirline']['@attributes']['Code'],
                    'DepartureDate'     => $fare['ota:TravelDates']['@attributes']['DepartureDate'],
                    'DepartureLocation' => $fare['ota:DepartureLocation']['@attributes']['LocationCode'],
                    'ArrivalLocation'   => $fare['ota:ArrivalLocation']['@attributes']['LocationCode'],
                    'PassengerTypeCode' => $fare['ota:PricingInfo']['@attributes']['PassengerTypeCode'],
                    'Amount'            => $fare['ota:PricingInfo']['ota:BaseFare']['@attributes']['Amount'],
                    'CurrencyCode'      => $fare['ota:PricingInfo']['ota:BaseFare']['@attributes']['CurrencyCode'],
                    'FareType'          => $fare['@attributes']['FareApplicationType'],
                    'ResBookDesigCode'  => $fare['@attributes']['ResBookDesigCode'],
                    'Ref1'              => $fare['ota:PricingInfo']['ota:TPA_Extensions']['sita:SITA_FareInfoExtension']['sita:References']['sita:Ref1'],
                    'Ref2'              => $fare['ota:PricingInfo']['ota:TPA_Extensions']['sita:SITA_FareInfoExtension']['sita:References']['sita:Ref2']
                );
            }

            return isset($result['sita:SITA_AirfareFareDisplayRS']['ota:OTA_AirFareDisplayRS']['ota:Success']) ? $faresInfo : false;
        } catch (SoapFault $fault) {
            dump($fault);
            return false;
        }
    }*/
/*
    function AirSchedule($request) {
        try {
            $reqPayloadString = $this->POS .
            '<OTA_AirScheduleRQ>
                <OriginDestinationInformation>
                    <DepartureDateTime>' . $request['DepartureDate'] . '</DepartureDateTime>
                    <OriginLocation LocationCode="' . $request['OriginLocation'] . '"/>
                    <DestinationLocation LocationCode="' . $request['DestinationLocation'] . '"/>
                </OriginDestinationInformation>
            </OTA_AirScheduleRQ>';

            $result  = $this->client->OTA_AirScheduleRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = XML2Array::createArray($this->client->__getLastResponse());
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];

            return isset($result['OTA_AirScheduleRS']['Success']) ? $result : false;
        } catch (SoapFault $fault) {
            dump($fault);
            return false;
        }
    }
/*
    function AirDetails($request) {
        try {
            $reqPayloadString = $this->POS .
            '<OTA_AirDetailsRQ>
                <Airline Code="' . $request['MarketingAirline'] . '"/>
                <FlightNumber>' . $request['FlightNumber'] . '</FlightNumber>
                <DepartureAirport LocationCode="' . $request['OriginLocation'] . '"/>
                <ArrivalAirport LocationCode="' . $request['DestinationLocation'] . '"/>
                <DepartureDate>' . $request['DepartureDate'] . '</DepartureDate>
            </OTA_AirDetailsRQ>';

            $result  = $this->client->OTA_AirDetailsRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = XML2Array::createArray($this->client->__getLastResponse());
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];

            return isset($result['OTA_AirDetailsRS']['Success']) ? $result : false;
        } catch (SoapFault $fault) {
            dump($fault);
            return false;
        }
    }
*/

    function CheckFlightSegments() {
        return array('status' => 'ok');
    }

    function FlightSeatMap() {
        return true;
    }

    function FlightPricing($args) {
        $segments = WtDB::Ref()->GetTransactionsResults(new WtMapArgs('ids', $args));

        foreach($segments as $segment) {
            $fareBasis = $segment['fare_basis_code'];
            $marketingAirline = $segment['first_marketing_airline'];

            if (WtDB::Ref()->FlightConditionsCheck(new WtMapArgs('airline', $marketingAirline, 'fare_basis', $fareBasis, 'gds', strtolower($this->provider['gds'])))) continue;

            $refs = unserialize($segment['mercado_rowid']);
            $Ref2 = $refs['Adult']['Ref2'];
            $request = array(
                'DepartureDateTime'     => $segment['first_departure_date'] . 'T00:00:00',
                'OriginLocation'        => WtDB::Ref()->CityValue(new WtMapArgs('value', 'city', 'airport', $segment['first_departure_airport'])),
                'DestinationLocation'   => WtDB::Ref()->CityValue(new WtMapArgs('value', 'city', 'airport', $segment['first_arrival_airport'])),
                'FareReference'         => $segment['fare_basis_code'],
                'Ref1'                  => htmlentities($segment['mercado_id']),
                'Ref2'                  => htmlentities($Ref2),
                'Airline'               => $segment['first_marketing_airline']
            );
            $rule = $this->AirfareRules($request);

            if (!is_array($rule)) {
                return false;
            }

            $flightConditions = array(
                'gds'               => $this->provider['gds'],
                'fare_basis'        => $fareBasis,
                'marketing_airline' => $marketingAirline,
                'baggage'           => '',
                'all_paragraphs'    => implode("\r", $rule)
            );

            WtDB::Ref()->FlightConditionsInsert(new WtFuncArgs($flightConditions));
        }
        return true;
    }

    /**
    * The rule text message returns one or more SITA automated rules textual conditions and
    * regulations for the specified fare organized by category names.
    *
    */
    function AirfareRules($request) {
        try {
            $this->client->uri = 'http://www.sita.aero/PTS/fare/2005/12/RulesRQ';
            $reqPayloadString = $this->AirfarePOS .
            '<OTA_AirRulesRQ>
                <POS><Source AirlineVendorID="S7" AirportCode="DUS"/></POS>
                <RuleReqInfo>
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

            $result  = $this->client->SITA_AirfareRulesRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = XML2Array::createArray($this->client->__getLastResponse());
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
            if ($this->log) {
                TicketLog::write($this->client->__getLastRequest(), 'Request SITA_AirfareRules:', 'AirfareRules');
                TicketLog::write($this->client->__getLastResponse(), 'Response SITA_AirfareRules:', 'AirfareRules');
//                TicketLog::write($result, 'SITA_AirfareRules:', 'AirfareRules');
            }

            if (isset($result['ota:OTA_AirRulesRS']['ota:Success'])) {
                $rulesArr = array();
                $rules = $result['ota:OTA_AirRulesRS']['ota:FareRuleResponseInfo']['ota:FareRules']['ota:SubSection'];
//dump($rules);
                foreach($rules as $paragraph) {
                    if (isset($paragraph['ota:Paragraph']['ota:Text'])) {
                        $paragraph['ota:Paragraph'] = array($paragraph['ota:Paragraph']);
                    }
                    $paragraphText = '';
                    foreach($paragraph['ota:Paragraph'] as $text) {
                        if (isset($text['ota:Text']['@value'])) {
                            $text['ota:Text'] = array($text['ota:Text']);
                        }
                        foreach($text['ota:Text'] as $line) {
                            $paragraphText .= @$line['@value'] . "\r";
                        }
                        $rulesArr[$paragraph['@attributes']['SubSectionNumber']] = $paragraph['@attributes']['SubSectionNumber'] . ' ' . $paragraph['@attributes']['SubTitle'] . "\r" . $paragraphText;
                    }
                }
                $rulesArr['xml_response'] = $this->client->__getLastResponse();
                return $rulesArr;
            } else {
                $errors = $result['ota:OTA_AirRulesRS']['ota:Errors']['ota:Error'];
                if (isset($errors['@value'])) {
                    $errors = array($errors);
                }
                $ErrorText = '';
                foreach($errors as $error) {
                    $ErrorText .= $error['@value'] . ";\r";
                }
                return $ErrorText;
            }
        } catch (SoapFault $fault) {
            TicketLog::write($this->client->__getLastRequest(), 'Request:', 'Exception AirfareRules');
            TicketLog::write($this->client->__getLastResponse(), 'Response:', 'Exception AirfareRules');
            TicketLog::write($fault, 'Exception:', 'AirfareRules');
            return false;
        }
    }

    /**
    * The routing message returns routing information for the specified carrier, route-tariff and
    * routing number.
    *
    */
/*  function AirfareRouting($request) {
        try {
            $this->client->uri = 'http://www.sita.aero/PTS/fare/2005/12/RoutingRQ';
            $reqPayloadString = $this->POS . '
            <POS><Source><RequestorID Type="6" ID="'.$this->RequestorID.'" ID_Context="Airfare"/></Source></POS>
            <OriginDestinationInformation>
                <DepartureDateTime>' . $request['DepartureDateTime'] . '</DepartureDateTime>
                <OriginLocation LocationCode="' . $request['OriginLocation'] . '" CodeContext="IATA"/>
                <DestinationLocation LocationCode="' . $request['DestinationLocation'] . '" CodeContext="IATA"/>
            </OriginDestinationInformation>
            <FareRoutings>
                <SpecifiedRouting RouteNumber="' . $request['RouteNumber'] . '" RouteTariff="' . $request['RouteTariff'] . '">
                    <Airline Code="' . $request['Airline'] . '"/>
                </SpecifiedRouting>
            </FareRoutings>';

            $result  = $this->client->SITA_AirfareRoutingRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = XML2Array::createArray($this->client->__getLastResponse());
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];

            return isset($result['sita:SITA_AirfareRoutingRS']['sita:Success']) ? $result : false;
        } catch (SoapFault $fault) {
            dump($fault);
            return false;
        }
    }*/


    /**
    * Airfare Shop FlightShop and CalendarShop are tools that help travel purchases find the right
    * priced availability for their budget and requirement.
    * The Flight Shop request provides the cheapest available travel options for the given carrier
    * and market.
    * It should be noted that shopping is not intended to replace pricing. The Airfare shopping
    * interfaces return only a subset of the information returned in an Airfare Pricing response.
    *
    */
/*  function AirLowFareSearch($request) {
        try {
            $this->client->uri = 'http://www.sita.aero/PTS/fare/2005/12/FlightShopRQ';
            $reqPayloadString = $this->AirfarePOS .
            '<OTA_AirLowFareSearchRQ EchoToken="' . WtSession::Ref()->sid() . '" Target="Test" MaxResponses="5" DirectFlightsOnly="true">
                <POS>
                    <Source AirlineVendorID="S7" AirportCode="DUS">
                        <RequestorID Type="7" ID="42174053E" ID_Context="Airfare"/>
                    </Source>
                </POS>
                <OriginDestinationInformation RPH="01">
                    <DepartureDateTime>'.$request['DepartureDateTime'].'</DepartureDateTime>
                    <OriginLocation LocationCode="'.$request['OriginLocation'].'"/>
                    <DestinationLocation LocationCode="'.$request['DestinationLocation'].'"/>
                </OriginDestinationInformation>
                <OriginDestinationInformation RPH="02">
                    <DepartureDateTime>'.$request['DepartureDateTime2'].'</DepartureDateTime>
                    <OriginLocation LocationCode="'.$request['OriginLocation2'].'"/>
                    <DestinationLocation LocationCode="'.$request['DestinationLocation2'].'"/>
                </OriginDestinationInformation>
                <TravelPreferences>
                    <FareRestrictPref>
                        <AdvResTicketing AdvTicketingInd="true" RequestedTicketingDate="2012-12-16T12:00:00"/>
                    </FareRestrictPref>
                </TravelPreferences>
                <TravelerInfoSummary>
                    <AirTravelerAvail>
                        <PassengerTypeQuantity Code="ADT" Quantity="1"/>
                    </AirTravelerAvail>
                    <PriceRequestInformation PricingSource="Published"/>
                </TravelerInfoSummary>
            </OTA_AirLowFareSearchRQ>';

            $result  = $this->client->SITA_AirfareFlightShopRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = XML2Array::createArray($this->client->__getLastResponse());
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
dump($result);

            return isset($result['OTA_AirAvailRS']['Success']) ? $result : false;
        } catch (SoapFault $fault) {
            dump($fault);
            return false;
        }
    }*/

    /**
    * The Air Availability Request message requests Flight Availability for a specific city pair, on a
    * specific date and for a specific number of passengers and class of service. The request can be
    * narrowed down to display further availability for a specific airline, specific flight or a specific
    * booking class on a specific flight.
    *
    */
    function AirAvail($request) {
        try {
            $this->client->uri = 'http://www.opentravel.org/OTA/2003/05';

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
                $origin_code = $request['departure_city_'.$i] ? $request['departure_city_'.$i] : $request['departure_airport_'.$i];
                $destination_code = $request['arrival_city_'.$i] ? $request['arrival_city_'.$i] : $request['arrival_airport_'.$i];
                $Segment =
//                  '<OriginDestinationInformation RPH="' . $i . '">
                    '<OriginDestinationInformation>
                        <DepartureDateTime>' . $request['date_'.$i] . '</DepartureDateTime>
                        <OriginLocation LocationCode="' . $origin_code . '"/>
                        <DestinationLocation LocationCode="' . $destination_code . '"/>
                    </OriginDestinationInformation>';
                $SeatsRequested  = $request['adult_qty'] + $request['child_qty'];
//              $SeatsRequested += $request['infant_qty'];

                $reqPayloadString = $this->POS
                    . $Segment
                    . '<TravelerInfoSummary><SeatsRequested>'
                    . $SeatsRequested
                    . '</SeatsRequested></TravelerInfoSummary>'
                    . '<TravelPreferences><VendorPref Code="S7"/>' . $BookingClassPref . '</TravelPreferences>';

                $result  = $this->client->OTA_AirAvailRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
                $result = XML2Array::createArray($this->client->__getLastResponse());
                $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
                if ($this->log) {
                    TicketLog::write($this->client->__getLastRequest(), 'Request SITA_AirAvail:', 'AirAvail');
                    TicketLog::write($this->client->__getLastResponse(), 'Response SITA_AirAvail:', 'AirAvail');
//                    TicketLog::write($result, 'SITA_AirAvail:', 'AirAvail');
                }

                $results = $result['OTA_AirAvailRS']['OriginDestinationOptions']['OriginDestinationOption'];
                foreach($results as $segment) {
//dump_die($segment);
                    if (isset($segment['FlightSegment']['@attributes'])) $segment['FlightSegment'] = array($segment['FlightSegment']);
                    $Legs = array();
                    foreach ($segment['FlightSegment'] as $leg) {
//dump($leg);
                        if ($leg['MarketingAirline']['@attributes']['Code'] != 'S7') continue;
                        if ($leg['@attributes']['Ticket'] != 'eTicket') continue;
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
//dump($Legs);
                    if (!empty($Legs)) {
                        $offer = array(
                            'direction' => $i,
                            'segments'  => $Legs
                        );
                        $offers[] = $offer;
                    }
                }
            }
//dump($offers);
//dump_die($offers);
            return isset($result['OTA_AirAvailRS']['Success']) ? $offers : false;
        } catch (SoapFault $fault) {
            TicketLog::write($this->client->__getLastRequest(), 'Request:', 'Exception AirAvail');
            TicketLog::write($this->client->__getLastResponse(), 'Response:', 'Exception AirAvail');
            TicketLog::write($fault, 'Exception:', 'AirAvail');
            return false;
        }
    }

    /**
    * The itinerary pricing transaction allows the client to price a PNR returning the lowest price for
    * the specified passengers and itinerary. The itinerary pricing request is similar to the Airfare
    * terminal FSI entry.
    *
    */
    function AirfarePrice($request) {
//dump_die($request);
        try {
            $this->client->uri = 'http://www.sita.aero/PTS/fare/2005/11/PriceRQ';
            $Segments = '';
            foreach($request['segments'] as $leg) {
                $Segments .=
                    '<OriginDestinationOption>
                        <FlightSegment ArrivalDateTime="' . $leg['ArrivalDateTime']
                        . '" DepartureDateTime="' . $leg['DepartureDateTime']
                        . '" StopQuantity="' . $leg['StopQuantity']
                        . '" FlightNumber="' . $leg['FlightNumber']
//                      . '" ResBookDesigCode="' . $ResBookDesigCode
//                      . '" Status="' . 13
                        . '" RPH="' . $leg['RPH'] . '">
                            <DepartureAirport LocationCode="' . $leg['DepartureAirport'] . '"/>
                            <ArrivalAirport LocationCode="' . $leg['ArrivalAirport'] . '"/>
                            <MarketingAirline Code="' . $leg['MarketingAirline'] . '"/>
                        </FlightSegment>
                    </OriginDestinationOption>';
            }

            $AirTravelerAvail  = '<PassengerTypeQuantity Code="ADT" Quantity="1" RPH="1"/>';
            $AirTravelerAvail .= $request['child_qty'] > 0 ? '<PassengerTypeQuantity Code="CNN" Quantity="1" RPH="1"/>' : '';
            $AirTravelerAvail .= $request['infant_qty'] > 0 ? '<PassengerTypeQuantity Code="INF" Quantity="1" RPH="1"/>' : '';

            $reqPayloadString = $this->AirfarePOS . '
                <OTA_AirPriceRQ xmlns="http://www.opentravel.org/OTA/2003/05">
                    <POS>
                        <Source AirlineVendorID="S7" AirportCode="DUS"/>
                    </POS>
                    <AirItinerary>
                        <OriginDestinationOptions>' . $Segments . '</OriginDestinationOptions>
                    </AirItinerary>
                    <TravelerInfoSummary>
                        <AirTravelerAvail>' . $AirTravelerAvail . '</AirTravelerAvail>
                    </TravelerInfoSummary>
                </OTA_AirPriceRQ>';

            $result  = $this->client->SITA_AirfarePriceRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = XML2Array::createArray($this->client->__getLastResponse());
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
            if ($this->log) {
                TicketLog::write($this->client->__getLastRequest(), 'Request SITA_AirfarePrice:', 'AirfarePrice');
                TicketLog::write($this->client->__getLastResponse(), 'Response SITA_AirfarePrice:', 'AirfarePrice');
//                TicketLog::write($result, 'SITA_AirfarePrice:', 'AirfarePrice');
            }

            if (isset($result['sita:SITA_AirfarePriceRS']['ota:OTA_AirPriceRS']['ota:Errors'])) {
                return false;
            }

            $prices = $result['sita:SITA_AirfarePriceRS']['ota:OTA_AirPriceRS']['ota:PricedItineraries'];
            if (isset($prices['ota:PricedItinerary']['ota:AirItinerary'])) {
                $prices['ota:PricedItinerary'] = array($prices['ota:PricedItinerary']);
            }
            $AirfarePrices = array();
            $TotalFare = $TotalTax = 0;
            foreach ($prices['ota:PricedItinerary'] as $price) {
//dump($price);
                $FareBreakdown  = $price['ota:AirItineraryPricingInfo']['ota:PTC_FareBreakdowns']['ota:PTC_FareBreakdown'];
                $References     = $price['ota:AirItineraryPricingInfo']['ota:FareInfos']['ota:FareInfo']['ota:TPA_Extensions']['sita:SITA_FareInfoExtension']['sita:References'];
                switch($FareBreakdown['ota:PassengerTypeQuantity']['@attributes']['Code']) {
                    case 'ADT': $prefix = 'Adult';
                                $AirfarePrices['Ref1']              = $References['sita:Ref1'];
                                $AirfarePrices['FareBasisCode']     = $FareBreakdown['ota:FareBasisCodes']['ota:FareBasisCode'];
                                $AirfarePrices['TicketTimeLimit']   = $price['ota:TicketingInfo']['@attributes']['TicketTimeLimit'];
                                $AirfarePrices['Currency']          = $FareBreakdown['ota:PassengerFare']['ota:TotalFare']['@attributes']['CurrencyCode'];
                                break;
                    case 'CNN': $prefix = 'Child';
                                break;
                    case 'INF': $prefix = 'Infant';
                                break;
                }
                $AirfarePrices[$prefix.'TotalFare'] = $FareBreakdown['ota:PassengerFare']['ota:TotalFare']['@attributes']['Amount'];
                if ($FareBreakdown['ota:PassengerFare']['ota:BaseFare']['@attributes']['CurrencyCode'] == $this->provider['currency']) {
                    $AirfarePrices[$prefix.'BaseFare'] = $FareBreakdown['ota:PassengerFare']['ota:BaseFare']['@attributes']['Amount'];
                } elseif (isset($FareBreakdown['ota:PassengerFare']['ota:EquivFare']) &&
                        $FareBreakdown['ota:PassengerFare']['ota:EquivFare']['@attributes']['CurrencyCode'] == $this->provider['currency']) {
                    $AirfarePrices[$prefix.'BaseFare'] = $FareBreakdown['ota:PassengerFare']['ota:EquivFare']['@attributes']['Amount'];
                } else {
                    $AirfarePrices[$prefix.'BaseFare'] = $FareBreakdown['ota:PassengerFare']['ota:BaseFare']['@attributes']['Amount'] * $FareBreakdown['ota:PassengerFare']['ota:BaseFare']['@attributes']['Rate'];
                }
                $AirfarePrices[$prefix.'TaxAmount'] = 0;
                $TotalFare += $AirfarePrices[$prefix.'BaseFare'];

                $priceInfo = array(
//                  'SequenceNumber'=> $price['@attributes']['SequenceNumber'],
                );
                $Taxes = array();
                if (isset($FareBreakdown['ota:PassengerFare']['ota:Taxes'])) {
                    foreach ($FareBreakdown['ota:PassengerFare']['ota:Taxes']['ota:Tax'] as $tax) {
//dump($tax);
                        if (!is_array($tax)) continue;
                        $Taxes[$tax['@attributes']['TaxCode']] = array(
                            'TaxCode'   => $tax['@attributes']['TaxCode'],
                            'Amount'    => $tax['@attributes']['Amount'],
                            'Currency'  => $tax['@attributes']['CurrencyCode']
                        );
                        $AirfarePrices[$prefix.'TaxAmount'] += $tax['@attributes']['Amount'];
                    }
                    $TotalTax += $AirfarePrices[$prefix.'TaxAmount'];
                }
                if ($prefix == 'Adult') $AirfarePrices['Taxes'] = $Taxes;
//              $priceInfo['Notes'] = $price['ota:Notes'];
                $priceInfo['Ref2']  = $References['sita:Ref2'];

                $AirfarePrices['PassengersRef'][$prefix] = $priceInfo;
            }
            $AirfarePrices['TotalFare'] = $TotalFare;
            $AirfarePrices['TotalTax']  = $TotalTax;
            $AirfarePrices['PricedItinerary'] = serialize($prices['ota:PricedItinerary']);

//dump_die($AirfarePrices);

            return isset($result['sita:SITA_AirfarePriceRS']['ota:OTA_AirPriceRS']['ota:Success']) ? $AirfarePrices : false;
        } catch (SoapFault $fault) {
            TicketLog::write($this->client->__getLastRequest(), 'Request:', 'Exception AirfarePrice');
            TicketLog::write($this->client->__getLastResponse(), 'Response:', 'Exception AirfarePrice');
            TicketLog::write($fault, 'Exception:', 'AirfarePrice');
            return false;
        }
    }

    function SearchFlight($args) {
        $request = $args['request'];
        $transaction_id = $args['id'];
        $request_id     = $request['id'];

        $offers = $this->AirAvail($request);
        if (!$offers) return false;

        $directions = array();
        foreach ($offers as $key => &$offer) {
            $offer['prices'] = $this->AirfarePrice(array_merge($request, $offer));
            if (!$offer['prices']) unset($offers[$key]);
            $offer['message'] = $offer['prices']['PricedItinerary'];
            $directions[$offer['direction']][$offer['prices']['FareBasisCode']][] = $offer;
        }
//dump_die($directions);
        $ResultOffers = array();
        $sec_number = 0;
        foreach ($directions[1] as $fareCode => $direction) {
            foreach ($direction as $key => &$offer) {
                $offer['sec_number'] = $sec_number;
                $offer['ref_number'] = $key;
                $ResultOffers[$offer['sec_number']][$offer['ref_number']][$offer['direction']] = $offer;

                for($i=2; $i<=count($directions); $i++) {
                    foreach ($directions[$i] as $key_leg => $leg) {
                        foreach ($leg as $key_fare => $fare) {
                            $fare['sec_number'] = $sec_number;
                            $fare['ref_number'] = $key_leg + $key_fare;
                            $ResultOffers[$fare['sec_number']][$fare['ref_number']][$fare['direction']] = $fare;
                        }
                    }
                }
            }
            $sec_number++;
        }
//dump($ResultOffers);
        $Results = array();
        foreach($ResultOffers as &$sequence) {
            $AdultTotalFare = $AdultBaseFare = $AdultTaxAmount = 0;
            $ChildTotalFare = $ChildBaseFare = $ChildTaxAmount = 0;
            $InfantTotalFare = $InfantBaseFare = $InfantTaxAmount = 0;
            $TotalFare = $TotalTax = $Stops = 0;
            $ref = $sequence[0];
            for($i=1; $i<=count($ref); $i++) {
                $AdultTotalFare += $ref[$i]['prices']['AdultTotalFare'];
                $AdultBaseFare  += $ref[$i]['prices']['AdultBaseFare'];
                $AdultTaxAmount += $ref[$i]['prices']['AdultTaxAmount'];
                if (isset($ref[$i]['prices']['ChildTotalFare'])) {
                    $ChildTotalFare += $ref[$i]['prices']['ChildTotalFare'];
                    $ChildBaseFare  += $ref[$i]['prices']['ChildBaseFare'];
                    $ChildTaxAmount += $ref[$i]['prices']['ChildTaxAmount'];
                }
                if (isset($ref[$i]['prices']['InfantTotalFare'])) {
                    $InfantTotalFare += $ref[$i]['prices']['InfantTotalFare'];
                    $InfantBaseFare  += $ref[$i]['prices']['InfantBaseFare'];
                    $InfantTaxAmount += $ref[$i]['prices']['InfantTaxAmount'];
                }
                $Stops += count($ref[$i]['segments'])-1;
                $TotalFare += $ref[$i]['prices']['TotalFare'];
                $TotalTax  += $ref[$i]['prices']['TotalTax'];
            }
            foreach($sequence as &$ref) {
                foreach($ref as &$dir) {
                    $dir['prices']['AdultTotalFare'] = $AdultTotalFare;
                    $dir['prices']['AdultBaseFare'] = $AdultBaseFare;
                    $dir['prices']['AdultTaxAmount'] = $AdultTaxAmount;
                    if (isset($dir['prices']['ChildTotalFare'])) {
                        $dir['prices']['ChildTotalFare'] = $ChildTotalFare;
                        $dir['prices']['ChildBaseFare'] = $ChildBaseFare;
                        $dir['prices']['ChildTaxAmount'] = $ChildTaxAmount;
                    }
                    if (isset($dir['prices']['InfantTotalFare'])) {
                        $dir['prices']['InfantTotalFare'] = $InfantTotalFare;
                        $dir['prices']['InfantBaseFare'] = $InfantBaseFare;
                        $dir['prices']['InfantTaxAmount'] = $InfantTaxAmount;
                    }
                    $dir['prices']['TotalFare'] = $TotalFare;
                    $dir['prices']['TotalTax'] = $TotalTax;
                    $dir['sec_stop'] = $Stops;
                    $Results[] = $dir;
                }
            }
        }
//dump_die($Results);
        foreach($Results as $key => $sequence) {
//dump($sequence);
            $args = array(
                'provider_id'               => $this->provider['pid'],
                'transaction_id'            => $transaction_id,
                'request_id'                => $request_id,
                'gds'                       => ucfirst(strtolower($this->GDS)),
                'officeid'                  => $this->provider['officeid'],
                'mercado_id'                => $sequence['prices']['Ref1'],
                'mercado_rowid'             => serialize($sequence['prices']['PassengersRef']),
                'last_ticketing_date'       => $sequence['prices']['TicketTimeLimit'] ? $sequence['prices']['TicketTimeLimit'] : date('Y-m-d'),
                'last_ticketing_timezone'   => '+00:00',
                'direction_id'              => $sequence['direction'],
                'sec_number'                => $sequence['sec_number'],
                'ref_number'                => $sequence['ref_number'],
                'currency'                  => $sequence['prices']['Currency'],
                'fare_basis_code'           => $sequence['prices']['FareBasisCode'],
                'taxes'                     => serialize($sequence['prices']['Taxes'])
            );
            if (isset($sequence['prices']['AdultTotalFare'])) {
                $args['adult_qty']        = $request['adult_qty'];
                $args['adult_base_fare']  = $sequence['prices']['AdultBaseFare'] ? $sequence['prices']['AdultBaseFare'] : 0.00;
                $args['adult_tax_amount'] = isset($sequence['prices']['AdultTaxAmount']) ? $sequence['prices']['AdultTaxAmount'] : 0.00;
                $args['adult_total_fare'] = $request['adult_qty'] * $sequence['prices']['AdultTotalFare'];
            }
            if (isset($sequence['prices']['ChildTotalFare'])) {
                $args['child_qty']        = $request['child_qty'];
                $args['child_base_fare']  = $sequence['prices']['ChildBaseFare'] ? $sequence['prices']['ChildBaseFare'] : 0.00;
                $args['child_tax_amount'] = isset($sequence['prices']['ChildTaxAmount']) ? $sequence['prices']['ChildTaxAmount'] : 0.00;
                $args['child_total_fare'] = $request['child_qty'] * $sequence['prices']['ChildTotalFare'];
            }
            if (isset($sequence['prices']['InfantTotalFare'])) {
                $args['infant_qty']        = $request['infant_qty'];
                $args['infant_base_fare']  = $sequence['prices']['InfantBaseFare'] ? $sequence['prices']['InfantBaseFare'] : 0.00;
                $args['infant_tax_amount'] = isset($sequence['prices']['InfantTaxAmount']) ? $sequence['prices']['InfantTaxAmount'] : 0.00;
                $args['infant_total_fare'] = $request['infant_qty'] * $sequence['prices']['InfantTotalFare'];
            }
            $args['total_fare'] = $args['adult_total_fare'] + $args['child_total_fare'] + $args['infant_total_fare'];
            $args['base_fare']  = ($args['adult_base_fare'] * $request['adult_qty']) + ($args['child_base_fare'] * $request['child_qty']) + ($args['infant_base_fare'] * $request['infant_qty']);
            $args['tax_fare']   = ($args['adult_tax_amount'] * $request['adult_qty']) + ($args['child_tax_amount'] * $request['child_qty']) + ($args['infant_tax_amount'] * $request['infant_qty']);
            $args['fee_fare']   = 0;
            $args['stops']      = count($sequence['segments'])-1;
            $args['sec_stop']   = $sequence['sec_stop'];//$args['stops'];
            $args['segments']   = $request['flight_qty'];

            $hash = '';
            $segmentArr = array();
            foreach($sequence['segments'] as $seg_key => $segment) {
//dump($segment);
                if($seg_key == 2) {
                    $pref = 'third';
                } elseif ($seg_key == 1) {
                    $pref = 'second';
                } else {
                    $pref = 'first';
                }

                $departure_city     = WtDB::Ref()->AirportValue(new WtMapArgs('value','city','code',$segment['DepartureAirport']));
                $arrival_city       = WtDB::Ref()->AirportValue(new WtMapArgs('value','city','code',$segment['ArrivalAirport']));
                $departure_timezone = WtDB::Ref()->CityValue(new WtMapArgs('value','timezone','code',$departure_city));
                $arrival_timezone   = WtDB::Ref()->CityValue(new WtMapArgs('value','timezone','code',$arrival_city));
                $departure_timezone = $departure_timezone != $departure_city ? $departure_timezone : '+00:00';
                $arrival_timezone   = $arrival_timezone != $arrival_city ? $arrival_timezone : '+00:00';

                $dateStart = new DateTime($segment['DepartureDateTime'] . $departure_timezone);
                $dateEnd   = new DateTime($segment['ArrivalDateTime'] . $arrival_timezone);
                $dateDiff = $dateStart->diff($dateEnd);
                $segment['totalFlightTime'] = $dateDiff->h . ':' . $dateDiff->i;

                $args['elapsed_time'] = str_replace(':','H',$segment['totalFlightTime']);
                $args['duration'] = $segment['totalFlightTime'] . ':00';

                list($departure_date, $departure_time) = explode('T', $segment['DepartureDateTime']);
                list($arrival_date, $arrival_time) = explode('T', $segment['ArrivalDateTime']);

                $segmentArr[$pref.'_departure_date']     = $departure_date;
                $segmentArr[$pref.'_departure_time']     = $departure_time;
                $segmentArr[$pref.'_departure_timezone'] = $departure_timezone;
                $segmentArr[$pref.'_arrival_date']       = $arrival_date;
                $segmentArr[$pref.'_arrival_time']       = $arrival_time;
                $segmentArr[$pref.'_arrival_timezone']   = $arrival_timezone;
                $segmentArr[$pref.'_flight_number']      = $segment['FlightNumber'];
                $segmentArr[$pref.'_departure_airport']  = $segment['DepartureAirport'];
                $segmentArr[$pref.'_departure_terminal'] = '';//$segment[''];
                $segmentArr[$pref.'_arrival_airport']    = $segment['ArrivalAirport'];
                $segmentArr[$pref.'_arrival_terminal']   = '';//$segment[''];
                $segmentArr[$pref.'_operating_airline']  = $segment['MarketingAirline'];
                $segmentArr[$pref.'_marketing_airline']  = $segment['MarketingAirline'];
                $segmentArr[$pref.'_validating_airline'] = $segment['MarketingAirline'];
                $segmentArr[$pref.'_airplane']           = $segment['Equipment'];
                $segmentArr[$pref.'_booking_class']      = $segment['MarketingCabin'];
                $segmentArr[$pref.'_service_class']      = $request['class'];

                $hash .= $segmentArr[$pref.'_departure_date'].$segmentArr[$pref.'_departure_time'].$segmentArr[$pref.'_flight_number'].$segmentArr[$pref.'_booking_class'];

                $args['hash'] = md5($hash);
                $args['message'] = $sequence['message'];
                $sectionArr = array_merge($args,$segmentArr);
            }
//dump($sectionArr);
            WtDB::Ref()->TransactionsResultInsert(new WtFuncArgs($sectionArr));
        }
        return true;
    }

    function AvailabilityAirPrice($args) {
    }

    function GetBookingRequest($args) {
        $results = WtDB::Ref()->GetTransactionsResults(new WtMapArgs('ids', explode('_', $args['result_ids'])));
        $countInf = 0;
        $Passengers = $OSIText = $SSRText = $SeatText = '';
        foreach($args['passengers'] as $key => $passenger) {
            $RPH = $key+1;
            switch ($passenger['type']) {
                case 'ADT': $passengerCategoryCode = 'ADT';
                            break;
                case 'CHD': $passengerCategoryCode = 'CNN';
                            break;
                case 'INF': $passengerCategoryCode = 'INF';
                            $countInf++;
                            break;
            }
            $AccompaniedByInfant = $passenger['type'] == 'INF' ? ' AccompaniedByInfant="false"' : '';
            $PassengerTypeQuantity = '<PassengerTypeQuantity PassengerTypeCode="' . $passengerCategoryCode . '" Quantity="1"/>';

            list($y,$m,$d) = explode('-',$passenger['birth_date']);
            $passengerBirthDate = strtoupper(date('dMy', mktime(0, 0, 0, $m, $d, $y)));

//          $SeatText = '<SeatRequest FlightRefNumberRPHList="1" TravelerRefNumberRPHList="1" SeatNumber="2A"/>';

            if ($passenger['type'] == 'INF') {
                $SSRText .= '
                    <SpecialServiceRequest SSRCode="INFT" ServiceQuantity="1" Status="NN" FlightRefNumberRPHList="1" TravelerRefNumberRPHList="' . $countInf . '">
                        <Airline Code="S7"/>
                        <Text>.' . $passenger['lastname'] . '/' . $passenger['firstname'] . ' ' . $passengerBirthDate . '</Text>
                    </SpecialServiceRequest>';
/*              $OSIText .= '
                    <OtherServiceInformation>
                        <TravelerRefNumber RPH="' . $countInf . '"/>
                        <Airline Code="S7"/>'.
//                      <Text>S7 ' . $countInf . 'INF ' . $passenger['lastname'] . '/' . $passenger['firstname'] . '/P' . $countInf . '</Text>
                        '<Text>' . $countInf . 'INF ' . $passenger['lastname'] . '/' . $passenger['firstname'] . '</Text>
                    </OtherServiceInformation>';*/
            }
            if ($passenger['type'] == 'CHD') {
                $SSRText .= '
                    <SpecialServiceRequest SSRCode="CHLD" ServiceQuantity="1" Status="HK" TravelerRefNumberRPHList="' . $RPH . '" FlightRefNumberRPHList="1">
                        <Airline Code="S7"/><Text>/' . $passengerBirthDate . '</Text>
                    </SpecialServiceRequest>';
                $passenger['title'] = $passenger['type'];
            }

            $PassengerPhone = '';
            if ($key == 0) {
                $PhoneCode = WtDB::Ref()->CountryValue(new WtMapArgs('code', $args['buyer_phonecode'], 'value', 'c.phone'));
                $PassengerPhone = '
                    <Telephone PhoneNumber="' . $args['buyer_phonenumber'] . '" PhoneTechType="1" PhoneLocationType="7" AreaCityCode="' . $PhoneCode . '"/>
                    <Email>' . $args['buyer_email'] . '</Email>
                    <Address FormattedInd="false">
                        <AddressLine>' . $passenger['address'] . '</AddressLine>
                    </Address>';
            }

            if ($passenger['type'] != 'INF') {
                $Passengers .= '
                    <AirTraveler BirthDate="' . $passenger['birth_date'] . '" PassengerTypeCode="' . $passenger['type'] . '"' . $AccompaniedByInfant . '>
                        <PersonName>
                            <NamePrefix>' . $passenger['title'] . '</NamePrefix>
                            <GivenName>' . $passenger['firstname'] . '</GivenName>
                            <Surname>' . $passenger['lastname'] . '</Surname>
                        </PersonName>' .
                        $PassengerPhone . '
                        <Document DocID="' . $passenger['doc_number'] . '" DocType="' . $passenger['doc_type'] . '" DocHolderNationality="' . $passenger['citizenship'] . '">
                            <DocHolderName>' . $passenger['title'] . ' ' . $passenger['firstname'] . ' ' . $passenger['lastname'] . '</DocHolderName>
                        </Document>' .
                        $PassengerTypeQuantity .
                        '<TravelerRefNumber RPH="' . $RPH . '"/>
                    </AirTraveler>';
            }
        }
        $countSeats = count($args['passengers']) - $countInf;

        $Segments = '';
        $FlightRefNumberRPHList = array();
        $RPH = 1;
        foreach($results as $segment) {
            for($i=0; $i <= $segment['stops']; $i++) {
                switch ($i) {
                    case 0: $pref = 'first';
                            break;
                    case 1: $pref = 'second';
                            break;
                    case 2: $pref = 'third';
                            break;
                }
                $Segments .= '
                    <FlightSegment DepartureDateTime="' . $segment[$pref.'_departure_date'] . 'T' . $segment[$pref.'_departure_time'] . '" RPH="' . $RPH . '">
                        <DepartureAirport LocationCode="' . $segment[$pref.'_departure_airport'] . '"/>
                        <ArrivalAirport LocationCode="' . $segment[$pref.'_arrival_airport'] . '"/>
                        <OperatingAirline Code="' . $segment[$pref.'_operating_airline'] . '" FlightNumber="' . $segment[$pref.'_flight_number'] . '"/>
                        <BookingClassAvails>
                            <BookingClassAvail ResBookDesigCode="' . $segment[$pref.'_booking_class'] . '" ResBookDesigStatusCode="NN" ResBookDesigQuantity="' . $countSeats .'" />
                        </BookingClassAvails>
                    </FlightSegment>';

                list($y,$m,$d) = explode('-',$segment[$pref.'_departure_date']);
                $departure_date = strtoupper(date('dM', mktime(0, 0, 0, $m, $d, $y)));
                $SSR_segments = $segment[$pref.'_departure_airport'] . $segment[$pref.'_arrival_airport'] . ' ' . $segment[$pref.'_flight_number'] . $segment[$pref.'_booking_class'] . $departure_date;
                $FlightRefNumberRPHList[] = $RPH;
                $RPH++;
            }
        }
        if (count($FlightRefNumberRPHList)>1) {
            $FlightRefNumberRPHList = implode(',',$FlightRefNumberRPHList);
            $SSRText = str_replace('FlightRefNumberRPHList="1">', 'FlightRefNumberRPHList="' . $FlightRefNumberRPHList . '">', $SSRText);
        }

//      $SeatText = '<SeatRequests>' . $SeatText . '</SeatRequests>';
        $SSRText = '<SpecialServiceRequests>' . $SSRText . '</SpecialServiceRequests>';
//      $OSIText = '<OtherServiceInformations>' . $OSIText . '</OtherServiceInformations>';

        $request = '
            <AirItinerary>
                <OriginDestinationOptions>
                    <OriginDestinationOption>'
                    . $Segments . '
                    </OriginDestinationOption>
                </OriginDestinationOptions>
            </AirItinerary>
            <TravelerInfo>'
                . $Passengers .'
                <SpecialReqDetails>' .
//                  $SeatText .
                    $SSRText .
                    $OSIText .
                    '<Remarks>
                        <Remark RPH="1">ETM-SYSTEM ORDER ' . $args['id'] . '</Remark>
                        <Remark RPH="2">ETM-SYSTEM PASSENGERS/ADT/' . $args['adult_qty'] . '/CHD/' . $args['child_qty'] . '/INF/' . $args['infant_qty'] . '</Remark>
                        <Remark RPH="3">ETM-SYSTEM ADT/FARE/' . $args['adult_base_fare']  . '/TAX/' . $args['adult_tax_amount']  . '/CURRENCY/' . $args['transaction_currency'] . '</Remark>
                        <Remark RPH="4">ETM-SYSTEM CHD/FARE/' . $args['child_base_fare']  . '/TAX/' . $args['child_tax_amount']  . '/CURRENCY/' . $args['transaction_currency'] . '</Remark>
                        <Remark RPH="5">ETM-SYSTEM INF/FARE/' . $args['infant_base_fare'] . '/TAX/' . $args['infant_tax_amount'] . '/CURRENCY/' . $args['transaction_currency'] . '</Remark>
                    </Remarks>
                </SpecialReqDetails>
            </TravelerInfo>
            <Ticketing TicketType="eTicket"/>
            <Queue QueueGroup="' . $args['rules']['queue'] . '"/>';// DateTime="2013-03-18" Text="test add queue"/>';

        return $request;
    }

    function BookFlight($args) {
        try {
            $request = $this->GetBookingRequest($args);
            $this->client->uri = 'http://www.opentravel.org/OTA/2003/05';
            $reqPayloadString = $this->POS . $request;
            $result = $this->client->OTA_AirBookRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = XML2Array::createArray($this->client->__getLastResponse());
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
            if ($this->log) {
                TicketLog::write($this->client->__getLastRequest(), 'Request SITA_AirBook:', 'AirBook');
                TicketLog::write($this->client->__getLastResponse(), 'Response SITA_AirBook:', 'AirBook');
//                TicketLog::write($result, 'SITA_AirBook:', 'AirBook');
            }

            $order = array('id' => $args['id']);

            $checkErrors = $this->CheckErrors($result['OTA_AirBookRS']);
            if (is_array($checkErrors)) {
                $order['status'] = 'E';
                $errorsTxt = '';
                $errorsMsg = '';
                if (isset($checkErrors['errors'])) {
                    $errorsTxt = 'Errors:<br>';
                    $errorsMsg = 'ERROR:\n';

                    foreach($checkErrors['errors'] as $error) {
                        $errorsTxt .= $error['Id'].': '.$error['Message'].'<br>';

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

                    foreach($checkErrors['warnings'] as $warning) {
                        $warningsTxt .= $warning['Id'].': '.$warning['Message'].'<br>';

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
                    'oid'       => $order['id'],
                    'action'    => 'order_error',
                    'details'   => array('queue_id' => $qid,
                        'error' => $errorsTxt . "<br />" . $warningsTxt,
                        'type'  => 'book')
                )));

                WtDB::Ref()->OrdersUpdate(new WtFuncArgs($order));
                return array(
                    'status' => 'error',
                    'result' => $errorsMsg . $warningsMsg
                );
            }

            $order['status']     = 'B';
            $order['pnr_number'] = $result['OTA_AirBookRS']['AirReservation']['@attributes']['BookingReferenceID'];

            WtDB::Ref()->OrdersUpdate(new WtFuncArgs($order));
// add queue
            $args   = WtDB::Ref()->ProvidersOfficeRow(new WtMapArgs('pid', $this->provider['pid'], 'type', 'T', 'default', 'Y', 'active', 'Y'));
            $module = WtDB::Ref()->ProvidersValue(new WtMapArgs('fields', 'ticket_module', 'id', $this->provider['pid']));
            WtProvider::init(new $module($args));
            if ( WtProvider::Ref()->Connect() ) {
                $res = WtProvider::Ref()->RunCommand('ZZDUSZZS7;' . $order['pnr_number'] . 'YYYYY');
            }

            return array(
                'status' => 'success',
                'result' => $order['pnr_number']
            );

        } catch (SoapFault $fault) {
            TicketLog::write($this->client->__getLastRequest(), 'Request:', 'Exception AirBook');
            TicketLog::write($this->client->__getLastResponse(), 'Response:', 'Exception AirBook');
            TicketLog::write($fault, 'Exception:', 'AirBook');
            return false;
        }
    }
}

/*
 * @deprecated
 */
class SitaProvider extends BaseProvider {

    private $client;
    private $POS;
    private $TicketPOS;

    protected $login;
    protected $password;
    protected $agency;
    protected $secure_url;
    protected $currency;
    protected $cookies;

    public function __construct($args) {
        $args = parent::__construct($args);

        $this->login       = $args['login'];
        $this->password    = $args['password'];
        $this->agency      = $args['agency'];
        $this->secure_url  = 'https://sws.sita.aero/sws/';
        $this->currency    = $args['currency'];
        $this->Office      = 'DUS900';
        $this->GroupCode   = '115';
        $this->CountryCode = 'DE';
        $this->CityCode    = 'DUS';
        $this->AirlineID   = 'S7';

        $this->SetCookies();

        $this->POS = '
            <POS>
                <Source ERSP_UserID="'.$this->login.'/'.$this->password.'" AgentSine="'.$this->agency.'" PseudoCityCode="'.$this->Office.'" AgentDutyCode="'.$this->GroupCode.'" ISOCountry="'.$this->CountryCode.'" AirlineVendorID="'.$this->AirlineID.'" AirportCode="'.$this->CityCode.'"/>
            </POS>';
        $this->TicketPOS = '
            <POS>
                <Source ERSP_UserID="'.$this->login.'/'.$this->password.'" AgentSine="'.$this->agency.'" PseudoCityCode="'.$this->Office.'" AgentDutyCode="'.$this->GroupCode.'" ISOCountry="'.$this->CountryCode.'" AirlineVendorID="'.$this->AirlineID.'" AirportCode="'.$this->CityCode.'"/>
                <Source><RequestorID Type="5" ID="42174053E"/></Source>
            </POS>';

        $this->client = new SITASoap(null, array(
            'soap_version' => SOAP_1_1,
            'location'     => $this->secure_url,
            'uri'          => 'http://www.opentravel.org/OTA/2003/05',
            'trace'        => 1 // need for $this->client->__getLastResponse() !!!!!!!
        ));

        $this->log = true;
        if ($this->log) {
            TicketLog::Ref()->set_log_file(WtSession::Ref()->sid());
        }
    }

    function SetCookies() {
        $this->cookies = WtSession::Ref()->cookies;
    }

    function Connect() {
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
    function RunCommand($command, $log=true) {
        if ($log) TicketLog::write($command, "command", 'RunCommand');
        try {
            $command = str_replace(';',"\r",$command);
            $this->client->uri = 'http://www.opentravel.org/OTA/2003/05';
            $reqPayloadString = $this->POS . '<ScreenEntry>' . $command . '</ScreenEntry>';

            $this->client->actionAttr['OTA_ScreenTextRQ'] = array(
//'TransactionIdentifier' => WtSession::Ref()->sid(),
//'EchoToken' => WtSession::Ref()->sid(),
                'QuantityGroup'         => '2',
//              'OmitBlankLinesIndicator' => 'true',
//              'MergeScreenIndicator'  => 'true'
            );

            $result = $this->client->OTA_ScreenTextRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = XML2Array::createArray($this->client->__getLastResponse());
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
            if ($this->log) {
                TicketLog::write($this->client->__getLastRequest(), 'Request OTA_ScreenText:', 'RunCommand');
                TicketLog::write($this->client->__getLastResponse(), 'Response OTA_ScreenText:', 'RunCommand');
//                TicketLog::write($result, 'OTA_ScreenText:', 'RunCommand');
            }
            if ($log) TicketLog::write($result, "result", 'RunCommand');

            $response = array();
            $text = is_array($result['OTA_ScreenTextRS']['TextScreens']['TextScreen']['TextData']) ? $result['OTA_ScreenTextRS']['TextScreens']['TextScreen']['TextData'] : array($result['OTA_ScreenTextRS']['TextScreens']['TextScreen']['TextData']);
            foreach($text as $line) {
                $response[] = $line;
            }
//$response[] = serialize($result);
            return isset($result['OTA_ScreenTextRS']['Success']) ? $response : false;
        } catch (SoapFault $fault) {
            TicketLog::write($this->client->__getLastRequest(), 'Request:', 'Exception RunCommand');
            TicketLog::write($this->client->__getLastResponse(), 'Response:', 'Exception RunCommand');
            TicketLog::write($fault, 'Exception:', 'RunCommand');
            return false;
        }
    }

    function GetBook($log = false, $extended = true) {
        try {
            $reqPayloadString = $this->POS . '<UniqueID Type="0" ID="' . $this->pnrNumber . '"/>';
            $result = $this->client->OTA_ReadRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = XML2Array::createArray($this->client->__getLastResponse());
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
            if ($this->log) {
                TicketLog::write($this->client->__getLastRequest(), 'Request OTA_Read:', 'GetBook');
                TicketLog::write($this->client->__getLastResponse(), 'Response OTA_Read:', 'GetBook');
                TicketLog::write($result, 'OTA_Read:', 'GetBook');
            }

            if (isset($result['OTA_AirBookRS']['Errors'])) {
                $this->PNR_text = $result['OTA_AirBookRS']['Errors']['Error']['@value'];
                return;
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
                    $this->PNR['passengers'][] = array(
                        'type'      => $passenger['@attributes']['PassengerTypeCode'],
                        'title'     => $passenger['PersonName']['NamePrefix'],
                        'firstname' => $passenger['PersonName']['GivenName'],
                        'lastname'  => $passenger['PersonName']['Surname'],
                        'birthdate' => $passenger['@attributes']['BirthDate'],
//                      'email'     => $passenger['Email'],
//                      'phone'     => $passenger['Telephone']['@attributes']['PhoneNumber'],
//                      'address'   => $passenger['Address']['AddressLine']
                    );
                }

                $this->PNR['pnr_number'] = $this->pnrNumber;
            }
        } catch (SoapFault $fault) {
            TicketLog::write($this->client->__getLastRequest(), 'Request:', 'Exception GetBook');
            TicketLog::write($this->client->__getLastResponse(), 'Response:', 'Exception GetBook');
            TicketLog::write($fault, 'Exception:', 'GetBook');
            return false;
        }
    }

    public function GetFare() {
        return array('error' => true);
    }

    /**
     * Get fare quotation by PNR
     * @param boolean $bestBuy if true get fare quotation with best buy
     * @return array
     * @access public
     */
    public function GetFareQuotation($bestBuy = true) {
        $this->initialize();

        $logParams = array_intersect_key($this->order, array_fill_keys(array('id','type','provider_id','transaction_currency','customer_currency','class','payment_method','base_fare','tax_fare','fee','total_fare'),1));
        TicketLog::write($logParams, 'start fare quotation with params:', 'GetFareQuotation');

//        if ( $this->order['currency'] != $this->provider['currency'] )
//            $this->rate = $this->GetRate($this->order['currency'], $this->provider['currency'], false);

        $Response['pnr'] = $this->PNR;
//        $Response['output'] = $command->GetTextResponse();
        return $Response;
    }

    /**
     * Ticketing book
     * @param boolean $bestBuy if true get fare quotation with best buy
     * @param string $CCNumber credit card number
     * @return array
     * @access public
     */
    public function TicketingBook($bestBuy = true, $CCNumber = '') {
        $this->initialize();

        $logParams = array_intersect_key($this->order, array_fill_keys(array('id','type','provider_id','transaction_currency','customer_currency','class','payment_method','base_fare','tax_fare','fee','total_fare'),1));
        TicketLog::write($logParams, 'start ticketing with params:', 'Ticketing');

        try {
            $this->Tickets = $this->order['passengers'];
            $this->PNR['segments'] = WtDB::Ref()->TransactionsResultRows(new WtFuncArgs(array(
                'fields'    => 'first_booking_class, message',
                'orderid'   => $this->order['id'],
                'order'     => 'direction_ind'
            )));

            $return_result = array();
            $RPH = $countInfant = 0;
            foreach($this->Tickets as $ticket) {
                if ($ticket['type'] == 'INF') {
                    $countInfant++;
                } else {
                    $RPH++;
                }
                $TPA_Extensions = '';
                foreach($this->PNR['segments'] as $segment) {
                    $info   = unserialize($segment['message']);
                    foreach($info as $key => $passenger) {
                        $price  = $passenger['ota:AirItineraryPricingInfo']['ota:PTC_FareBreakdowns']['ota:PTC_FareBreakdown'];
                        $ext    = $passenger['ota:AirItineraryPricingInfo']['ota:FareInfos']['ota:FareInfo']['ota:TPA_Extensions']['sita:SITA_FareInfoExtension'];
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

                        $Taxes = '';
                        if (isset($price['ota:PassengerFare']['ota:Taxes'])) {
                            $taxList = isset($price['ota:PassengerFare']['ota:Taxes']['ota:Tax']['@value']) ? array($price['ota:PassengerFare']['ota:Taxes']['ota:Tax']) : $price['ota:PassengerFare']['ota:Taxes']['ota:Tax'];
                            foreach($taxList as $tax) {
                                $Taxes .= '<Tax Amount="' . $tax['@attributes']['Amount'] . '" CurrencyCode="' . $tax['@attributes']['CurrencyCode'] . '" TaxCode="' . $tax['@attributes']['TaxCode'] . '"/>';
                            }
                        }
                        break;
                    }

                    $TPA_Extensions .= '
                    <PTC_FareBreakdown PricingSource="' . $price['@attributes']['PricingSource'] . '">
                        <PassengerTypeQuantity Code="' . $price['ota:PassengerTypeQuantity']['@attributes']['Code'] . '"/>
                        <FareBasisCodes>
                            <FareBasisCode>' . $price['ota:FareBasisCodes']['ota:FareBasisCode'] . '</FareBasisCode>
                        </FareBasisCodes>
                        <PassengerFare>
                            <BaseFare Amount="' . $price['ota:PassengerFare']['ota:BaseFare']['@attributes']['Amount'] . '" CurrencyCode="' . $price['ota:PassengerFare']['ota:BaseFare']['@attributes']['CurrencyCode'] . '"/>
                            <EquivFare Amount="' . $price['ota:PassengerFare']['ota:EquivFare']['@attributes']['Amount'] . '" CurrencyCode="' . $price['ota:PassengerFare']['ota:EquivFare']['@attributes']['CurrencyCode'] . '"/>
                            <Taxes>' . $Taxes . '</Taxes>
                            <TotalFare Amount="' . $price['ota:PassengerFare']['ota:TotalFare']['@attributes']['Amount'] . '" CurrencyCode="' . $price['ota:PassengerFare']['ota:TotalFare']['@attributes']['CurrencyCode'] . '"/>
                            <UnstructuredFareCalc>' . $price['ota:PassengerFare']['ota:UnstructuredFareCalc'] . '</UnstructuredFareCalc>
                            <TPA_Extensions>
                                <SITA_FareInfoExtension FareRPH="' . $ext['@attributes']['FareRPH'] . '" RuleNumber="' . $ext['@attributes']['RuleNumber'] . '" TariffNumber="' . $ext['@attributes']['TariffNumber'] . '">
                                    <SubjectToGovtApproval>false</SubjectToGovtApproval>
                                    <References>
                                        <Ref1>' . htmlentities($ext['sita:References']['sita:Ref1']) . '</Ref1>
                                        <Ref2>' . htmlentities($ext['sita:References']['sita:Ref2']) . '</Ref2>
                                    </References>
                                    <Directionality Code="' . $ext['sita:Directionality']['@attributes']['Code'] . '"/>
                                </SITA_FareInfoExtension>
                            </TPA_Extensions>
                        </PassengerFare>
                    </PTC_FareBreakdown>';
                }

                $this->client->uri = 'http://www.opentravel.org/OTA/2003/05';
                $reqPayloadString = $this->TicketPOS . '
                    <DemandTicketDetail>' . $Passengers . '
                        <TPA_Extensions>' . $TPA_Extensions . '</TPA_Extensions>
                        <PaymentInfo PaymentType="1"/>
                        <BookingReferenceID ID="' . $this->pnrNumber . '" Type="14"/>
                    </DemandTicketDetail>';

                $result = $this->client->SITA_AirDemandTicketRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
                $result = XML2Array::createArray($this->client->__getLastResponse());
                $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
                if ($this->log) {
                    TicketLog::write($this->client->__getLastRequest(), 'Request SITA_AirDemandTicket:', 'TicketingBook');
                    TicketLog::write($this->client->__getLastResponse(), 'Response SITA_AirDemandTicket:', 'TicketingBook');
                    TicketLog::write($result, 'SITA_AirDemandTicket:', 'TicketingBook');
                }

                if (isset($result['SITA_AirDemandTicketRS']['Success'])) {
                    $return_result[] = $result['SITA_AirDemandTicketRS']['TicketItemInfo'];
                } else {
                    $error = $result['SITA_AirDemandTicketRS']['Errors']['Error']['@attributes']['Code'] . ' (' .
                             $result['SITA_AirDemandTicketRS']['Errors']['Error']['@attributes']['Type'] . ') - ' .
                             $result['SITA_AirDemandTicketRS']['Errors']['Error']['@value'];
                    throw new TicketServerException("Tickets not found\n" . $error, WtTicketServerException::GABRIEL_ERROR);
                }
            }
            return $this->SetResults($return_result);

        } catch (SoapFault $fault) {
            TicketLog::write($this->client->__getLastRequest(), 'Request:', 'Exception TicketingBook');
            TicketLog::write($this->client->__getLastResponse(), 'Response:', 'Exception TicketingBook');
            TicketLog::write($fault, 'Exception:', 'TicketingBook');
            return false;
        }
    }

    private function SetResults($NumTickets) {
        $this->GetBook();

        $route = '';
        foreach ($this->PNR['coupons'] as $segment) {
            $route .= $segment['departure_airport'] . '-' . $segment['arrival_airport'] . ';';
        }

        foreach ($this->Tickets as &$ticket) {
            if (isset($NumTickets['@attributes']['TicketNumber'])) $NumTickets = array($NumTickets);
            foreach ($NumTickets as $fa) {

                if ($fa['PassengerName']['GivenName'] == $ticket['firstname'] &&
                    $fa['PassengerName']['Surname'] == $ticket['lastname']) {
                    $ticket['eticket']  = $fa['@attributes']['TicketNumber'];
                    $ticket['totalsum'] = $fa['@attributes']['TotalAmount'];
                    $ticket['coupons']  = $this->PNR['coupons'];
                    $ticket['response'] = serialize($fa);
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

        $Response['output']         = $this->PNR_text;
        $Response['tickets']        = $this->Tickets;
//      $Response['payment_card']   = $payment_card;
//      $Response['warnings']       = $this->warnings;
        return $Response;
    }

    public function VoidBook($init = true) {
        try {
            $reqPayloadString = $this->POS . '<UniqueID Type="15" ID="' . $this->pnrNumber . '" Reason="Cancel PNR for RXA unit testing."/>';
            $result = $this->client->OTA_CancelRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = XML2Array::createArray($this->client->__getLastResponse());
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
            if ($this->log) {
                TicketLog::write($this->client->__getLastRequest(), 'Request OTA_Cancel:', 'VoidBook');
                TicketLog::write($this->client->__getLastResponse(), 'Response OTA_Cancel:', 'VoidBook');
                TicketLog::write($result, 'OTA_Cancel:', 'VoidBook');
            }

            if (isset($result['OTA_CancelRS']['Success'])) {
                return 'ITINERARY CANCELLED';
            } else {
                return $result['OTA_CancelRS']['Errors']['Error']['@attributes']['Code'] . ' (' .
                       $result['OTA_CancelRS']['Errors']['Error']['@attributes']['Type'] . ') - ' .
                       $result['OTA_CancelRS']['Errors']['Error']['@value'];
            }
        } catch (SoapFault $fault) {
            TicketLog::write($this->client->__getLastRequest(), 'Request:', 'Exception VoidBook');
            TicketLog::write($this->client->__getLastResponse(), 'Response:', 'Exception VoidBook');
            TicketLog::write($fault, 'Exception:', 'VoidBook');
            return false;
        }
    }

    public function VoidTicket($ticketNumber) {
        try {
            $reqPayloadString = $this->TicketPOS . '<UniqueID Type="30" ID="' . $ticketNumber . '"/>';
            $result = $this->client->OTA_CancelRQ(new SoapVar($reqPayloadString, XSD_ANYXML));
            $result = XML2Array::createArray($this->client->__getLastResponse());
            $result = $result['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
            if ($this->log) {
                TicketLog::write($this->client->__getLastRequest(), 'Request OTA_Cancel:', 'VoidTicket');
                TicketLog::write($this->client->__getLastResponse(), 'Response OTA_Cancel:', 'VoidTicket');
                TicketLog::write($result, 'OTA_Cancel:', 'VoidTicket');
            }

            if (isset($result['OTA_CancelRS']['Success'])) {
                return $result;
            } else {
                return array(
                    'status'  => 'error',
                    'message' => $result['OTA_CancelRS']['Errors']['Error']['@attributes']['Code'] . ' (' .
                                 $result['OTA_CancelRS']['Errors']['Error']['@attributes']['Type'] . ') - ' .
                                 $result['OTA_CancelRS']['Errors']['Error']['@value']
                );
            }
        } catch (SoapFault $fault) {
            TicketLog::write($this->client->__getLastRequest(), 'Request:', 'Exception VoidTicket');
            TicketLog::write($this->client->__getLastResponse(), 'Response:', 'Exception VoidTicket');
            TicketLog::write($fault, 'Exception:', 'VoidTicket');
            return false;
        }
    }

    public function GetDataForRevalidationTicket($ticketNumber) {
    }

    public function RevalidationTicket(WtFuncArgs $args) {
    }

    public function GetDataForExchangeTicket($ticketNumber) {
    }

    public function ExchangeTicket(WtFuncArgs $args) {
    }

    public function GetDataForRefundTicket($ticketNumber) {
    }

    public function RefundTicket(WtFuncArgs $args) {
    }
}

?>
