<?php

namespace App\Console\Commands\Fix;

use App\PayDocument;
use App\Profiles;
use App\Services\Service1C;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class MassPayDisciplinesClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mass:pay:disciplines:clear';

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
        $file = fopen(storage_path('import/mass_pay_disciplines_report_clear.csv'), 'r');
        //$reportFile = fopen(storage_path('import/mass_pay_disciplines_report_clear.csv'), 'w');
        $rowCount = sizeof (file (storage_path('import/mass_pay_disciplines_report_clear.csv')));

        $this->output->progressStart($rowCount);
        $upd = 0;

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $userId = $row[0];
            $disciplineId = $row[3];
            $credits = $row[5];
            $status = $row[9] ?? '';


            if($status == 'del success')
            {
                $studentDiscipline = StudentDiscipline
                    ::where('student_id', $userId)
                    ->where('discipline_id', $disciplineId)
                    ->first();

                if($studentDiscipline->payed_credits - $credits >= 0)
                {
                    StudentDiscipline
                        ::where('id', $studentDiscipline->id)
                        ->update([
                            'payed_credits' => $studentDiscipline->payed_credits - $credits,
                            'payed' => 0
                        ]);
                    $upd++;
                    $this->info('success');
                }
                else
                {
                    $this->info('min');
                }

            }

            /*$profile = Profiles::where('user_id', $userId)->first();

            if($profile)
            {
                $studentDiscipline = StudentDiscipline
                    ::with('discipline')
                    ->where('student_id', $userId)
                    ->where('discipline_id', $disciplineId)
                    ->first();

                if($studentDiscipline)
                {
                    $payDocument = PayDocument
                        ::where('student_discipline_id', $studentDiscipline->id)
                        ->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") = ' . '"' . date('Y-m-d', time()) . '"', '=')
                        ->orderBy('id', 'desc')
                        ->first();

                    if($payDocument)
                    {
                        $deleteResult = Service1C::deletePay($payDocument->id, false);

                        $row[] = $payDocument->id;
                        $row[] = $payDocument->amount;

                        if ($deleteResult && isset($deleteResult['Ошибка']) && $deleteResult['Ошибка'] === '') {
                            $payDocument->status = PayDocument::STATUS_CANCEL;
                            $payDocument->save();

                            $row[] = 'del success';
                        }
                        else
                        {
                            $row[] = 'del fail';
                        }

                    }
                    else
                    {
                        $row[] = 'pay document not found';
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

            fputcsv($reportFile, $row);*/
            $this->output->progressAdvance();
        }
        $this->info($upd);

        //fclose($reportFile);
        $this->output->progressFinish();
    }
}
