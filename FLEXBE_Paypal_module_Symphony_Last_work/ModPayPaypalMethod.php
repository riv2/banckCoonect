<?php

namespace Framework\Src\Common\Service\Admin\Land\Module\Pay\PaymentMethod;

use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\Request;
use Framework\Src\Common\Models\Backend\Entity\Bill\BillPayEntity;

/**
 * Class ModPayPaypalMethod
 * @package Framework\Src\Common\Service\Admin\Land\Module\Pay\PaymentMethod
 */
class ModPayPaypalMethod extends ModPayAbstractMethod
{
    public $type = 'paypal'; // ТИП ПЛАТЕЖКИ ДЛЯ ЭКВАРИНГА.

    public $support_types = [ // нужно для формирования списка платежек - ModPayCollection
        'paypal' => true,
    ];

    public $support_currency = [
        'AUD',
        'BRL',
        'CAD',
        'CZK',
        'DKK',
        'EUR',
        'HKD',
        'HUF',
        'INR',
        'ILS',
        'JPY',
        'MYR',
        'MXN',
        'TWD',
        'NZD',
        'NOK',
        'PHP',
        'PLN',
        'GBP',
        'RUB',
        'SGD',
        'SEK',
        'CHF',
        'THB',
        'USD',
    ]; // Список поддерживаемых валют у paypal.

    private const MOD_PAY_PAYPAL_LINK         = 'https://www.paypal.com/cgi-bin/webscr';  // Ссылка для оплаты бой.
    private const MOD_PAY_PAYPAL_LINK_SANDBOX = 'https://www.sandbox.paypal.com/cgi-bin/webscr'; // Ссылка для оплаты тест.
    private const MOD_PAY_PAYPAL_CERT         = '/config/acquiring/cacert.pem'; //Фаил сертификата для api https://github.com/paypal/ipn-code-samples/tree/master/php/cert
    private const MOD_PAY_PAYPAL_API          = 'https://api-3t.paypal.com/nvp'; // Сылка на npv api бой.
    private const MOD_PAY_PAYPAL_API_SANDBOX  = 'https://api-3t.sandbox.paypal.com/nvp';  // Сылка на npv api тест.
    private const MOD_PAY_PAYPAL_VERSION      = '74.0'; // Версия api

    private const MOD_PAY_PAYPAL_DEBUG_CHANEL   = '#dev_log'; // куда слать логи.
    private const MOD_PAY_PAYPAL_STATUS_SUCCESS = 'Success'; // статус для ACK

    private const MOD_PAY_PAYPAL_RESPONSE_TRANSATION_ID = 'PAYMENTINFO_0_TRANSACTIONID'; // Номер транзакции
    private const MOD_PAY_PAYPAL_RESPONSE_CURRENCY      = 'L_CURRENCYCODE0'; // Основная валюта счета в платежке

    private const MOD_PAY_PAYPAL_REQUEST_PAYMENTACTION = 'PAYMENTREQUEST_0_PAYMENTACTION'; // действие что нудно сделать с заказом.
    private const MOD_PAY_PAYPAL_REQUEST_RETURNURL     = 'RETURNURL'; // ссылка при успешной оплате - обратно на проект, нужно для дальнейшей оплате на проекте.
    private const MOD_PAY_PAYPAL_REQUEST_CANCELURL     = 'CANCELURL'; // ссылка если будет ошибка или отмена оплаты.
    private const MOD_PAY_PAYPAL_REQUEST_AMT           = 'PAYMENTREQUEST_0_AMT'; // Общая Стоимость заказа
    private const MOD_PAY_PAYPAL_REQUEST_SHIPPINGAMT   = 'PAYMENTREQUEST_0_SHIPPINGAMT'; // Стоимость доставки заказа
    private const MOD_PAY_PAYPAL_REQUEST_CURRENCYCODE  = 'PAYMENTREQUEST_0_CURRENCYCODE'; // Валюта заказа.
    private const MOD_PAY_PAYPAL_REQUEST_ITEMAMT       = 'PAYMENTREQUEST_0_ITEMAMT'; //Стоимость еденицы заказа.
    private const MOD_PAY_PAYPAL_REQUEST_NAME          = 'L_PAYMENTREQUEST_0_NAME0'; // имя заказа
    private const MOD_PAY_PAYPAL_REQUEST_DESC          = 'L_PAYMENTREQUEST_0_DESC0'; // описание заказа
    private const MOD_PAY_PAYPAL_REQUEST_L_AMT         = 'L_PAYMENTREQUEST_0_AMT0'; // Итоговая сумма для перевода
    private const MOD_PAY_PAYPAL_REQUEST_QTY           = 'L_PAYMENTREQUEST_0_QTY0'; //Количество заказанных единиц

