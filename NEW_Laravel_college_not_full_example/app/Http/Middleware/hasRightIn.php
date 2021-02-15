<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\Auth;

class hasRightIn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $sectionName, $actions)
    {
        $actions = explode(' ', $actions);

        if(!Auth::user()->hasRight($sectionName, $actions))
        {
            abort(404);
        }

        return $next($request);
    }
}
