<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\Auth;

class hasRight
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $sectionName, $actionName)
    {
        if(!Auth::user()->hasRight($sectionName, $actionName))
        {
            abort(404);
        }

        return $next($request);
    }
}
