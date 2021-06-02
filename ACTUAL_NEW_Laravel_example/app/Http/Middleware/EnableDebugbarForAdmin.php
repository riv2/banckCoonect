<?php

namespace App\Http\Middleware;

use Closure;

class EnableDebugbarForAdmin
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
        $response = $next($request);

        if ($request->hasCookie('debug_panel_allow')) {
            return $response;
        }

        if (auth()->user() && in_array(auth()->id(), [1, 2])) {
            $response->withCookie(cookie('debug_panel_allow', 1));
        }
        return $response;
    }
}
