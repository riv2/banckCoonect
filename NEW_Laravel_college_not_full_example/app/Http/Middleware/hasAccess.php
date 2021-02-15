<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\Auth;

class hasAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $cabinetName)
    {
        if(!Auth::user()->hasAccess($cabinetName))
        {
            abort(404);
        }

        return $next($request);
    }
}
