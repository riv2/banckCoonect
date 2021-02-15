<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\Auth;
use Illuminate\Support\Facades\Log;

class RegistrationPaid
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
        Log::info( $request->getPathInfo());

        if(!Auth::user()->studentProfile->paid) {
            return redirect()->route('payRegistrationFeeAlert', ['back' => base64_encode(env('APP_URL') . $request->getPathInfo())]);
        }

        return $next($request);
    }
}
