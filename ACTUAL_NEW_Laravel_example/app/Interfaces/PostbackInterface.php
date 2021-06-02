<?php

namespace App\Interfaces;

use App\Models\Notify;
use Illuminate\Http\Response;

interface PostbackInterface
{
    /**
     * @param \App\Models\Notify $notify
     * @return void
     */
    public function __construct(Notify $notify);

    /**
     * Отправление постбека
     *
     * @return \Illuminate\Http\Response
     */
    public function handle();

}
