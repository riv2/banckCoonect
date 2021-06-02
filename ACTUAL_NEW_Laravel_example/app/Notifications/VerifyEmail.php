<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification
{
    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($generated_password, $locale = 'ru')
    {
        $this->generated_password = $generated_password;
        $this->locale = $locale;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }

        if ($notifiable->role === 'advertiser') {
            $subject = __('emails.verify.advertiser_registered', [], $this->locale);
            $markdown = 'emails.pp_created_' . app()->getLocale();
        } elseif ($notifiable->role === 'partner') {
            $subject = __('emails.verify.partner_registered', [], $this->locale);
            $markdown = 'emails.account_created_' . app()->getLocale();
        }

        return (new MailMessage)
            ->subject($subject)
            ->markdown($markdown, [
                'notifiable' => $notifiable,
                'generated_password' => $this->generated_password ?? 'null',
                'verificationUrl' => $verificationUrl,
            ]);
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        $parametres = [
            'id' => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification()),
        ];
        $signed_route = URL::signedRoute('verification.verify', $parametres, null, false);
        return 'https://' . $notifiable->pp_domain . $signed_route;
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
