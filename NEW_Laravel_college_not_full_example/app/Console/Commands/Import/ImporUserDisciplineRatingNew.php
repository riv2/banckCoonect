<?php

namespace App\Console\Commands\Import;

use App\StudentDiscipline;
use Illuminate\Console\Command;

class ImporUserDisciplineRatingNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:discipline:rating:import:new';

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
        $file = fopen(storage_path('import/import_users_rating_2015-2017.csv'), 'r');
        $fileReport = fopen(storage_path('import/import_users_rating_2015-2017_report.csv'), 'w');
        $fileRowCount = sizeof (file (storage_path('import/import_users_rating_2015-2017.csv')));
        $this->output->progressStart($fileRowCount);

        while($row = fgetcsv($file, 0, ',', '"')) {
            $userId = $row[0];
            $disciplineId = $row[6];
            $rating = $row[5];

            $studentDiscipline = StudentDiscipline
                ::where('student_id', $userId)
                ->where('discipline_id', $disciplineId)
                ->first();

            if ($studentDiscipline) {

                if($this->needUpdateRating($studentDiscipline, $rating))
                {
                    $studentDiscipline->setFinalResult($rating);
                    $studentDiscipline->payed = true;
                    $studentDiscipline->save();

                    if($studentDiscipline->final_result == $rating)
                    {
                        $row[] = 'обновлено';
                    }
                    else
                    {
                        $row[] = 'error';
                    }

                }
                else
                {
                    $row[] = 'не обновлено';
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

    public function needUpdateRating($studentDiscipline, $primeResult)
    {
        if(!$studentDiscipline->final_result)
        {
            return true;
        }

        if(!$studentDiscipline->payed_credits)
        {
            return true;
        }
        else
        {
            if(
                $studentDiscipline->test1_result
                && $studentDiscipline->test_result
                && $studentDiscipline->task_result
                && $studentDiscipline->final_result < $primeResult
            )
            {
                return true;
            }
        }

        return false;
        /*$result = !$studentDiscipline->payed_credits
            && !$studentDiscipline->test1_result
            && !$studentDiscipline->test_result
            && !$studentDiscipline->final_result
            && !$studentDiscipline->task_result;

        $result = $result ||
            ($studentDiscipline->payed_credits
                && !$studentDiscipline->test1_result
                && !$studentDiscipline->test_result
                && !$studentDiscipline->final_result
                && !$studentDiscipline->task_result);

        $result = $result ||
            ($studentDiscipline->payed_credits
                && $studentDiscipline->test1_result
                && $studentDiscipline->test_result
                && $studentDiscipline->final_result
                && $studentDiscipline->task_result
                && $studentDiscipline->final_result < $primeResult);*/
    }
}
