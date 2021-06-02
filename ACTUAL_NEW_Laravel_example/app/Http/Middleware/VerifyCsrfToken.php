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
        '/deploy_rQMzYzypyU6RU',
        '/cpapixel.gif',
        '/confirm/16/cpapixel.gif',
        '/confirm/16/clickpixel.gif',
        '/confirm/16/fraudpixel.gif',
        '/webhook*',
        //
    ];
}
