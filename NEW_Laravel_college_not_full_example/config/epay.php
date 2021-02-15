<?php
return [
    'pay_test_mode' => env('PAYMENT_TEST_MODE', ''),

    'MERCHANT_CERTIFICATE_ID'   => env('MERCHANT_CERTIFICATE_ID', ''),
    'MERCHANT_NAME'             => env('MERCHANT_NAME', ''),
    'PRIVATE_KEY_PATH'          => env('PRIVATE_KEY_PATH', ''),
    'PRIVATE_KEY_PASS'          => env('PRIVATE_KEY_PASS', ''),
    'XML_TEMPLATE_FN'           => env('XML_TEMPLATE_FN', ''),
    'XML_COMMAND_TEMPLATE_FN'   => env('XML_COMMAND_TEMPLATE_FN', ''),
    'PUBLIC_KEY_PATH'           => env('PUBLIC_KEY_PATH', ''),
    'MERCHANT_ID'               => env('MERCHANT_ID', ''),
    // Линк для возврата покупателя в магазин (на сайт) после успешного проведения оплаты
    'EPAY_BACK_LINK'            => config('app.url') . '/pay/result',/*'https://testpay.kkb.kz/jsp/client/pay.jsp',*/
    // Линк для отправки результата авторизации в магазин.
    'EPAY_POST_LINK'            => config('app.url') . '/pay/auth_result/success',/*'https://testpay.kkb.kz/jsp/client/pl.jsp',*/
    // Линк для отправки неудачного результата авторизации либо информации об ошибке в магазин.
    'EPAY_FAILURE_POST_LINK'    => config('app.url') . '/pay/auth_result/fail',/*'https://testpay.kkb.kz/jsp/client/pay.jsp',*/

    'EPAY_FORM_TEMPLATE'        => 'default.xsl',
];