<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\Auth;
use App\User;

class HasNotAcademDebt
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
            && $user->hasAcademDebt()
        )
        {
            abort(404);
        }

        return $next($request);
    }
}
