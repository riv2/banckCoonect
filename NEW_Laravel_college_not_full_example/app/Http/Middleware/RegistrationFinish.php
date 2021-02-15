<?php

namespace App\Http\Middleware;

use App\Profiles;
use App\Services\Auth;
use Closure;
use Illuminate\Support\Facades\Session;

class RegistrationFinish
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
        if(
            isset( Auth::user()->studentProfile->registration_step) &&
            ( (Auth::user()->studentProfile->registration_step == 'finish') ||
                (Auth::user()->studentProfile->registration_step == Profiles::REGISTRATION_STEP_FINISH)
            )
        )
        {
            return $next($request);
        }

        return redirect()->route('registrationBlank');
    }
}
