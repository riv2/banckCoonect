<?php

namespace App\Http\Middleware;

use App\Services\Auth;
use Closure;
use DevDojo\Chatter\Models\Ban;

class NoForumBanned
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
        if(Ban::checkBaned(Auth::user()->id))
        {
            abort(404);
        }

        return $next($request);
    }
}
