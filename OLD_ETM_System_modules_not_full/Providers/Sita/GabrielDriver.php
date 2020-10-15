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


define('SITA_KEYS_PATH', 'lib/Providers/Sita/keys/');

class WtGabrielDriver extends SoapClient {

    public $actionAttr = array();
    public $log = true; 

    function __doRequest($request, $location, $saction, $version) {

//       dumpLog ($saction, 'transaction: ', 'sitaTransactions.log');

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
        $objKey->loadKey(SITA_PRIVATE_KEY, TRUE);

        /* Sign the message - also signs appropraite WS-Security items */
        $objWSSE->signSoapDoc($objKey);

        /* Add certificate (BinarySecurityToken) to the message and attach pointer to Signature */
        $token = $objWSSE->addBinaryToken(file_get_contents(SITA_CERT_FILE));
        $objWSSE->attachTokentoSig($token);

        $request = $objWSSE->saveXML();

        if ($this->log) {
            TicketLog::write($request, 'Request:', $saction);
        }

//        TicketLog::write($request, 'WSSE Request:', $saction);

        return parent::__doRequest($request, $location, $saction, $version);
    }
}

?>
