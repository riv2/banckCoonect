<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RefundBankReject extends Mailable
{
    use Queueable, SerializesModels;

    protected $user = null;
    protected $statusWaiting = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $statusWaiting)
    {
        $this->user = $user;
        $this->statusWaiting = $statusWaiting;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        

        return $this->from(getcong('site_email'), getcong('site_name'))
                    ->subject('Банк не принял заявку на возврат '. $this->statusWaiting->order_number)
                    ->view('emails.refund_bank_reject', [
                        'user' => $this->user,
                        'statusWaiting'=> $this->statusWaiting
                    ]);
    }
}
