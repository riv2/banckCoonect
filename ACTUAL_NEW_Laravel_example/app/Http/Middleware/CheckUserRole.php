<?php

namespace App\Http\Middleware;

use App\Helpers\PartnerProgramStorage;
use Closure;
use App\User;
use App\Role\RoleChecker;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param string $role
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle($request, Closure $next, $role)
    {
        /** @var User $user */
        $user = Auth::guard()->user();

        //Дополнительная проверка на домен, если это облако
        if (config("domain.cloud_domain")) {
            $pp = PartnerProgramStorage::getPP();
            //Партнер
            if ($user->role == "partner") {
                if (empty($pp) || $user->pp_id != $pp->id) {
                    //Не его партнерка
                    Auth::logout();
                    return redirect("/");
                }
            } elseif ($user->role == "advertiser") {
                if (empty($pp) || $user->pp_id != $pp->id) {
                    //Не его партнерка
                    Auth::logout();
                    return redirect("/");
                }
            } elseif ($user->role == "manager") {
                //может все
            }
        }

        if ($user->role != $role) {
            return redirect('/');
        }

        return $next($request);
    }
}
