<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class CheckBanned
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
        if (auth()->check()) {
            $user = auth()->user();
            if (!is_null($user)) {
                if ($user->status === 2) {
                    $user_id = $user->id;
                    $email = $user->email;
                    Log::info('Авторизация под заблокированным пользователем!', [$user_id, $email]);

                    if ($user->isImpersonated()) {
                        $user->leaveImpersonation();
                        return redirect()->route('manager.users')->withErrors(sprintf('Аккаунт #%d %s заблокирован, авторизация невозможна!', $user_id, $email));
                    }

                    auth()->logout();
                    return redirect()->route('login')->withErrors('Ваш аккаунт заблокирован за нарушение правил пользования сервисом.');
                } elseif ($user->status === 3) {
                    $user_id = $user->id;
                    $email = $user->email;
                    Log::info('Авторизация под удаленным пользователем!', [$user_id, $email]);

                    if ($user->isImpersonated()) {
                        $user->leaveImpersonation();
                        return redirect()->route('manager.users')->withErrors(sprintf('Аккаунт #%d %s удален, авторизация невозможна!', $user_id, $email));
                    }

                    auth()->logout();
                    return redirect()->route('login')->withErrors('Неправильный логин или пароль!');
                }
            }
        }

        return $next($request);
    }
}
