<?php

namespace App\Console\Commands\Import;

use App\FinanceNomenclature;
use App\PayDocument;
use App\PayDocumentStudentDiscipline;
use App\Profiles;
use App\Services\Service1C;
use App\StudentDiscipline;
use App\StudentFinanceNomenclature;
use Illuminate\Console\Command;

class RemoteAccessPay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remote_access:pay';

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
        $file = fopen(storage_path('import/remote_access_pay.csv'), 'r');
        $fileReport = fopen(storage_path('import/remote_access_pay_report.csv'), 'w');
        $fileRowCount = sizeof (file (storage_path('import/remote_access_pay.csv')));
        $this->output->progressStart($fileRowCount);

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $userId = $row[0];
            $disciplineId = $row[2];
            $sum = $row[4];
            $needPayDiscipline = $row[6];

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
                    if($needPayDiscipline == 'купить эту дисциплину ')
                    {
                        $creditPrice = $profile->user->getCreditPrice('2019-20.2');
                        $payResult = $this->payDiscipline($studentDiscipline, $profile, $studentDiscipline->discipline->ects, $creditPrice);

                        if($payResult)
                        {
                            $studentDiscipline->payed_credits = $studentDiscipline->discipline->ects;
                            $studentDiscipline->payed = 1;
                            $studentDiscipline->save();
                        }

                        $row[] =  $payResult ? 'success pay discipline' : 'fail pay discipline';
                    }


                    $creditPrice = $profile->user->remote_access_price;
                    $financeNomenclature = FinanceNomenclature
                        ::getRemoteAccess($studentDiscipline->discipline->ects, $creditPrice);

                    StudentDiscipline::setPayProcessing($studentDiscipline->id, true);
                    $balanceBeforeCall = $profile->user->balance;
                    $success = Service1C::pay($profile->iin, $financeNomenclature->code, $sum);

                    if ($success) {

                        StudentFinanceNomenclature::addRemoteAccess(
                            $profile->user_id,
                            $financeNomenclature,
                            $profile->currentSemester(),
                            $balanceBeforeCall,
                            $studentDiscipline->id
                        );

                        $studentDiscipline->setRemoteAccess();
                        StudentDiscipline::setPayProcessing($studentDiscipline->id, false);

                        $row[] = 'success distant pay';

                    } else {

                        StudentDiscipline::setPayProcessing($studentDiscipline->id, false);

                        $row[] = 'fail distant pay';
                    }

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

            fputcsv($fileReport, $row);
            $this->output->progressAdvance();
        }

        fclose($fileReport);
        $this->output->progressFinish();
    }

    /**
     * @param $studentDiscipline
     * @param $profile
     * @param $credits
     * @param $creditPrice
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

        if($amount == 0)
        {
            return true;
        }

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

        if (($payResult && isset($payResult['Ошибка']) && $payResult['Ошибка'] === '') || env('API_1C_ENABLED', false)) {
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
