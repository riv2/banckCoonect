<?php

namespace App\Console\Commands\Fix;

use App\Discipline;
use App\PayDocument;
use App\PayDocumentStudentDiscipline;
use App\Profiles;
use App\Services\Service1C;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class BuyDisciplinesAndSetRating extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buy:disciplines:set:rating {--part=1}';

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

        $file = fopen(storage_path('import/buy_disciplines_set_rating_' . $part . '.csv'), 'r');
        $reportFile = fopen(storage_path('import/buy_disciplines_set_rating_report_' . $part . '.csv'), 'w');
        $fileCount = sizeof(file (storage_path('import/buy_disciplines_set_rating_' . $part . '.csv')));

        $this->output->progressStart($fileCount);

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            if($part == 1)
            {
                $log = $this->actionForPart1($row);
            }

            if($part == 2)
            {
                $log = $this->actionForPart2($row);
            }

            if($part == 3)
            {
                $log = $this->actionForPart3($row);
            }

            fputcsv($reportFile, $log);
            $this->output->progressAdvance();
        }

        fclose($reportFile);
        $this->output->progressFinish();
    }

    /**
     * @param $str
     * @return mixed|null
     */
    public function getCredits($str)
    {
        $credits = preg_match('/[0-9]+/', $str, $matches);
        return $matches[0] ?? null;
    }

    /**
     * @param $studentDiscipline
     * @param $profile
     * @param $credits
     * @param $creditPrice
     * @return bool
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

    /**
     * @param $row
     * @return array
     */
    public function actionForPart1($row)
    {
        $userId = $row[0];
        $disciplineId = $row[2];
        $credits = null; //$this->getCredits($row[5]);
        $rating = $row[3];
        $comment = $row[5];
        //$ratingLetter = $row[6];
        //$ratingPoints = $row[7];

        $studentDiscipline = StudentDiscipline
            ::where('student_id', $userId)
            ->where('discipline_id', $disciplineId)
            ->first();

        if($studentDiscipline)
        {
            $profile = Profiles::where('user_id', $userId)->first();

            if($profile)
            {
                $discipline = Discipline::where('id', $disciplineId)->first();

                if($comment == 'купить дисциплину,проставить оценку')
                {
                    $credits = $discipline->ects;
                }

                if($credits ?? !$studentDiscipline->payed)
                {
                    $creditPrice = $profile->user->getCreditPrice('2019-20.1');


                    $payResult = $this->payDiscipline($studentDiscipline, $profile, $discipline->ects, $creditPrice);

                    if($payResult)
                    {
                        $studentDiscipline->payed = true;
                        $studentDiscipline->payed_credits = $credits;
                        $studentDiscipline->save();

                        $row[] = 'payed ' . $credits . ' credits. Price ' . $creditPrice;
                    }
                    else
                    {
                        $row[] = 'fail pay';
                    }
                }

                $studentDiscipline->setFinalResult($rating);
                $studentDiscipline->at_semester = 1;
                $studentDiscipline->save();
            }
            else
            {
                $row[] = 'profile not found';
            }
        }
        else
        {
            $row[] = 'student_discipline not found';
        }

        return $row;
    }

    /**
     * @param $row
     * @return array
     */
    public function actionForPart2($row)
    {
        $userId = $row[0];
        $disciplineId = $row[3];
        $credits = $this->getCredits($row[7]);
        $rating = $row[4];
        $ratingLetter = $row[6];
        $ratingPoints = $row[7];

        $studentDiscipline = StudentDiscipline
            ::where('student_id', $userId)
            ->where('discipline_id', $disciplineId)
            ->first();

        if($studentDiscipline)
        {
            $profile = Profiles::where('user_id', $userId)->first();

            if($profile)
            {
                if($credits)
                {
                    $creditPrice = $profile->user->getCreditPrice('2019-20.1');
                    $payResult = $this->payDiscipline($studentDiscipline, $profile, $credits, $creditPrice);

                    if($payResult)
                    {
                        $studentDiscipline->payed = true;
                        $studentDiscipline->payed_credits = $credits;
                        $studentDiscipline->save();

                        $row[] = 'payed ' . $credits . ' credits. Price ' . $creditPrice;
                    }
                    else
                    {
                        $row[] = 'fail pay';
                    }
                }

                $studentDiscipline->setFinalResult($rating);
                $studentDiscipline->migrated = false;
                $studentDiscipline->at_semester = 3;
                $studentDiscipline->save();
            }
            else
            {
                $row[] = 'profile not found';
            }
        }
        else
        {
            $row[] = 'student_discipline not found';
        }

        return $row;
    }

    /**
     * @param $row
     * @return array
     */
    public function actionForPart3($row)
    {
        $userId = $row[0];
        $disciplineId = $row[3];
        $rating = $row[4];
        $ratingLetter = $row[6];
        $ratingPoints = $row[7];
        $credits = null;//$this->getCredits($row[8]);

        $studentDiscipline = StudentDiscipline
            ::with('discipline')
            ->where('student_id', $userId)
            ->where('discipline_id', $disciplineId)
            ->first();

        if($studentDiscipline)
        {
            $profile = Profiles::where('user_id', $userId)->first();

            if($profile)
            {

                if(!$studentDiscipline->payed_credits || $credits) {
                    if (isset($studentDiscipline->discipline->ects) || $credits) {

                        $credits = $credits ? $credits : $studentDiscipline->discipline->ects;

                        $creditPrice = $profile->user->getCreditPrice('2019-20.1');
                        $payResult = $this->payDiscipline($studentDiscipline, $profile, $credits, $creditPrice);

                        if ($payResult) {
                            $studentDiscipline->payed = true;
                            $studentDiscipline->payed_credits = $credits;
                            $studentDiscipline->save();

                            $row[] = 'payed ' . $credits . ' credits. Price ' . $creditPrice;
                        } else {
                            $row[] = 'fail pay';
                        }
                    }
                }
                else
                {
                    $row[] = 'already payed';
                }

                $studentDiscipline->setFinalResult($rating);
                $studentDiscipline->migrated = false;
                $studentDiscipline->at_semester = 1;
                $studentDiscipline->save();
            }
            else
            {
                $row[] = 'profile not found';
            }

            $studentDiscipline->setFinalResult($rating);
            $studentDiscipline->migrated = false;
            $studentDiscipline->save();
        }
        else
        {
            $row[] = 'student_discipline not found';
        }

        return $row;
    }
}
