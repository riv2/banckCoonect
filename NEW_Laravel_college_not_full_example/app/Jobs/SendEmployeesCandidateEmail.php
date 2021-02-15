<?php

namespace App\Jobs;

use Mail;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmployeesCandidateEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $resumeID;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($resumeID)
    {
        $this->resumeID = $resumeID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $usersEmails = User::whereHas('roles', function($query){
            $query->where('name', 'oauk');
        })->pluck('email')->toArray();

        Mail::send('emails.employees.new_resume',
            [ 'id' => $this->resumeID ], 
            function ($message) use ($usersEmails) {
                $message->from(getcong('site_email'), getcong('site_name'));
                $message->to( 'test@test.com' )->subject('Новая заявка на вакансию');
            }
        );
    }
}
