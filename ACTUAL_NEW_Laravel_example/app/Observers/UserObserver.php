<?php

namespace App\Observers;

use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

class UserObserver
{
    public function creating(User $user)
    {
        if (
            !$user->pp ||
            !config('domain.cloud_domain')
        ) {
            return true;
        }
        $limit = config("app.tariff_limits.". $user->pp->tariff);
        $actual = User::query()
            ->where("pp_id", "=", $user->pp_id)
            ->where("role", "=", "partner")
            ->count();
        if ($actual>=$limit) {
            Log::stack(["telegram","email"])
                ->error(sprintf(
                    "Превышение лимита регистраций в ПП %s (%s)",
                    $user->pp_id,
                    $user->email
                ));

            $next = ($user->pp->tariff == "free") ? "START" : "PROFESSIONAL";

            $ppOwner = User::query()
                ->where("pp_id", "=", $user->pp_id)
                ->where("role", "=", "advertiser")
                ->first();
            Mail::send(["html"=>"emails.system_" . app()->getLocale()], ["text"=>"
            Попытка регистрации в партнерской программе.
            Пользователь ".$user->email." не смог зарегистрироваться. Чтобы снова открыть регистрацию перейдите на тариф ".$next."
            "], function ($message) use ($ppOwner) {
                $message->from(config("mail.from.address"))
                    ->to($ppOwner->email)
                    ->subject(__("Попытка регистрации в партнерской программе"));
            });
            $errors = Session::get('errors', new ViewErrorBag());
            if (! $errors instanceof ViewErrorBag) {
                $errors = new ViewErrorBag;
            }
            $bag = $errors->getBags()['default'] ?? new MessageBag;
            $bag->add(null, __("Регистрация закрыта, обратитесь к вашему менеджеру."));
            Session::flash(
                'errors',
                $errors->put('default', $bag)
            );
            return false;
        }
    }

    /**
     * Handle the user "created" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function created(User $user)
    {
        //
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
