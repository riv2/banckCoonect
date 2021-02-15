<?php

namespace App\Http\Middleware;

use App\Services\Auth;
use Closure;

class hasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roleName)
    {
        if(!Auth::user()->hasRole($roleName))
        {
            abort(404);
        }

        return $next($request);
    }
}
