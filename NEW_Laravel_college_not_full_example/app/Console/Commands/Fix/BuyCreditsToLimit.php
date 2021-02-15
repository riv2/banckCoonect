<?php

namespace App\Console\Commands\Fix;

use App\Discipline;
use App\PayDocument;
use App\Services\Service1C;
use App\StudentDiscipline;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class BuyCreditsToLimit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buy_credits:to_limit';

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
        $file = fopen(storage_path('import/buy_credits_to_limit.csv'), 'r');
        $fileLog = fopen(storage_path('export/buy_credits_to_limit_' . date('Y-m-d') . '.csv'), 'w');
        $rowCount = sizeof (file (storage_path('import/buy_credits_to_limit.csv')));

        $this->output->progressStart($rowCount);
        $updated = 0;
        $maxUpdated = 7523;

        while($updated <= $maxUpdated && $row = fgetcsv($file, 0, ',', '"'))
        {
            $this->output->progressAdvance();

            $userId = $row[0];
            $disciplineId = $row[9];

            //$this->info($disciplineId);

            $user = User::with('studentProfile')->whereHas('studentProfile')->where('id', $userId)->first();

            if(!$user)
            {
                $this->warn('User not found');
                $this->logCsv($row, 'User not found', $fileLog);
                continue;
            }

            $discipline = Discipline::where('id', $disciplineId)->first();

            if(!$discipline)
            {
                $this->warn('Discipline not found');
                $this->logCsv($row, 'Discipline not found', $fileLog);
                continue;
            }

            $studentDiscipline = StudentDiscipline
                ::where('student_id', $userId)
                ->where('discipline_id', $disciplineId)
                ->first();

            if(!$studentDiscipline)
            {
                $this->warn('StudentDiscipline not found');
                $this->logCsv($row, 'StudentDiscipline not found', $fileLog);
                continue;
            }

            $credits = $discipline->ects - $studentDiscipline->payed_credits;

            if($credits <= 0)
            {
                $this->logCsv($row, 'Already payed', $fileLog);
                continue;
            }

            $amount = $credits * $user->credit_price;
            $user->refreshBalance();

            $userBalance = $user->balanceByDebt();

            if(($userBalance - $amount) >= 0)
            {
                $payDocument = PayDocument::createForStudentDiscipline(
                    $user->id,
                    time() . $studentDiscipline->id,
                    $amount,
                    $credits,
                    $studentDiscipline->id,
                    $user->balance
                );

                // Pay processing ON
                StudentDiscipline::setPayProcessing($studentDiscipline->id, true);

                $paySuccess = Service1C::payDiscipline($user->studentProfile->iin, $amount, $payDocument);

                if ($paySuccess) {
                    $payDocument->status = PayDocument::STATUS_SUCCESS;
                    $payDocument->save();

                    if (!$studentDiscipline->payed_credits) {
                        $path = 'student_discipline_credits_limit:' . $studentDiscipline->student_id . ':' . $user->studentProfile->currentSemester();
                        $creditsLimit = Redis::get($path);
                        Redis::set($path, $creditsLimit + $studentDiscipline->discipline->ects);
                    }

                    $studentDiscipline->payed_credits += $credits;

                    if ($studentDiscipline->payed_credits >= $studentDiscipline->discipline->ects - $studentDiscipline->free_credits) {
                        $studentDiscipline->payed = true;
                    }

                    $studentDiscipline->save();

                    // Pay processing OFF
                    StudentDiscipline::setPayProcessing($studentDiscipline->id, false);

                } else {
                    $payDocument->status = PayDocument::STATUS_FAIL;
                    $payDocument->save();

                    // Pay processing OFF
                    StudentDiscipline::setPayProcessing($studentDiscipline->id, false);
                }
            }
            else
            {
                $this->logCsv($row, 'Low balance', $fileLog);
                continue;
            }

            $updated++;
            $this->logCsv($row, 'Success', $fileLog);
        }

        $this->output->progressFinish();
        fclose($fileLog);
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
