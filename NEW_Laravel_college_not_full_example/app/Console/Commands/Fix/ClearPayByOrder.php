<?php

namespace App\Console\Commands\Fix;

use App\DisciplinePayCancel;
use App\PayDocument;
use App\StudentDiscipline;
use App\StudentSubmodule;
use Illuminate\Console\Command;

class ClearPayByOrder extends Command
{
    const STUDENT_ID = 0;
    const DISCIPLINE_ID = 4;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:pay:by_order {--clear_test_result=false}';

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
        $clearTestResults = $this->option('clear_test_result');
        $clearTestResults = $clearTestResults == 'true' ? true : false;

        if($clearTestResults)
        {
            $this->info('clear_test_results mode ON');
        }

        $file = fopen(storage_path('import/pay_cancel_list.csv'), 'r');
        $updatedCount = 0;
        $updatedPayDocumentCount = 0;
        $rowCount = sizeof (file (storage_path('import/pay_cancel_list.csv')));

        $this->output->progressStart($rowCount);

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $studentId = $row[self::STUDENT_ID];
            $disciplineId = $row[self::DISCIPLINE_ID];

            if(!$disciplineId || !$studentId)
            {
                continue;
            }

            $studentDiscipline = StudentDiscipline
                ::where('student_id', $studentId)
                ->where('discipline_id', $disciplineId)
                ->whereNotNull('payed_credits')
                ->first();

            if($studentDiscipline)
            {
                /*set cancel status to pay documents*/
                $payDocumentList = PayDocument
                    ::select(['pay_documents.*'])
                    ->leftJoin('pay_documents_student_disciplines', 'pay_documents_student_disciplines.pay_document_id', '=', 'pay_documents.id')
                    ->where('pay_documents_student_disciplines.student_discipline_id', $studentDiscipline->id)
                    ->where('pay_documents.status', PayDocument::STATUS_SUCCESS)
                    ->get();

                foreach ($payDocumentList as $payDocument)
                {
                    $payDocument->status = PayDocument::STATUS_CANCEL;
                    $payDocument->save();
                    $updatedPayDocumentCount++;
                }

                /*set discipline_pay_cancel*/
                DisciplinePayCancel
                    ::where('discipline_id', $studentDiscipline->discipline_id)
                    ->where('user_id', $studentDiscipline->student_id)
                    ->where('status', DisciplinePayCancel::STATUS_APPROVE)
                    ->update([
                        'executed_1c' => true,
                        'executed_miras' => true
                    ]);

                if(!$studentDiscipline->submodule_id)
                {
                    $studentDiscipline->payed = 0;
                    $studentDiscipline->payed_credits = null;
                    $studentDiscipline->pay_processing = false;

                    if($clearTestResults)
                    {
                        $this->clearTestResults($studentDiscipline);
                    }

                    $studentDiscipline->save();
                }
                else
                {
                    $this->info('Submodule user_id = ' . $studentDiscipline->student_id . ' Submodule_id = ' . $studentDiscipline->submodule_id);

                    $studentSubmodule = new StudentSubmodule();
                    $studentSubmodule->student_id = $studentDiscipline->student_id;
                    $studentSubmodule->submodule_id = $studentDiscipline->submodule_id;
                    $studentSubmodule->save();

                    $studentDiscipline->delete();
                }

                $updatedCount++;
            }
            else
            {
                $this->warn('Relation not found ' . $studentId . ' - ' . $disciplineId);
            }

            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
        $this->info('Updated count: ' . $updatedCount);
        $this->info('Updated document count: ' . $updatedPayDocumentCount);
    }

    /**
     * @param $studentDiscipline
     */
    public function clearTestResults($studentDiscipline)
    {
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

        $studentDiscipline->save();
    }
}
