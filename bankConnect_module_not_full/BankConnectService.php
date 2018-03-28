<?php

namespace BankGateway\Service;

define('CUSTOMER_PRIVATE_KEY', ROOT_PATH . '/data/BankConnect/erp_xyz.pkcs8');
define('CUSTOMER_REQUEST_CERT', ROOT_PATH . '/data/BankConnect/pkcs10.csr');

//define('ENCRYPT_BANK_CONNECT_CERT', ROOT_PATH . '/data/BankConnect/bank_connect.cer');
//define('ENCRYPT_BANK_CONNECT_CERT', ROOT_PATH . '/data/BankConnect/bank_connect_test.cer');
define('ENCRYPT_BANK_CONNECT_CERT', ROOT_PATH . '/data/BankConnect/GBC_Certificate.cer');
//define('CUSTOMER_BANK_CERT', ROOT_PATH . '/data/BankConnect/bank.cer');
define('CUSTOMER_BANK_CERT', ROOT_PATH . '/data/BankConnect/erp_xyz.cer');

use Symfony\Component\Filesystem\Filesystem;

use BankGateway\Entity\BankGateway;
use BankGateway\Tool\WSSESoap;

use Common\Service\AbstractSimpleService;

use Payments\Entity\PaymentFileImport;
use Payments\Controller\SydbankGatewayController;

use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecurityDSig;

use \SoapClient;
use \DateTime;
use \SoapHeader;
use \SoapParam;
use \SoapFault;
use \DOMDocument;
	
class WSSESoapClient extends SoapClient
{
    function __doRequest($request, $location, $action, $version, $one_way = null)
    {
	//        file_put_contents(ROOT_PATH."/data/BankConnect/request/unencrypted.xml", $request);
        if (in_array($action, ['urn:CorporateService:getBankCertificate'])) {
            $retVal = parent::__doRequest($request, $location, $action, $version, $one_way);

            return $retVal;
        }

        $doc = new DOMDocument('1.0');
        $doc->loadXML($request);

        $objWSSE = new WSSESoap($doc);

        if (!in_array($action, ['urn:CorporateService:activateServiceAgreement'])) {
            /* create new XMLSec Key using AES256_CBC and type is private key */
            $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'private']);

            /* load the private key from file - last arg is bool if key in file (true) or is string (false) */
            $objKey->loadKey(CUSTOMER_PRIVATE_KEY, true);

            /* Sign the message - also signs appropiate WS-Security items */
            $options = [
                "KeyInfo" => [
                    "insertBefore" => false
                ],
                'algorithm' => XMLSecurityDSig::SHA256
            ];

            $objWSSE->signSoapDoc($objKey, $options);

            /* Add certificate (BinarySecurityToken) to the message */
            $token = $objWSSE->addBinaryToken(file_get_contents(CUSTOMER_BANK_CERT));

            /* Attach pointer to Signature */
            $objWSSE->attachTokentoSig($token);
        }

        if (!in_array($action, ['urn:CorporateService:getCustomerStatement'])) {
            $objKey = new XMLSecurityKey(XMLSecurityKey::AES256_CBC);
            $objKey->generateSessionKey();

            $siteKey = new XMLSecurityKey(XMLSecurityKey::RSA_OAEP_MGF1P, ['type' => 'public']);

            $siteKey->loadKey(ENCRYPT_BANK_CONNECT_CERT, true, true);

            $options = [
                "KeyInfo" => [
                    "X509SubjectKeyIdentifier" => true,
                ]
            ];

            $objWSSE->encryptSoapDoc($siteKey, $objKey, $options, false);
        }

//        file_put_contents(ROOT_PATH."/data/BankConnect/request/encrypted.xml", $objWSSE->saveXML());
        $retVal = parent::__doRequest($objWSSE->saveXML(), $location, $action, $version, $one_way);

        $doc = new DOMDocument();
        $doc->loadXML($retVal);

        $options = [
            "keys" => [
                "private" => [
                    "key" => CUSTOMER_PRIVATE_KEY,
                    "isFile" => true,
                    "isCert" => false
                ]
            ]
        ];

        $objWSSE->decryptSoapDoc($doc, $options);

        return $doc->saveXML();
    }
}

class BankConnectService extends AbstractSimpleService
{
    /**
     * @var BankGateway $lastEntity
     */
    private $lastEntity;

    /**
     * @var WSSESoapClient $soapClient
     */
    private $soapClient;
    private $config = [];
    private $keys;
    private $activeCertificatePath;

    private function setLastEntity($entity)
    {
        $this->lastEntity = $entity;
    }

    /**
     * @return BankGateway
     */
    public function getLastEntity()
    {
	    return $this->lastEntity;
    }

