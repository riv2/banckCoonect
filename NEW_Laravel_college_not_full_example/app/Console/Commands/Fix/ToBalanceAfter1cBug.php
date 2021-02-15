<?php

namespace App\Console\Commands\Fix;

use App\PayDocument;
use App\Profiles;
use App\Services\Service1C;
use Illuminate\Console\Command;

class ToBalanceAfter1cBug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'to_balance:after:1c_bug';

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
        $file = fopen(storage_path('import/to_balance_after_1c_bug.txt'), 'r');
        $reportFile = fopen(storage_path('import/to_balance_after_1c_bug_report.csv'), 'w');
        $rowCount = sizeof (file (storage_path('import/to_balance_after_1c_bug.txt')));

        $this->output->progressStart($rowCount);

        while($row = fgets($file))
        {
            $request = json_decode($row, true);
            $reportRow = [];

            if($request)
            {
                $reportRow = $request;
                $profile = Profiles::where('iin', $request['iin'])->first();

                if($profile)
                {
                    $payDocument = new PayDocument();
                    $payDocument->order_id = 0;
                    $payDocument->user_id = $profile->user_id;
                    $payDocument->student_discipline_id = null;
                    $payDocument->amount = $request['summa'];
                    $payDocument->credits = null;
                    $payDocument->status = PayDocument::STATUS_PROCESS;
                    $payDocument->complete_pay = 0;
                    $payDocument->type = PayDocument::TYPE_TO_BALANCE;
                    $payDocument->save();

                    $payResult = Service1C::sendRequest(Service1C::API_ADD_TO_BALANCE, $request);

                    if ($payResult && isset($payResult['Ошибка']) && $payResult['Ошибка'] === '') {
                        $payDocument->status = PayDocument::STATUS_SUCCESS;
                        $payDocument->save();
                        $reportRow[] = 'success';
                    }
                    else
                    {
                        $payDocument->status = PayDocument::STATUS_FAIL;
                        $payDocument->save();
                        $reportRow[] = 'fail';
                    }

                }
                else
                {
                    $reportRow[] = 'profile not found';
                }
            }

            $this->output->progressAdvance();
            fputcsv($reportFile, $reportRow);
        }

        $this->output->progressFinish();
        fclose($reportFile);

    }
}
