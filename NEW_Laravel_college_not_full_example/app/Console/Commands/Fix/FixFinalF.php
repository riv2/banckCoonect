<?php

namespace App\Console\Commands\Fix;

use App\StudentDiscipline;
use Illuminate\Console\Command;

class FixFinalF extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:final:f {--part=1}';

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
        $part = $this->option('part');

        $file = fopen(storage_path('import/fix_final_f_' . $part . '.csv'), 'r');
        $fileReport = fopen(storage_path('import/fix_final_f_report_' . $part . '.csv'), 'w');
        $fileRowCount = sizeof (file (storage_path('import/fix_final_f_' . $part . '.csv')));
        $this->output->progressStart($fileRowCount);

        while($row = fgetcsv($file, 0, ',', '"')) {
            $userId = $row[0];
            $disciplineId = $part == 1 ? $row[7] : $row[1];

            $studentDiscipline = StudentDiscipline
                ::where('student_id', $userId)
                ->where('discipline_id', $disciplineId)
                ->first();

            if ($studentDiscipline) {

                if($studentDiscipline->final_result_letter == 'F')
                {
                    $studentDiscipline->final_result = null;
                    $studentDiscipline->final_result_points = null;
                    $studentDiscipline->final_result_gpa = null;
                    $studentDiscipline->final_result_letter = null;
                    $studentDiscipline->final_date = null;
                    $studentDiscipline->final_manual = 0;
                    $studentDiscipline->save();

                    if($studentDiscipline->final_result === null)
                    {
                        $row[] = 'success';
                    }
                    else
                    {
                        $row[] = 'error';
                    }
                }
                else
                {
                    $row[] = 'not F';
                }
            }
            else
            {
                $row[] = 'student_discipline not found';
            }

            $this->output->progressAdvance();
            fputcsv($fileReport, $row);
        }

        $this->output->progressFinish();
        fclose($fileReport);
    }
}
