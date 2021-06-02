<?php

namespace App\Listeners;

use App\Models\MailLogger;
use Illuminate\Mail\Events\MessageSent;

class LogSentMessage
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
     * @param MessageSent $event
     * @return \Swift_Message
     */
    public function handle(MessageSent $event)
    {
        $log = MailLogger::where('id','=', $event->message->getId())->first();
        if($log) {
            $log->status = 'sent';
            $log->save();
        }
        return $event->message;
    }

}
