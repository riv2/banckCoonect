<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;
use Closure;
use Illuminate\Support\Facades\Route;

class StripEmptyParams extends Middleware
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
        $old_query = request()->query();

        $query = $old_query;
        foreach ($query as $key => $value) {
            // Если значения пустые - сносим!
            if ($value == '') {
                unset($query[$key]);
                continue;
            }
        }

        if ($old_query !== $query) {
            $path = url()->current() . (!empty($query) ? '/?' . http_build_query($query) : '');
            return redirect($path, 301, []);
        }
        return $next($request);
    }
}
