<?php

namespace App\Http\Middleware;

use App\Services\Auth;
use Closure;

class IsStudent
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
        $profile = Auth::user()->studentProfile;
        
        if(!$profile || $profile->education_status != 'student')
        {
            return redirect()
                ->route('home')
                ->with(
                    'messages', 
                    [
                        ['class' => 'alert-danger', 'message' => 'Доступно только обучающимся Miras University.']
                    ]
                );
        }

        return $next($request);
    }
}
