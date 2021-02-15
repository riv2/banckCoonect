<?php

namespace App\Console\Commands\Fix;

use App\PayDocument;
use App\Services\Service1C;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class CancelPayLimit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pay_limit:cancel';

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
        $file = fopen(storage_path('import/cancel_credits_to_limit.csv'), 'r');
        $fileLog = fopen(storage_path('export/cancel_credits_to_limit_' . date('Y-m-d') . '.csv'), 'w');
        $rowCount = sizeof (file (storage_path('import/cancel_credits_to_limit.csv')));

        $this->output->progressStart($rowCount);
        $updated = 0;
        $updateLimit = 2;
        $validUserList = $this->getValidUserList();
        $start = 0;
        $i=0;

        while(/*$updated < $updateLimit &&*/ $row = fgetcsv($file, 0, ',', '"'))
        {
            if($i < $start)
            {
                $this->output->progressAdvance();
                $i++;
                continue;
            }

            $i++;
            $userId = $row[0];
            $disciplineId = $row[9];

            if( true /*in_array($userId, $validUserList)*/ )
            {
                $studentDiscipline = StudentDiscipline
                    ::where('student_id', $userId)
                    ->where('discipline_id', $disciplineId)
                    ->first();

                if($studentDiscipline)
                {
                    $payDoc = PayDocument
                        ::where('student_discipline_id', $studentDiscipline->id)
                        ->orderBy('id', 'desc')
                        ->first();

                    if($payDoc)
                    {
                        $deleteResult = Service1C::deletePay($payDoc->id);

                        if( isset($deleteResult['Ошибка']) && $deleteResult['Ошибка'] === '' )
                        {
                            $payDoc->status = PayDocument::STATUS_CANCEL;
                            $payDoc->save();

                            $restoreCredits = $studentDiscipline->payed_credits - $payDoc->credits;

                            $studentDiscipline->payed = 0;
                            $studentDiscipline->payed_credits = ($restoreCredits > 0) ? $restoreCredits : 0;
                            $studentDiscipline->save();

                            $updated++;

                            $this->logCsv($row, 'Success', $fileLog);
                        }
                        else
                        {
                            $this->logCsv($row, '1c delete fail', $fileLog);
                        }
                    }
                    else
                    {
                        $this->logCsv($row, 'Pay document not found', $fileLog);
                    }
                }
                else
                {
                    $this->logCsv($row, 'StudentDiscipline not found', $fileLog);
                }

            }

            $this->output->progressAdvance();
        }

        fclose($fileLog);
        $this->output->progressFinish();
    }

    public function getValidUserList()
    {
        return [];
    }

    /**
     * @param $row
     * @param $status
     * @param $file
     */
    public function logCsv($row, $status, $file)
    {
        $row[] = $status;
        fputcsv($file, $row);
    }
}
