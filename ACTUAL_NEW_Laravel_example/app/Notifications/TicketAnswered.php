<?php

namespace App\Notifications;

use App\Models\ServicedeskTaskComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAnswered extends Notification
{
    use Queueable;

    /**
     * @var ServicedeskTaskComment $comment
     */
    public $comment;

    public function __construct($comment)
    {
        $this->comment = $comment;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__("Получен комментарий на запрос #") . $this->comment->task->id)
                    ->markdown('emails.message', [
                        "text"=>__('tickets.mail.ticket_answered', [
                            'subject' => $this->comment->task->subject,
                            'body' => $this->comment->body,
                        ])
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
