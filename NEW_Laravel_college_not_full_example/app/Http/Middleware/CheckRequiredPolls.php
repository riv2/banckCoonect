<?php

namespace App\Http\Middleware;

use Closure;
use App\Poll;
use Illuminate\Support\Facades\Auth;

class CheckRequiredPolls
{
    protected $except = [
        'logout',
        'students.polls.show',
        'student.poll.show',
        'student.poll.pass',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_id = Auth::id();

        if (!$request->routeIs($this->except) && !empty($user_id)) {
            $polls = Poll::availablePolls($user_id)->where('is_required', true)->get();

            if (!$polls->isEmpty()) {
                return redirect()->route('students.polls.show')->withErrors([__('Before you will start to work on the site you need to pass polls')]);
            }
        }

        return $next($request);
    }
}
