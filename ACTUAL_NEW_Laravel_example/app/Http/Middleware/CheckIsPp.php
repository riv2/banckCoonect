<?php

namespace App\Http\Middleware;

use App\Helpers\PartnerProgramStorage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Closure;
use Illuminate\Support\Facades\Cache;

class CheckIsPp
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
        $root_domain = config('app.domain');
        if ($root_domain === null || $root_domain === '') {
            return abort(503, 'Не указана env переменная DOMAIN');
        }

        $request_host = $request->getHost();
        if ($request_host !== $root_domain) {
            $pp = Cache::remember('pp_' . $request_host, now()->addHours(1), function () use ($request_host) {
                $pp = PartnerProgramStorage::getPP($request_host);
                return $pp;
            });
            if (!$pp) {
                abort(404, 'Партнерская программа не найдена');
            }
            App::singleton('partner_program', function () use ($pp) {
                return $pp;
            });

            View::share('partner_program', $pp);
            return $next($request);
        }
        return abort(404);
    }
}
