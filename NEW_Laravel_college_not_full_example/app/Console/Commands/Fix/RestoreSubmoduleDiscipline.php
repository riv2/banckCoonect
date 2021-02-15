<?php

namespace App\Console\Commands\Fix;

use App\StudentDiscipline;
use Illuminate\Console\Command;

class RestoreSubmoduleDiscipline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:submodule:discipline';

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
        $file = fopen(storage_path('import/restore_submodule_discipline.csv'), 'r');
        $reportFile = fopen(storage_path('import/restore_submodule_discipline_report.csv'), 'w');
        $reportFile2 = fopen(storage_path('import/restore_submodule_discipline_report2.csv'), 'w');
        $rowCount = sizeof (file (storage_path('import/restore_submodule_discipline.csv')));

        $this->output->progressStart($rowCount);

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $userId = $row[0];
            $disciplineId = $row[6];
            $semester = $row[8];
            $sumbodule_id = $row[7];
            $status1 = $row[9];
            $status2 = $row[10];

            if( $status1 == 'deleted submodule' && $status2 == 'deleted' && $semester == 2 )
            {
                $studentDiscipline = StudentDiscipline
                    ::where('student_id', $userId)
                    ->where('discipline_id', $disciplineId)
                    ->first();

                if( $studentDiscipline )
                {
                    fputcsv($reportFile, $studentDiscipline->toArray());
                }
                else
                {
                    fputcsv($reportFile2, [$userId,$disciplineId]);
                }
            }

            $this->output->progressAdvance();
        }

        fclose($reportFile);
        fclose($reportFile2);
        $this->output->progressFinish();
    }
}
