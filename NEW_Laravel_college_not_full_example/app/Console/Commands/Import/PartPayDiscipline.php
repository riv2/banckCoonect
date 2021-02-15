<?php

namespace App\Console\Commands\Import;

use App\Discipline;
use App\FinanceNomenclature;
use App\PayDocument;
use App\PayDocumentStudentDiscipline;
use App\Profiles;
use App\Services\Service1C;
use App\StudentDiscipline;
use App\StudentFinanceNomenclature;
use Illuminate\Console\Command;

class PartPayDiscipline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disciplines:pay:part';

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
        $file = fopen(storage_path('import/disciplines_pay_part.csv'), 'r');
        $fileReport = fopen(storage_path('import/disciplines_pay_part_report.csv'), 'w');
        $fileRowCount = sizeof (file (storage_path('import/disciplines_pay_part.csv')));
        $this->output->progressStart($fileRowCount);

        while($row = fgetcsv($file, 0, ',', '"')) {

            $userId = $row[0];
            $disciplineId = $row[3];
            $credits = $row[4];
            $distant = $row[5];
            $trial = $row[6];
            $consult = $row[7];

            $studentDiscipline = StudentDiscipline
                ::with('discipline')
                ->where('student_id', $userId)
                ->where('discipline_id', $disciplineId)
                ->first();

            if($studentDiscipline)
            {
                $profile = Profiles::where('user_id', $userId)->first();

/*--------------Credist pay */
                if($credits && is_numeric($credits))
                {
                    if(($studentDiscipline->payed_credits + $credits) <= $studentDiscipline->discipline->ects)
                    {
                        $creditPrice = $profile->user->getCreditPrice('2019-20.1');
                        $payResult = $this->payDiscipline($studentDiscipline, $profile, $credits, $creditPrice);

                        if($payResult)
                        {
                            $studentDiscipline->payed_credits = $studentDiscipline->payed_credits + $credits;
                            $studentDiscipline->payed = $studentDiscipline->payed_credits >= $studentDiscipline->discipline->ects;
                            $studentDiscipline->save();
                        }

                        $row[] =  $payResult ? 'success pay credits' : 'fail pay credits';
                    }
                    else
                    {
                        $row[] = 'payed_credits upward ects';
                    }
                }

/*--------------Distant pay*/
                if($distant && is_numeric($distant))
                {
                    $creditPrice = $profile->user->remote_access_price;
                    $financeNomenclature = FinanceNomenclature
                        ::getRemoteAccess($studentDiscipline->discipline->ects, $creditPrice);

                    StudentDiscipline::setPayProcessing($studentDiscipline->id, true);
                    $balanceBeforeCall = $profile->user->balance;
                    $success = Service1C::pay($profile->iin, $financeNomenclature->code, $financeNomenclature->cost);

                    if ($success) {

                        StudentFinanceNomenclature::addRemoteAccess(
                            $profile->user_id,
                            $financeNomenclature,
                            $profile->currentSemester() - 1,
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

/*--------------Trial pay*/
                if($trial && is_numeric($trial))
                {
                    $payResult = $this->payByNomenclature($profile, 'БК000015067', $trial);
                    $row[] = $payResult ? 'success trial pay' : 'fail trial pay';
                }

/*--------------Consult pay*/
                if($consult && is_numeric($consult))
                {
                    $payResult = $this->payByNomenclature($profile, 'БК000078119', $consult);
                    $row[] = $payResult ? 'success consult pay' : 'fail consult pay';
                }

            }
            else
            {
                $row[] = 'student_discipline not found';
            }

            fputcsv($fileReport, $row);
            $this->output->progressAdvance();
        }

        fclose($fileReport);
        $this->output->progressFinish();
    }

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

    /**
     * @param $profile
     * @param $nomenclature
     * @param $amount
     * @return bool
     * @throws \Exception
     */
    public function payByNomenclature($profile, $nomenclature, $amount)
    {
        $payResult = Service1C::sendRequest(Service1C::API_PAY, [
            'iin_list' => [$profile->iin],
            'code' => $nomenclature,
            'cost' => "$amount"
        ]);

        if (($payResult && isset($payResult['Ошибка']) && $payResult['Ошибка'] === '') || env('API_1C_ENABLED', false)) {
            return true;
        }

        return false;
    }
}
