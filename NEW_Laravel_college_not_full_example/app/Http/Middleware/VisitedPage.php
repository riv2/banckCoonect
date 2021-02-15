<?php

namespace App\Http\Middleware;

use App\ActivityLog;
use Closure;

class VisitedPage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $page)
    {
        ActivityLog::userVisitedPage($page);

        return $next($request);
    }
}
