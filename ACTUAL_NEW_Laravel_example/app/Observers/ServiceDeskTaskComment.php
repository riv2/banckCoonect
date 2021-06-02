<?php

namespace App\Observers;

use App\Notifications\TicketAnswered;
use App\Notifications\TicketCreated;
use App\User;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class ServiceDeskTaskComment
{
    public function created(\App\Models\ServicedeskTaskComment $comment)
    {
        //Получим ID менеджера
        $manager_id = User::query()->where("role", "=", "manager")->first()->id;
        //В обоих случаях мы передаем комментарий
        //чтобы из него вытащить текст комментария и основной таск

        //Мы понимаем что тикет только создан, если к нему прикреплен только один коммент
        if ($comment->task->comments->count() == 1) {
            Notification::send([
                $comment->task->creator,
                $comment->task->doer,
            ], new TicketCreated($comment));

            if ($comment->task->doer->id==$manager_id) {
                Log::stack(["telegram"])->info("Cоздан тикет #".$comment->task->id." ".$comment->task->subject. ". Текст обращения:" . $comment->body);
            }
        } else {
            if ($comment->task->creator->id == $comment->partner_id) {
                // Ответ от создателя задачи
                $to = $comment->task->doer;
                if ($comment->task->doer->id==$manager_id) {
                    Log::stack(["telegram"])->info("Новый ответ на тикет #".$comment->task->id." ".$comment->task->subject. ". Текст обращения:" . $comment->body);
                }
            } else {
                $to = $comment->task->creator;
            }
            Notification::send($to, new TicketAnswered($comment));
        }
    }
}
