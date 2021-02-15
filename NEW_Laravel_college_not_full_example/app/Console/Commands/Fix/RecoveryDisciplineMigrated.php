<?php

namespace App\Console\Commands\Fix;

use App\PayDocument;
use App\Services\Service1C;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class RecoveryDisciplineMigrated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recovery:discipline:migrated';

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
        $file = fopen(storage_path('import/recovery_discipline_migrated.csv'), 'r');
        $reportFile = fopen(storage_path('import/recovery_discipline_migrated_report.csv'), 'w');
        $rowCount = sizeof (file (storage_path('import/recovery_discipline_migrated.csv')));

        $this->output->progressStart($rowCount);

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $userId = $row[0];
            $disciplineId = $row[3];
            $action = $row[4];

            $studentDiscipline = StudentDiscipline
                ::where('student_id', $userId)
                ->where('discipline_id', $disciplineId)
                ->first();

            if($studentDiscipline)
            {
                if($action == 'убрать куплено кредитов,очистить оценку.' || $action == 'очистить оценку.')
                {
                    $cancelLog = $this->payCancel($studentDiscipline->id);
                    $row = array_merge($row, $cancelLog);
                    $studentDiscipline->payed_credits = null;
                    $studentDiscipline->payed = 0;

                    /*Clear*/
                    $studentDiscipline->test1_result = null;
                    $studentDiscipline->test1_result_points = null;
                    $studentDiscipline->test1_result_letter = null;
                    $studentDiscipline->test1_date = null;
                    $studentDiscipline->test1_result_trial = false;
                    $studentDiscipline->test1_blur = false;
                    $studentDiscipline->test1_qr_checked = false;

                    $studentDiscipline->test_result = null;
                    $studentDiscipline->test_result_points = null;
                    $studentDiscipline->test_result_letter = null;
                    $studentDiscipline->test_date = null;
                    $studentDiscipline->test_result_trial = false;
                    $studentDiscipline->test_blur = false;
                    $studentDiscipline->test_manual = false;
                    $studentDiscipline->test_qr_checked = false;

                    $studentDiscipline->final_result = null;
                    $studentDiscipline->final_result_points = null;
                    $studentDiscipline->final_result_gpa = null;
                    $studentDiscipline->final_result_letter = null;
                    $studentDiscipline->final_date = null;
                    $studentDiscipline->final_manual = false;

                    $studentDiscipline->task_result = null;
                    $studentDiscipline->task_result_points = null;
                    $studentDiscipline->task_result_letter = null;
                    $studentDiscipline->task_date = null;
                    $studentDiscipline->task_blur = false;
                    $studentDiscipline->task_manual = false;

                    $row[] = 'clear ratings';
                }

                if(trim($action) == 'перенести рекомендуемый семестр с 1 на  4')
                {
                    $studentDiscipline->recommended_semester = 4;
                    $row[] = 'change recommended semester';
                }

                /*if($action == 'Засчитано бесплатно')
                {
                    $studentDiscipline->migrated = 1;
                    $studentDiscipline->payed = 1;
                    $row[] = 'add migrated';
                }*/

                $studentDiscipline->save();
            }
            else
            {
                $row[] = 'not found';
            }

            fputcsv($reportFile, $row);
            $this->output->progressAdvance();
        }

        fclose($reportFile);
        $this->output->progressFinish();
    }

    /**
     * @param $studentDisciplineId
     * @return array
     * @throws \Exception
     */
    public function payCancel($studentDisciplineId)
    {
        $payDocList = PayDocument
            ::where('student_discipline_id', $studentDisciplineId)
            ->where('status', PayDocument::STATUS_SUCCESS)
            ->get();

        $log = [];

        foreach ($payDocList as $payDoc)
        {
            $result = Service1C::deletePay($payDoc->id);

            if ($result && isset($result['Ошибка']) && $result['Ошибка'] === '') {
                $log[] = 'success (' . $payDoc->id . ')';
                $payDoc->status = PayDocument::STATUS_CANCEL;
                $payDoc->save();
            }
            else
            {
                $log[] = 'fail (' . $payDoc->id . ')';
            }
        }

        return $log;
    }
}
