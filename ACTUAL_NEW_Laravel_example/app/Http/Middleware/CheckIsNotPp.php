<?php

namespace App\Http\Middleware;

use Closure;

class CheckIsNotPp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $root_domain = config('app.domain');
        if ($root_domain === null || $root_domain === '') {
            return abort(503, 'Не указана env переменная DOMAIN');
        }

        if ($request->getHost() === $root_domain) {
            return $next($request);
        }

        return abort(404);
    }
}
