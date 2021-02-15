<?php

namespace App\Console\Commands\Fix;

use App\PayDocument;
use App\Services\Service1C;
use App\User;
use Illuminate\Console\Command;

class FixZeroPays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:zero:pays';

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
        $logFile = fopen(storage_path('export/fix_zero_pays.csv'), 'w');

        $payDocumentList = PayDocument
            ::where('created_at', '>=', '2020-03-23')
            ->where('created_at', '<=', '2020-03-27 ')
            ->where('amount', 0)
            ->get();

        fputcsv($logFile, [
            'ID',
            'ФИО',
            'ИИН',
            'pay ID',
            'amount',
            'credits',
            'credit price',
            'need pay',
            'pay date',
            'Оплата'
        ]);

        $this->output->progressStart(count($payDocumentList));

        foreach ($payDocumentList as $payDocument)
        {
            $user = User
                ::where('id', $payDocument->user_id)
                ->first();

            $creditPrice = $user->getCreditPrice('2019-20.2');

            if($creditPrice > 0)
            {
                $fio = $user->studentProfile->fio;
                $iin = $user->studentProfile->iin;
                $needPay = $payDocument->credits * $creditPrice;

                $payResult = Service1C::sendRequest(
                    Service1C::API_PAY,
                    [
                        'id_miras_app' => $payDocument->id,
                        'iin_list' => [$iin],
                        'code' => Service1C::NOMENCLATURE_CODE_DISCIPLINE,
                        'cost' => "$needPay"
                    ]
                );

                if ($payResult && isset($payResult['Ошибка']) && $payResult['Ошибка'] === '') {
                    $presult = true;
                    $payDocument->amount = $needPay;
                    $payDocument->save();
                }
                else
                {
                    $presult = false;
                }

                fputcsv($logFile, [
                    $user->id,
                    $fio,
                    $iin,
                    $payDocument->id,
                    $payDocument->amount,
                    $payDocument->credits,
                    $creditPrice,
                    $needPay,
                    $payDocument->created_at,
                    $presult ? 'success' : $payResult['Ошибка']
                ]);
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        fclose($logFile);
    }
}
