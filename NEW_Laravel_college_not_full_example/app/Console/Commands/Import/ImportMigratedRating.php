<?php

namespace App\Console\Commands\Import;

use App\Profiles;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class ImportMigratedRating extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:migrated:rating';

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
        $file = fopen(storage_path('import/import_migrated_rating.csv'), 'r');
        $fileReport = fopen(storage_path('import/import_migrated_rating_report.csv'), 'w');
        $fileRowCount = sizeof (file (storage_path('import/import_migrated_rating.csv')));
        $this->output->progressStart($fileRowCount);

        while($row = fgetcsv($file, 0, ',', '"')) {
            $userIin = $row[2];
            $disciplineId = $row[14];
            $rating = $row[13];

            if($rating)
            {
                $profile = Profiles::where('iin', $userIin)->first();

                if($profile)
                {
                    $studentDiscipline = StudentDiscipline
                        ::where('student_id', $profile->user_id)
                        ->where('discipline_id', $disciplineId)
                        ->first();

                    if ($studentDiscipline) {
                        $studentDiscipline->setFinalResult($rating);
                        $studentDiscipline->migrated = true;
                        $studentDiscipline->payed = true;
                        $studentDiscipline->save();

                        if($studentDiscipline->final_result == $rating)
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
                        $row[] = 'student_discipline not found';
                    }
                }
                else
                {
                    $row[] = 'student not found';
                }
            }
            else
            {
                $row[] = 'rating not found';
            }
            $this->output->progressAdvance();
            fputcsv($fileReport, $row);
        }

        $this->output->progressFinish();
        fclose($fileReport);
    }
}
