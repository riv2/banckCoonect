<?php

namespace App\Http\Middleware;

use App\Helpers\PartnerProgramStorage;
use Closure;
use Illuminate\Http\Request;

class WelcomeMessage
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/advertiser/welcome',
    ];

    /**
     * Determine if the request has a URI that should pass through.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Если не авторизован - пропускаем
        if (!auth()->check()) {
            return $next($request);
        }

        if ($this->inExceptArray($request)) {
            return $next($request);
        }

        if (auth()->user()->role == 'advertiser') {
            $pp = PartnerProgramStorage::getPP();
            if ($pp->onboarding_status=='registered') {
                return redirect()->route('advertiser.welcome.index');
            }
        }

        return $next($request);
    }
}