    private const MOD_PAY_PAYPAL_REQUEST_TOKEN = 'TOKEN'; // Токен который приходит от платежки для дальнейших действий с платежкой, оплата, отмета, возврат, получить инфу.
    private const MOD_PAY_PAYPAL_REQUEST_ACK   = 'ACK'; // ответ от сервера

    private const MOD_PAY_PAYPAL_PAYMENTACTION = 'Sale'; // Действие в платежке - продажа.

    // Ошибки
    private const MOD_PAY_PAYPAL_RESPONSE_MSG     = 'L_SHORTMESSAGE0'; // Ловим ошибки.
    private const MOD_PAY_PAYPAL_ERROR_RESTRICTED = 'Restricted account'; // Ошибка - аккаунт не активирован.

    private const SETTING_MODULE_SEND_TO_USER_ERROR_MSG = false; // Настройка расслыки ошибок для владельца сайта.

    private $request; // Приходяшие данные от платежки.

    /**
     * ModPayPaypalMethod constructor.
     */
    public function __construct()
    {
        $this->request = Request::createFromGlobals();
    }

    /**
     * @param int    $pay_id
     * @param string $type
     * @param array  $data
     *
     * @return bool
     */
    public function payment($pay_id = 0, $type = '', $data = [])
    {
        // Поддерживается ли тип
        if (!$this->isSupportedType($type))
        {
            return false;
        }

        // Вытаскиваем счёт
        $pay = $this->getPay($pay_id);

        if (!isset($pay['pay_id']))
        {
            return false;
        }

        $this->debugPay($pay_id, true); // Помечаем как тестовую платежку.
        $link = self::MOD_PAY_PAYPAL_LINK_SANDBOX;

        if ($this->config['debug'] === 'no')
        {
            $this->debugPay($pay_id, false);  // Помечаем как боевую платежку.
            $link = self::MOD_PAY_PAYPAL_LINK;
        }

        // Описание платежа
        if (!isset($data['desc']) || strlen(trim($data['desc'])) < 1)
        {
            $name = $desc = tr("mod::pay::default_desc", ['%pay_id%' => $pay_id]);
        }
        else
        {
            $name = $desc = $data['desc'];
        }

//                $pay['currency'] = 'USD'; // для теста ошибки оплаты.

        $requestParams = [
            self::MOD_PAY_PAYPAL_REQUEST_RETURNURL    => $this->getReturnURL('success', $pay_id),
            self::MOD_PAY_PAYPAL_REQUEST_CANCELURL    => $this->getReturnURL('fail', $pay_id),
            self::MOD_PAY_PAYPAL_REQUEST_AMT          => $pay['pay_sum'],
            self::MOD_PAY_PAYPAL_REQUEST_SHIPPINGAMT  => 0, // стоисомть доставки - пусть будет 0
            self::MOD_PAY_PAYPAL_REQUEST_CURRENCYCODE => $pay['currency'],
            self::MOD_PAY_PAYPAL_REQUEST_ITEMAMT      => $pay['pay_sum'],
            self::MOD_PAY_PAYPAL_REQUEST_NAME         => $name,
            self::MOD_PAY_PAYPAL_REQUEST_DESC         => $desc,
            self::MOD_PAY_PAYPAL_REQUEST_L_AMT        => $pay['pay_sum'],
            self::MOD_PAY_PAYPAL_REQUEST_QTY          => 1, // количичесво товара в заказе - так как у нас весь заказ это одна еденица - то пусть будет 1.
        ];

        $response = $this->requestToAPI('SetExpressCheckout', $requestParams); // запрос счета на оплату.

        $pay_extra = $pay['pay_extra'];

        if (isset($response[self::MOD_PAY_PAYPAL_REQUEST_ACK]) && $response[self::MOD_PAY_PAYPAL_REQUEST_ACK] === self::MOD_PAY_PAYPAL_STATUS_SUCCESS)
        { // Запрос был успешно принят

            $pay_extra['paypal_token'] = $response[self::MOD_PAY_PAYPAL_REQUEST_TOKEN];

            $this->updatePay($pay['pay_id'], [
                'pay_extra' => $pay_extra,
            ]);

            $this->redirect($link . '?cmd=_express-checkout&token=' . urlencode($response[self::MOD_PAY_PAYPAL_REQUEST_TOKEN]));
        }
        else
        {
            $pay_extra['paypal_error_SetExpressCheckout'] = $response;

            $this->updatePay($pay['pay_id'], [
                'pay_extra' => $pay_extra,
            ]);

            $response['pai_id']      = $pay['pay_id'];
            $response['Error_point'] = 'ModPayPaypalMethod->payment->SetExpressCheckout: Paypal reject payment. PayID - ' . $pay['pay_id'];

            $this->debugLogger($response['Error_point'], $pay['pay_id'], true);
            $this->redirect($this->getReturnURL('fail', $pay_id));
        }

        return true;
    }

