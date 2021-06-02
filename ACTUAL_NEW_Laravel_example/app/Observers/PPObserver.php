<?php

namespace App\Observers;

use App\Models\PayMethod;
use App\Models\Pp;

class PPObserver
{
    /**
     * Handle the pp "created" event.
     *
     * @param Pp $pp
     * @return void
     */
    public function created(Pp $pp)
    {
        $pp->pay_methods()->attach(PayMethod::all());
    }

    /**
     * Handle the pp "updated" event.
     *
     * @param Pp $pp
     * @return void
     */
    public function updated(Pp $pp)
    {
        //
    }

    /**
     * Handle the pp "deleted" event.
     *
     * @param Pp $pp
     * @return void
     */
    public function deleted(Pp $pp)
    {
        //
    }

    /**
     * Handle the pp "restored" event.
     *
     * @param Pp $pp
     * @return void
     */
    public function restored(Pp $pp)
    {
        //
    }

    /**
     * Handle the pp "force deleted" event.
     *
     * @param Pp $pp
     * @return void
     */
    public function forceDeleted(Pp $pp)
    {
        //
    }
}
