<?php

namespace App\Console\Commands\Fix;

use App\Discipline;
use App\Services\Service1C;
use App\StudentDiscipline;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RestoreElectives extends Command
{
    const STUDENT_ID = 0;
    const FIO = 1;
    const IIN = 2;
    const DISCIPLINE_NAME = 3;
    const DISCIPLINE_ID = 4;
    const CREDITS = 5;
    const COST = 6;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:pay:elective';

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
        $file = fopen(storage_path('import/pay_electives.csv'), 'r');
        $fileReport = fopen(storage_path('export/pay_electives_2000.csv'), 'w');
        $updatedCount = 0;

        $this->output->progressStart();

        while($row = fgetcsv($file, 0, ',', "'"))
        {
            $studentId = $row[self::STUDENT_ID];
            $disciplineId = $row[self::DISCIPLINE_ID];
            $credits = $row[self::CREDITS];
            $cost = $row[self::COST];

            if(!$disciplineId || !$studentId)
            {
                continue;
            }

            $studentDiscipline = StudentDiscipline
                ::select('students_disciplines.*')
                ->leftJoin('profiles', 'profiles.user_id', '=', 'students_disciplines.student_id')
                ->where('student_id', $studentId)
                ->where('discipline_id', $disciplineId)
                ->where('is_elective', true)
                ->where('profiles.course', 1)
                ->first();

            if($studentDiscipline)
            {
                if($cost == 2000) {
                    $user = User::with('studentProfile')->where('id', $studentId)->first();
                    if ($user) {

                        /*$log = ['id = ' . $studentDiscipline->student_id];

                        $amount = 2000;
                        $log[] = 'amount = ' . $amount;

                        $payResult = 'none';

                        if($amount > 0 && !empty($user->studentProfile->iin)) {
                            $nomenclatureCode = '00000003516';
                            $payResult = Service1C::pay($user->studentProfile->iin, $nomenclatureCode, $amount);
                        }
                        $log[] = '1c_result = ' . $payResult;

                        Log::info('Repay discipline: ' . implode(' : ', $log));*/

                        /*fputcsv($fileReport, [
                            $studentId,
                            $row[self::FIO],
                            "'" . $row[self::IIN],
                            $row[self::DISCIPLINE_NAME],
                            $row[self::DISCIPLINE_ID],
                            $row[self::CREDITS],
                            $amount,
                            '14.11.2019'
                        ]);*/
                    }
                    $updatedCount++;
                }
            }
            else
            {
                $this->warn('Relation not found ' . $studentId . ' - ' . $disciplineId);
            }

            $this->output->progressAdvance();
        }

        fclose($file);
        fclose($fileReport);

        $this->output->progressFinish();
        $this->info('Updated count: ' . $updatedCount);
    }
}
