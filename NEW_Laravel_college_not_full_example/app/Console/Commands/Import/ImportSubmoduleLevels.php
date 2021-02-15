<?php

namespace App\Console\Commands\Import;

use App\Profiles;
use App\StudentDiscipline;
use App\StudentSubmodule;
use Illuminate\Console\Command;

class ImportSubmoduleLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:submodule:levels';

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
        $file = fopen(storage_path('import/import_submodule_levels.csv'), 'r');
        $reportFile = fopen(storage_path('import/import_submodule_levels_report.csv'), 'w');
        $rowCount = sizeof (file (storage_path('import/import_submodule_levels.csv')));

        $this->output->progressStart($rowCount);

        $studentResults = [];

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $fio = $row[0];
            $levelKz = $row[7];
            $levelEn = $row[9];

            $cache = [
                'kz' => $levelKz,
                'en' => $levelEn,
                'fio' => $fio
            ];

            $studentResults[strtoupper($fio)] = $cache;
        }

        Profiles::where('course', 1)->chunk(1000, function($profiles) use($studentResults, $reportFile){

            foreach ($profiles as $profile)
            {
                $hasSubmoduleKz = (bool)StudentSubmodule
                    ::where('student_id', $profile->user_id)
                    ->whereIn('submodule_id', [3,4])
                    ->count();

                $hasSubmoduleEn = (bool)StudentSubmodule
                    ::where('student_id', $profile->user_id)
                    ->whereIn('submodule_id', [1,2])
                    ->count();

                if($hasSubmoduleEn || $hasSubmoduleKz)
                {
                    $report = [
                        $profile->user_id,
                        $profile->fio,
                    ];

                    $fio = strtoupper($profile->fio);
                    if( isset($studentResults[$fio]) )
                    {
                        if($hasSubmoduleEn)
                        {
                            if(!empty($studentResults[$fio]['en']))
                            {
                                $report[] = 'en success';
                            }
                            else
                            {
                                $report[] = 'en not found';
                            }
                        }

                        if($hasSubmoduleKz)
                        {
                            if(!empty($studentResults[$fio]['kz']))
                            {
                                $report[] = 'kz success';
                            }
                            else
                            {
                                $report[] = 'kz not found';
                            }
                        }
                    }
                    else
                    {
                        $report[] = 'user not found';
                    }

                    fputcsv($reportFile, $report);
                }

                $this->output->progressAdvance();

            }
        });

        $this->output->progressFinish();
        fclose($reportFile);
    }
}