    /**
     * @param bool $pay_back_page
     *
     * return redirect link
     */
    public function success($pay_back_page = false)
    {
        $pay = $this->getDataForStatusRequest();

        $response = $this->requestToAPI('GetExpressCheckoutDetails',
            [self::MOD_PAY_PAYPAL_REQUEST_TOKEN => $pay['pay_extra']['paypal_token']]); // запрос на получение и валидирование счета ополаты чекрез api.

        if (is_array($response) && $response[self::MOD_PAY_PAYPAL_REQUEST_ACK] === self::MOD_PAY_PAYPAL_STATUS_SUCCESS)
        {
            $requestParams                                             = $response;
            $requestParams[self::MOD_PAY_PAYPAL_REQUEST_PAYMENTACTION] = self::MOD_PAY_PAYPAL_PAYMENTACTION;

            $response = $this->requestToAPI('DoExpressCheckoutPayment', $requestParams); // Оплата счета через api

            if (is_array($response) && $response[self::MOD_PAY_PAYPAL_REQUEST_ACK] === self::MOD_PAY_PAYPAL_STATUS_SUCCESS)  // Оплата счета в проекте.
            {
                $pay_extra                         = $pay['pay_extra'];
                $pay_extra['paypal_transactionId'] = $response[self::MOD_PAY_PAYPAL_RESPONSE_TRANSATION_ID];
                $pay_extra['paypal_response']      = $response;

                $this->updatePay($pay['pay_id'], [ // Оптала счета.
                    'pay_time_done' => time(),
                    'pay_status'    => BillPayEntity::STATUS_PAID,
                    'pay_extra'     => $pay_extra,
                ]);

                $this->lockPay($pay['pay_id']); //Блокировка счета на изминения пользователем. Только через in - можно изменить.
            }
            else
            {
                $pay_extra                                          = $pay['pay_extra'];
                $pay_extra['paypal_error_DoExpressCheckoutPayment'] = $response;

                $this->updatePay($pay['pay_id'], [
                    'pay_extra'  => $pay_extra,
                    'pay_status' => BillPayEntity::STATUS_ERROR,
                ]);

                $response['pai_id']      = $pay['pay_id'];
                $response['Error_point'] = 'ModPayPaypalMethod->payment->success->DoExpressCheckoutPayment: Paypal reject payment. PayID - ' . $pay['pay_id'];

                $this->debugLogger($response['Error_point'], $pay['pay_id'], self::SETTING_MODULE_SEND_TO_USER_ERROR_MSG);
                $this->redirect($this->getReturnURL('fail', $pay['pay_id']));
            }
        }
        else
        {
            $pay_extra                                           = $pay['pay_extra'];
            $pay_extra['paypal_error_GetExpressCheckoutDetails'] = $response;

            $this->updatePay($pay['pay_id'], [
                'pay_extra'  => $pay_extra,
                'pay_status' => BillPayEntity::STATUS_ERROR,
            ]);

            $response['pai_id']      = $pay['pay_id'];
            $response['Error_point'] = 'ModPayPaypalMethod->payment->success->GetExpressCheckoutDetails: Paypal reject payment. PayID - ' . $pay['pay_id'];

            $this->debugLogger($response['Error_point'], $pay['pay_id'], self::SETTING_MODULE_SEND_TO_USER_ERROR_MSG);
            $this->redirect($this->getReturnURL('fail', $pay['pay_id']));
        }

        $url = '/?pay_status=success&pay_id=' . $pay['pay_id'];

        if ($pay_back_page)
        {
            $url = $pay_back_page . substr($url, 1, 100);
        }

        $this->redirect($url);
    }

