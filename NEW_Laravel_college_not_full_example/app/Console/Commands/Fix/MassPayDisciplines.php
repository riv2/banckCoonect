<?php

namespace App\Console\Commands\Fix;

use App\PayDocument;
use App\PayDocumentStudentDiscipline;
use App\Profiles;
use App\Services\Service1C;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class MassPayDisciplines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mass:pay:disciplines';

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
        $file = fopen(storage_path('import/mass_pay_disciplines.csv'), 'r');
        $reportFile = fopen(storage_path('import/mass_pay_disciplines_report.csv'), 'w');
        //$reportFile2 = fopen(storage_path('import/mass_pay_disciplines_report2.csv'), 'w');
        $rowCount = sizeof (file (storage_path('import/mass_pay_disciplines.csv')));

        /*while($row = fgetcsv($reportFile, 0, ',', '"'))
        {
            $userId = $row[0];

            $profile = Profiles::with('user')->where('user_id', $userId)->first();

            if($profile)
            {
                $this->info($userId);
                $row[] = $profile->user->getCreditPrice(1);
            }

            fputcsv($reportFile2, $row);
        }

        fclose($reportFile2);*/


        $this->output->progressStart($rowCount);

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $userId = $row[0];
            $disciplineId = $row[14];
            $credits = $row[16];

            $profile = Profiles::where('user_id', $userId)->first();

            if($profile)
            {
                $studentDiscipline = StudentDiscipline
                    ::with('discipline')
                    ->where('student_id', $userId)
                    ->where('discipline_id', $disciplineId)
                    ->first();

                if($studentDiscipline)
                {
                    $creditPrice = $profile->user->getCreditPrice('2019-20.1');

                    if($creditPrice)
                    {
                        if( $this->payDiscipline($studentDiscipline, $profile, $credits, $creditPrice) )
                        {
                            $studentDiscipline->payed_credits = $studentDiscipline->payed_credits + $credits;

                            if($studentDiscipline->payed_credits >= $studentDiscipline->discipline->ects)
                            {
                                $studentDiscipline->payed = 1;
                            }

                            $studentDiscipline->save();
                            $row[] = 'success';
                        }
                        else
                        {
                            $row[] = 'error payed 1c';
                        }
                    }
                    else
                    {
                        $row[] = 'error credit price';
                    }

                    $row[] = $creditPrice;

                }
                else
                {
                    $row[] = 'student_discipline not found';
                }
            }
            else
            {
                $row[] = 'profile not found';
            }

            fputcsv($reportFile, $row);
            $this->output->progressAdvance();
        }

        fclose($reportFile);
        $this->output->progressFinish();
    }

    /**
     * @param $studentDiscipline
     * @param $profile
     * @param $credits
     * @return bool
     * @throws \Exception
     */
    public function payDiscipline($studentDiscipline, $profile, $credits, $creditPrice)
    {
        $amount = $credits * $creditPrice;

        $payDocument = new PayDocument();
        $payDocument->order_id = 0;
        $payDocument->user_id = $profile->user_id;
        $payDocument->student_discipline_id = $studentDiscipline->id;
        $payDocument->amount = $amount;
        $payDocument->credits = $credits;
        $payDocument->status = PayDocument::STATUS_PROCESS;
        $payDocument->complete_pay = 0;
        $payDocument->save();

        $payDocumentStudentDiscipline = new PayDocumentStudentDiscipline();
        $payDocumentStudentDiscipline->pay_document_id = $payDocument->id;
        $payDocumentStudentDiscipline->student_discipline_id = $studentDiscipline->id;
        $payDocumentStudentDiscipline->save();

        $payResult = Service1C::sendRequest(Service1C::API_PAY, [
            'id_miras_app' => $payDocument->id,
            'iin_list' => [$profile->iin],
            'code' => Service1C::NOMENCLATURE_CODE_DISCIPLINE,
            'cost' => "$amount"
        ]);

        if ($payResult && isset($payResult['Ошибка']) && $payResult['Ошибка'] === '') {
            $payDocument->status = PayDocument::STATUS_SUCCESS;
            $payDocument->save();
            return true;
        }
        else
        {
            $payDocument->status = PayDocument::STATUS_FAIL;
            $payDocument->save();
        }

        return false;
    }
}
