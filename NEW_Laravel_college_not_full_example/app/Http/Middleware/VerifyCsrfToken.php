<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '*/pay/auth_result/success',
        '*/pay/auth_result/fail',
        '*/forum/user/ban',
        '*/promotions/*/request',
        '*/profile/mobile',
        '*/profile/mobile/approve',
        '*/get_result_etxt',

        '*/login',
        '*/profile/id',
        '*/students/list/ajax',
        '*/students/delete/ajax',
        '/or_cabinet/list/ajax/*',
        '*inspection/matriculants/list/ajax/*',
        '*/disciplines/list/ajax',
        '/or_cabinet/notification/send',
        '/inspection/notification/send',
        '/inspection/notification/delete',
        '/deansoffice/notifications/list',
        '/deansoffice/news/list',
        '/or_cabinet/order/attach_users',
        '/orders/detach_users',
        '/finances/balance/update',
        '*/users/list/ajax',
        '*/guests/list/ajax',
        '*/specialities/list/ajax',
        '*/discountrequests/list/category/*',
        '*/discountrequests/list/ent',
        '*/discountrequests/list/custom',
        '*/modules/list/ajax',
        '/appeals/list/ajax',
        '/pay/test/cloudpay',
        '/pay/result/cloudpay',
        '/quiz_results/list/ajax',
        '/speciality_semesters/list/ajax',
        '/speciality_semesters/default_list/ajax',
        '/study_plan/list/ajax',
    ];
}