    /**
     * @param bool $pay_back_page
     *
     * return redirect link
     */

    public function fail($pay_back_page = false)
    {
        $pay = $this->getDataForStatusRequest();

        $response                = $this->requestToAPI('GetExpressCheckoutDetails', [self::MOD_PAY_PAYPAL_REQUEST_TOKEN => $pay['pay_extra']['paypal_token']]);
        $response['Error_point'] = 'ModPayPaypalMethod->fail: Paypal reject payment. PayID - ' . $pay['pay_id'];

        $pay_extra                                           = $pay['pay_extra'];
        $pay_extra['paypal_error_GetExpressCheckoutDetails'] = $response;

        $this->updatePay($pay['pay_id'], [
            'pay_extra'  => $pay_extra,
            'pay_status' => BillPayEntity::STATUS_PAID,
        ]);

        $this->debugLogger($response['Error_point'], $pay['pay_id'], self::SETTING_MODULE_SEND_TO_USER_ERROR_MSG);

        $hash = $this->getPayHash($pay['pay_id']);
        $url  = '/?pay_status=fail&pay_id=' . $pay['pay_id'] . '&h=' . $hash;

        if ($pay_back_page)
        {
            $url = $pay_back_page . substr($url, 1, 100);
        }

        $this->redirect($url);
    }

    /**
     * @param $settings
     *
     * @return bool|string
     * @throws \Exception
     */
    public function checkAPICurrency(array $settings)
    {
        global $container;
        $projectCurrency = $container->get('land_group_service')->load_vars()['currency_code'] ?? null;

        if (!isset($container->get('land_group_service')->load_vars()['currency_code']))
        { //если нет валюты - дальше не имеет смпысла проверять.
            return tr('admin::settings::pay::services::paypal::currency_not_correct_full');
        }

        $response = $this->requestToAPI('GetBalance', [], $settings);

        if (isset($response[self::MOD_PAY_PAYPAL_RESPONSE_CURRENCY]))
        {
            if ($response[self::MOD_PAY_PAYPAL_RESPONSE_CURRENCY] === $projectCurrency)
            {
                $result = true;
            }
            else
            {
                $result = tr('admin::settings::pay::services::paypal::currency_not_correct_full');
            }
        }
        else
        {
            switch ($response[self::MOD_PAY_PAYPAL_RESPONSE_MSG]) {
                case self::MOD_PAY_PAYPAL_ERROR_RESTRICTED:
                    $result = tr('admin::settings::pay::services::paypal::api_restricted_error');
                    break;
                default:
                    $result = tr('admin::settings::pay::services::paypal::api_auth_error');
            }
        }

        return $result;
    }

    /**
     * @param $state
     * @param $pay_id
     *
     * @return string
     */
    private function getReturnURL(string $state, string $pay_id)
    {
        $protocol = 'http://';

        if (isset($_SERVER['HTTP_X_HTTPS']) && $_SERVER['HTTP_X_HTTPS'] === 'on')
        {
            $protocol = 'https://';
        }

        return $protocol . $_SERVER['HTTP_HOST'] . '/mod/pay/paypal/' . $state . '/?pay_id=' . $pay_id;
    }

