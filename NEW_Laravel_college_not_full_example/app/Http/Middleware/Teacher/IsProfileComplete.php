<?php

namespace App\Http\Middleware\Teacher;

use App\Teacher\ProfileTeacher;
use App\User;
use Closure;
use Illuminate\Support\Facades\Session;

class IsProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $userID = \Auth::user()->id;
        $teacherProfileExists = ProfileTeacher::where('user_id', $userID)
            ->where('fio', '!=', '')
            ->where('iin', '!=', '')
            ->where('bdate', '!=', '')
            ->exists();

        if ($teacherProfileExists) {
            return $next($request);
        } else {
            Session::push('messages', ['class' => 'alert-danger', 'message' => __('Профиль заполнен не полностью')]);
            return redirect()->route('teacherProfileID');
        }
    }
}
