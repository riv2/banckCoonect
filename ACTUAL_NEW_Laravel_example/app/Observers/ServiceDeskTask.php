<?php

namespace App\Observers;

use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ServiceDeskTask
{
    public function created(\App\Models\ServicedeskTask $task)
    {
//        Log::stack(["telegram"])
//            ->info("Новый тикет: " . $task->subject);
//        $user = User::query()->where("id","=", $task->creator_user_id)->first();
//        Mail::send(["text"=>"emails.supportnew_" . app()->getLocale()], ["support"=>$task], function($message) use($user) {
//            $message->from(config("mail.from.address"))
//                ->to($user->email)
//                ->subject(__("Новый тикет"));
//        });
//        Mail::send(["text"=>"emails.supportnew_" . app()->getLocale()], ["support"=>$task], function($message) use($user) {
//            $message->from(config("mail.from.address"))
//                ->to("help@gocpa.cloud")
//                ->subject(__("Новый тикет"));
//        });
    }

}
