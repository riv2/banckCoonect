<?php

namespace App\Listeners;

use App\Models\MailLogger;
use Illuminate\Mail\Events\MessageSending;

class LogSendingMessage
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param MessageSending $event
     * @return \Swift_Message
     */
    public function handle(MessageSending $event)
    {
        $log = new MailLogger();
        $log->id = $event->message->getId();
        $log->message = $event->message->getBody();
        $log->subject = $event->message->getSubject() ?? "";
        $log->sender = $event->message->getFrom();
        $log->recipients = $event->message->getTo();
        $log->status = 'new';
        $log->save();
        return $event->message;
    }

}
