<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Onboarding extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * @var string[]
     */
    private array $subjects;
    public User $user;
    private int $massageNumber;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, int $messageNumber)
    {
        $this->user = $user;
        $this->massageNumber = $messageNumber;
        $this->subjects = [
            '1' => 'Знакомство с GoCPA. С чего начать?',
            '2' => 'Знакомство с GoCPA. Следующий шаг: заведение оффера',
            '3' => 'Знакомство с GoCPA. Магия интеграции',
            '4.1' => 'Знакомство с GoCPA. Вы могли бы уже начать работать с партнёрами, если только',
            '4.2' => 'Знакомство с GoCPA. Пора приглашать партнёров!',
            '5' => 'Знакомство с GoCPA. Это только начало успеха вашей партнерской программы',
        ];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): Onboarding
    {
        return $this->view('emails.onboarding.' . $this->massageNumber)->subject($this->subjects[$this->massageNumber]);
    }
}
