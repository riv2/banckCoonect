<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\Auth;

class ConfirmMobile
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
        if(Auth::user()->studentProfile &&
            !Auth::user()->studentProfile->mobile_confirm &&
            !Auth::user()->checkIgnoreConfirmMobile())
        {
            return redirect()->route('studentMobileConfirm');
        }

        return $next($request);
    }
}
