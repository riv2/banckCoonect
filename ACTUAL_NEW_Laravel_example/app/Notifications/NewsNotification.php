<?php

namespace App\Notifications;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public News $news;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(News $news)
    {
        $this->news = $news;
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
                    ->withSwiftMessage(function ($message) use ($notifiable) {
                        $message->getHeaders()
                            ->addTextHeader('List-Unsubscribe', "<{$notifiable->unsubscribe_link}>");
                    })
                    ->from('no-reply@cpadroid.ru') // . $this->news->pp->tech_domain, $this->news->pp->short_name
                    ->subject($this->news->news_title)
                    ->markdown('emails.news_notification', [
                        'news' => $this->news,
                        'notifiable' => $notifiable,
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
