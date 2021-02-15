<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class FixStudentEngSpeciality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'student:eng:speciality:fix {--year=2019} {--check=false}';

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
        $year = $this->option('year');
        $check = $this->option('check') == 'true' ? true : false;

        if($check)
        {
            $this->warn('Check mode');
        }

        $file = fopen(storage_path('import/import_users_fix_' . $year . '.csv'), 'r');
        $fileRowCount = sizeof (file (storage_path('import/import_users_fix_' . $year . '.csv')));

        /*Create file cache*/
        $this->info('Create cache by csv');
        $this->output->progressStart( $fileRowCount );
        $csvCache = [];

        while($row = fgetcsv($file, 0, ',', "'"))
        {
            $userExId = $row[0];
            $newSpeciality = $row[16];

            if($newSpeciality && empty($csvCache[$userExId]))
            {
                $csvCache[$userExId] = $newSpeciality;
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $this->output->progressStart(count($csvCache));
        $updatedRows = 0;

        print_r($csvCache);

        foreach($csvCache as $userExId => $newSpeciality)
        {
            $user = User::with('studentProfile')->where('ex_id', $userExId)->first();

            if($user)
            {
                if($user->studentProfile)
                {
                    $user->studentProfile->education_speciality_id = $newSpeciality;
                    $user->studentProfile->save();

                    $user->studentProfile->updateDisciplines();
                    $user->studentProfile->updateSubmodules();

                    $updatedRows++;
                }
            }

            $this->output->progressAdvance();
        }
        $this->output->progressFinish();

        $this->info('Updated rows: ' . $updatedRows);
    }
}
