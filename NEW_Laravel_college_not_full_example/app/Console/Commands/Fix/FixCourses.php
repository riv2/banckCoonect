<?php

namespace App\Console\Commands\Fix;

use App\Profiles;
use App\User;
use Illuminate\Console\Command;

class FixCourses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:fix';

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
        $file = fopen(storage_path('import/fix_courses.csv'), 'r');
        $rowCount = sizeof (file (storage_path('import/fix_courses.csv')));
        $updatedCount = 0;
        $userNotFound = [];

        $this->output->progressStart($rowCount);

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $userId = $row[0];
            $course = $row[1];

            $userProfile = Profiles::with('speciality')->where('user_id', $userId)->first();

            if(!$userProfile)
            {
                $userNotFound[] = $userId;
                continue;
            }

            if(!$course)
            {
                $userProfile->course = date('Y', time()) - $userProfile->speciality->year;
            }
            else
            {
                $userProfile->course = $course;
            }

            $userProfile->save();
            $updatedCount++;

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        print_r($userNotFound);
        $this->info('Updated: ' . $updatedCount);
    }
}
