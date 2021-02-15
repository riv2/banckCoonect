<?php

namespace App\Jobs;

use App\Speciality;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateStudentSubmodules implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $speciality;

    /**
     * Create a new job instance.
     *
     * @param Speciality $speciality
     * @return void
     */
    public function __construct(Speciality $speciality)
    {
        $this->speciality = $speciality;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->speciality->updateStudentSubmodules();
    }
}