    private function parseResponse($response)
    {
        $em = $this->getEntityManager();

        $obj = $this->getLastEntity();
        $obj->setRequest($this->soapClient->__getLastRequest());
        $obj->setResponse($this->soapClient->__getLastResponse());
        $obj->setProcessTime(microtime(true) - $obj->getProcessTime());

        $em->flush();

        if (property_exists($response, "corporateMessage")) {
            $corporateMessage = (array)$response->corporateMessage;

            if (isset($corporateMessage['compressed']) && $corporateMessage['compressed']) {
                $output = gzdecode($corporateMessage['content']);
            } else {
                $output = $corporateMessage['content'];

                $strpos = strpos($output, '-----BEGIN CERTIFICATE-----', 1);

                if ($strpos > 0) {
                    $output = substr($output, strpos($output, '-----BEGIN CERTIFICATE-----', $strpos - 27));
                }
            }
        } else if (property_exists($response, "paymentResponse")) {
            if ($response->paymentResponse->responseCode == 200) {
                return true;
            } else {
                return $response->paymentResponse->message;
            }
        } else {
            throw new Exception('Invalid response');
        }

        return $output;
    }

    private function sendRequest($action, $header = [], SoapParam $body = null)
    {
        $em = $this->getEntityManager();
        $obj = new BankGateway();
        $obj->setGatewayType('BankConnect');

        $this->setLastEntity($obj);

        try {

   		    $this->soapClient = new WSSESoapClient( ROOT_PATH . '/data/BankConnect/bankconnect.wsdl' /* $this->config['get_url'] */ , [
                'connection_timeout' => $this->config['timeout'],
                'soap_version' => SOAP_1_1,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'trace' => 1
            ]);

            $this->soapClient->__setSoapHeaders($header);

            if (!$body) {
                $body = [];
            }

            $obj->setProcessTime(microtime(true));
            $em->persist($obj);
            $em->flush();

            return $this->parseResponse($this->soapClient->$action($body));		
			
        } catch (SoapFault $e) {
		
            $obj->setProcessTime(microtime(true));
            $obj->setResponse($e->getMessage());
            $em->persist($obj);
            $em->flush();
            return "Error: " . $e->getMessage();
        }
    }

    public function createKeyPair()
    {
        $dn = [
            'countryName' => 'DK',
//            'stateOrProvinceName' => 'DK',
//            'localityName' => 'DK',
            'organizationName' => 'Bank Connect',
            'organizationalUnitName' => 'Bank Connect',
            'commonName' => $this->getCommonName(),
//            'emailAddress' => ' '
        ];

        $config = [
            'digest_alg' => 'sha256',
            'private_key_bits' => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA
        ];

        $resource = openssl_pkey_new($config);
        $csr = openssl_csr_new($dn, $resource, $config);

        openssl_pkey_export($resource, $privateKey);
        openssl_csr_export($csr, $csrStr);

        file_put_contents(CUSTOMER_PRIVATE_KEY, $privateKey);
        file_put_contents(CUSTOMER_REQUEST_CERT, $csrStr);
    }

    private function getCommonName()
    {
        $function_id = $this->getFunctionId();
        $activation_code = $this->getActivationCode();

        $commonName = $function_id . '@' . $activation_code;

        return $commonName;
    }

    private function getMessageId()
    {
        $endToEndMessageId = md5(date("c"));

        return $endToEndMessageId;
    }

    private function getFunctionId()
    {
        return $this->config['functionIdentification'];
    }

    private function getActivationCode()
    {
        return $this->config['activationCode'];
    }

    private function getPKCS10Request()
    {
        $pem2der = function ($pem_data) {
            $begin = "REQUEST-----";
            $end = "-----END";

            $pem_data = substr($pem_data, strpos($pem_data, $begin) + strlen($begin));
            $pem_data = substr($pem_data, 0, strpos($pem_data, $end));

            $der = base64_decode($pem_data);

            return $der;
        };

        return $pem2der(file_get_contents(CUSTOMER_REQUEST_CERT));
    }

    protected function getActivationHeader()
    {
        $timestamp = new DateTime();

        $data = [
            'organisationIdentification' => [
                'mainRegistrationNumber' => $this->config['mainRegistrationNumber'],
                'isoCountryCode' => $this->config['isoCountryCode'],
            ],
            'erpInformation' => [],
            'functionIdentification' => $this->getFunctionId(),
            'endToEndMessageId' => $this->getMessageId(),
            'createDateTime' => $timestamp->format('c'),
        ];

        $activationHeader = new SoapHeader($this->config['ns'], 'activationHeader', $data);

        return $activationHeader;
    }

    protected function getServiceHeader()
    {
        $timestamp = new DateTime();

        $data = [
            'organisationIdentification' => [
                'mainRegistrationNumber' => $this->config['mainRegistrationNumber'],
                'isoCountryCode' => $this->config['isoCountryCode'],
            ],
            'erpInformation' => [
                'erpsystem' => 'Simbo ERP',
                'erpversion' => '1.0',
            ],
            'format' => 'ISO20022',
            'functionIdentification' => $this->getFunctionId(),
            'endToEndMessageId' => $this->getMessageId(),
            'createDateTime' => $timestamp->format('c'),
        ];

        $serviceHeader = new SoapHeader($this->config['ns'], 'serviceHeader', $data, false);

        return $serviceHeader;
    }