    /**
     * @param string $method
     * @param array  $params
     * @param array  $settings
     *
     * @return array|string
     */
    private function requestToAPI(string $method, array $params = [], array $settings = [])
    {
        $sandbox = 'sandbox_';
        $linkApi = self::MOD_PAY_PAYPAL_API_SANDBOX;

        if (!empty($settings)) // Метод может вызываться без передачи массива конфигурации классу, сейчас через checkAPICurrency.
        {
            if ($settings['mode'] !== 'debug')
            {
                $linkApi = self::MOD_PAY_PAYPAL_API;
                $sandbox = '';
            }
            $credentials = [
                'USER'      => $settings[$sandbox . 'api_name'],
                'PWD'       => $settings[$sandbox . 'api_pass'],
                'SIGNATURE' => $settings[$sandbox . 'api_sig'],
            ];
        }
        else
        {
            if ($this->config['debug'] === 'no')
            {
                $linkApi = self::MOD_PAY_PAYPAL_API;
                $sandbox = '';
            }
            $credentials = [
                'USER'      => $this->config[$sandbox . 'api_name'],
                'PWD'       => $this->config[$sandbox . 'api_pass'],
                'SIGNATURE' => $this->config[$sandbox . 'api_sig'],
            ];
        }

        $methodParams = [
            'METHOD'  => $method,
            'VERSION' => self::MOD_PAY_PAYPAL_VERSION,
        ];

        $request = http_build_query(array_merge($methodParams, $credentials, $params));  // Сформировываем данные для NVP

        // Настраиваем cURL
        $curlOptions = [
            CURLOPT_URL            => $linkApi,
            CURLOPT_VERBOSE        => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO         => $this->getCert(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $request,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        $response = curl_exec($ch);

        if (curl_errno($ch))
        {
            $error = curl_error($ch);
            curl_close($ch);

            return $error;
        }
        else
        {
            curl_close($ch);
            $responseArray = [];

            parse_str($response, $responseArray); // Разбиваем данные, полученные от NVP в массив todo: превритить в dto во всех платежках.

            return $responseArray;
        }
    }

    /**
     * @return array
     */
    private function getDataForStatusRequest()
    {
        $pay_id = $this->request->query->get('pay_id');
        $token  = $this->request->query->get('token');

        if ((int)$pay_id < 1) // в случае если пытаются хакнуть. -- тут преверка на существование не нужна - переменая получается через реквест симфони. - будет null если ее не существует.
        {
            $this->redirect('/');
        }

        $pay = $this->getPay($pay_id);

        if (is_null($pay_id) || is_null($token) || !isset($pay['pay_id']) || !isset($pay['pay_extra']['paypal_token'])) {
            $this->redirect('/');
        }

        return $pay;
    }

    /**
     * @param $type
     *
     * @return bool
     */
    private function isSupportedType(string $type)
    {
        if (!array_key_exists($type, $this->support_types))
        {
            return false;
        }

        return true;
    }

    /**
     * @param $link
     *
     * return redirect
     */
    private function redirect(string $link)
    { // return new RedirectResponse($url); не отрабатывает - поставил пока такое решение.
        header("Location: " . $link);
        exit();
    }

    /**
     * @param      $msg
     * @param int  $pay_id
     * @param bool $toUser
     */
    private function debugLogger(string $msg, int $pay_id = 0, $toUser = false)
    {
        debugMessage('Billing-paypal-error: ', $msg, self::MOD_PAY_PAYPAL_DEBUG_CHANEL);

        if ($toUser === true)
        {
            $this->userMessage(tr('admin::settings::pay::services::paypal::pay_fail') . ' ' . $pay_id);
        }
    }

    /**
     * @return string
     */
    private function getCert()
    {
        return FRAMEWORK_ROOT_DIR . self::MOD_PAY_PAYPAL_CERT;
    }

    /**
     * @param int    $order_number
     * @param int    $payment_id
     * @param string $hash
     */
    public function complete($order_number = 0, $payment_id = 0, $hash = '')
    {
    }

    /**
     * @param string $login
     */
    public function validLogin($login = '')
    {
    }

    /**
     * @param string $login
     */
    public function marketActive($login = '')
    {
    }

}
