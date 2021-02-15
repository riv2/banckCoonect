<?php

namespace App\Console\Commands;

use App\Profiles;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class FixElictive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:elictive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $profileList = Profiles::whereNotNull('elective_speciality_id')->get();

        foreach ($profileList as $profile)
        {
            if(StudentDiscipline::where('student_id', $profile->user_id)->where('is_elective', 1)->count() === 0)
            {
                $this->info($profile->user_id);
                StudentDiscipline::addElectiveDisciplines($profile->user_id, $profile->elective_speciality_id);
            }
        }
    }
}