    protected function getTechnicalAddress()
    {
        $data = [
            'ipAddress' => '127.0.0.1'
        ];

        $technicalAddressHeader = new SoapHeader($this->config['ns'], "technicalAddress", $data, false);

        return $technicalAddressHeader;
    }

    protected function getCertificateRequest()
    {
        $data = $this->keys['csrPlain'];

        $serviceHeader = new SoapHeader($this->config['ns'], 'certificateRequest', $data, false);

        return $serviceHeader;
    }

    protected function getActivationAgreement()
    {
        $activationAgreement = [
            'activationAgreement' => [
                'activationCode' => $this->getActivationCode(),
                'certificateRequest' => $this->getPKCS10Request(),
            ]
        ];

        return $activationAgreement;
    }

    protected function getRenewCustomerCertificate()
    {
        $renewCustomerCertificate = [
            'certificateRequestMessage' => [
                'certificateRequest' => $this->getPKCS10Request(),
                'signature' => file_get_contents(CUSTOMER_BANK_CERT)
            ]
        ];

        return $renewCustomerCertificate;
    }

    protected function getPaymentMessage($content)
    {
        $size = strlen($content);

        if ($size >= 5242880) {
            $paymentMessageContent = gzencode($content, 9);
            $compressed = 1;
        } else {
            $paymentMessageContent = $content;
            $compressed = 0;
        }

        $paymentMessage = [
            'paymentMessage' => [
                'format' => 'ISO20022',
                'mimeType' => 'text/xml',
                'compressed' => $compressed,
                'content' => $paymentMessageContent,
            ]
        ];

        return $paymentMessage;
    }

    public function getBankCertificate()
    {
        if (!file_exists(CUSTOMER_PRIVATE_KEY) || !file_exists(CUSTOMER_REQUEST_CERT)) {
            $this->createKeyPair();
        }

        $response = $this->sendRequest('getBankCertificate',
            [
                $this->getTechnicalAddress(),
                $this->getActivationHeader()
            ]
        );

        file_put_contents(ENCRYPT_BANK_CONNECT_CERT, $response);

        return $response;//file_exists(ENCRYPT_BANK_CONNECT_CERT);
    }

    public function activateServiceAgreement()
    {
        if (!file_exists(ENCRYPT_BANK_CONNECT_CERT)) {
            $this->getBankCertificate();
        }

        $activationAgreementArray = $this->getActivationAgreement();

        $body = new SoapParam($activationAgreementArray, 'activationAgreement');

        $response = $this->sendRequest('activateServiceAgreement',
            [
                $this->getTechnicalAddress(),
                $this->getActivationHeader(),
            ],
            $body
        );

        file_put_contents(CUSTOMER_BANK_CERT, $response);

        return $response;//file_exists(CUSTOMER_BANK_CERT);
    }

    /*
    public function renewCustomerCertificate()
    {
        $this->createKeyPair();

        if (!file_exists(ENCRYPT_BANK_CONNECT_CERT)) {
            $this->getBankCertificate();
        }

        $renewCustomerCertificateArray = $this->getRenewCustomerCertificate();
        $body = new SoapParam($renewCustomerCertificateArray, 'certificateRequest');

        $response = $this->sendRequest("renewCustomerCertificate",
            [
                $this->getServiceHeader(),
                $this->getTechnicalAddress(),
                $this->getCertificateRequest(),
            ],
            $body
        );

//        file_put_contents(BANK_CONNECT_CERT, $response);
    }
    */

    public function transferPayments($paymentOrders)
    {
        if (!file_exists(CUSTOMER_BANK_CERT)) {
            $this->activateServiceAgreement();
        }

        $paymentMessage = $this->getPaymentMessage($paymentOrders);
        $body = new SoapParam($paymentMessage, 'paymentMessage');
		
		$ServiceHeader = $this->getServiceHeader();
		$TechnicalAddress = $this->getTechnicalAddress();

        $response = $this->sendRequest("transferPayments",
            [
                $this->getServiceHeader(),
                $this->getTechnicalAddress(),
            ],
            $body
        );

        return $response;
    }

    public function getCustomerStatement($return = false)
    {
        if (!file_exists(CUSTOMER_BANK_CERT)) {
            $test = $this->activateServiceAgreement();
        }

        $this->getServiceHeader();
        $this->getTechnicalAddress();

		$response = $this->sendRequest("getCustomerStatement",
            [
                $this->getServiceHeader(),
                $this->getTechnicalAddress(),
            ]
        );

        return $response;
    }

    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }
}

