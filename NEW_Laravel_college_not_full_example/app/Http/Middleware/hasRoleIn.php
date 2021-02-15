<?php

namespace App\Http\Middleware;

use App\Services\Auth;
use Closure;
use Illuminate\Support\Facades\Log;

class hasRoleIn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roles)
    {
        $roles = explode(' ', $roles);

        if(!(bool)Auth::user()->roles()->whereIn('name', $roles)->count())
        {
            abort(404);
        }

        return $next($request);
    }
}
