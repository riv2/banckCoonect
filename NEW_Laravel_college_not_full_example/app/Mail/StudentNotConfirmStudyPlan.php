<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class StudentNotConfirmStudyPlan extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $userId;
    protected $reason;

    public function __construct(int $userId, string $reason)
    {
        $this->userId = $userId;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->view('emails.student-not-confirm-study-plan')->with(
            [
                'userId' => $this->userId,
                'reason' => $this->reason
            ]
        );
    }
}