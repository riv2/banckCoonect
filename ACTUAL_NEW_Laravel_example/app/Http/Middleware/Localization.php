<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;

class Localization
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
        //Проверка - задана ли кука с языком
        if ($localeCookie = Cookie::get('locale', null)) {

            $locales = array_keys(config('app.locales'));
            //Проверка - нет ли такого параметра в конфиге приложения
            if (!in_array($localeCookie, $locales)) {
                Cookie::forget('locale');
                return $next($request);
            }
            $user = auth()->user();
            if($user) {
                if ( $user->lang !== $localeCookie ) {
                    $user->lang = $localeCookie;
                    $user->save();
                }
            }
            App()->setLocale($localeCookie);
        } else if (auth()->user()) {
            $localeUser = auth()->user()->lang;
            App()->setLocale($localeUser);
        } else {
            //Случай, когда кука не задана
            $localeBrowser = substr($request->getPreferredLanguage(), 0, 2);
            if (!in_array($localeBrowser, array_keys(config('app.locales')))) {
                //Язык браузера отсутствует в конфиге приложения
                return $next($request);
            } else if (!auth()->user() || auth()->user()->role == 'manager') {
                //Для неавторизованной зоны и менеджера
                App()->setLocale($localeBrowser);
                return $next($request);
            } else {
                $ppLangs = [];
                foreach (auth()->user()->pp->lang as $key => $item) {
                    if ($item) {
                        array_push($ppLangs, $key);
                    }
                }
                if (!in_array($localeBrowser, $ppLangs)) {
                    //Язык браузера отсутствует в параметрах партнёрской программы
                    return $next($request);
                }
                App()->setLocale($localeBrowser);
            }
        }

        return $next($request);
    }
}
