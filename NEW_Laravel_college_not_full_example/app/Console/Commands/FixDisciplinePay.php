<?php

namespace App\Console\Commands;

use App\PayDocument;
use App\Profiles;
use App\Services\Service1C;
use App\StudentDiscipline;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FixDisciplinePay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:discipline:pay';

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

        $studentDisciplineList = StudentDiscipline
            ::with('discipline')
            //->where('updated_at', '>=', '2019-10-03')
            //->where('payed', true)
            ->whereNotNull('payed_credits')
            ->whereIn('student_id', [
                14038
            ])
            ->get();

        $diffCount = 0;
        $allCount = 0;

        $this->output->progressStart(count($studentDisciplineList));

        foreach ($studentDisciplineList as $studentDiscipline)
        {
            $user = User::with('studentProfile')->where('id', $studentDiscipline->student_id)->first();

            if($user)
            {
                $log = ['id = ' . $studentDiscipline->id];
                $log[] = 'user_id = ' . $studentDiscipline->student_id;
                $log[] = 'payed_credits = ' . $studentDiscipline->payed_credits;

                /*$credits = StudentDiscipline::getCreditsCountForBuy($studentDiscipline->discipline->ects, 0, $studentDiscipline->free_credits);

                if($studentDiscipline->payed_credits > $credits)
                {
                    $studentDiscipline->payed_credits = $credits;
                    //$studentDiscipline->save();
                    $diffCount++;
                }*/

                $log[] = 'payed_credits new = ' . $studentDiscipline->payed_credits;

                $amount = $studentDiscipline->payed_credits * $user->credit_price;
                $log[] = 'amount = ' . $amount;

                $payResult = 'none';

                if($amount > 0) {
                    $nomenclatureCode = '00000006539';
                    //$payResult = Service1C::pay($user->studentProfile->iin, $nomenclatureCode, $amount);
                }
                $log[] = '1c_result = ' . $payResult;

                Log::info('Repay discipline: ' . implode(' : ', $log));
                $this->info('Repay discipline: ' . implode(' : ', $log));
                $allCount++;
            }

            $this->output->progressAdvance();
        }
        $this->output->progressFinish();

        $this->info('All count: ' . $allCount);
        $this->info('Diff count: ' . $diffCount);
    }
}
