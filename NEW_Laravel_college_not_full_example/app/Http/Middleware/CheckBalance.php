<?php

namespace App\Http\Middleware;

use App\Services\Auth;
use App\User;
use Closure;

class CheckBalance
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
        $user = Auth::user();
        if(
            env('CHECK_TEST_TABOO')
            && $user->keycloak
            && $user->import_type != User::IMPORT_TYPE_ENG_TEST
            && !$user->studentProfile->ignore_debt
            && $user->balance() < 0
        )
        {
            abort(404);
        }

        return $next($request);
    }
}
