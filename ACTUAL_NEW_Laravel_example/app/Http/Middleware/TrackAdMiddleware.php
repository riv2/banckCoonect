<?php

namespace App\Http\Middleware;

use Closure;

class TrackAdMiddleware
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
        // Проверяем подпись
        if ($request->get('sign') !== 'moy4B2B_k!J84EbBTQooCPR-F6LHTCPo') {
            return abort(404);
        }

        return $next($request);
    }
}
