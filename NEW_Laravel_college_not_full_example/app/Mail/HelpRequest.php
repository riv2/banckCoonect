<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class HelpRequest extends Mailable
{
    use Queueable, SerializesModels;

    protected $helpRequest = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($helpRequest)
    {
        $this->helpRequest = $helpRequest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->
        to(env('MAIL_FOR_NOTIFICATION', ''))->
            subject('Запрос обратной связи')->
        view('emails.help_request', ['helpRequest' => $this->helpRequest]);
    }
}
