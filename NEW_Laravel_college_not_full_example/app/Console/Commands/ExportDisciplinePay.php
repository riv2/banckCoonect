<?php

namespace App\Console\Commands;

use App\Http\Controllers\Student\PromotionController;
use App\Profiles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExportDisciplinePay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:discipline:pay';

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
        $fileName = storage_path('export/export_discipline_pay.csv');
        $file = fopen($fileName, 'w');

        $profileList = Profiles
            ::select([
                'id',
                'user_id',
                'fio',
                'iin',
                'discount',
                DB::raw('payed_credit_sum(profiles.user_id) as payed_credits')
            ])
            ->with('user')
            ->whereHas('user')
            ->whereRaw('payed_credit_sum(profiles.user_id) > 0');

        $count = $profileList;
        $count = $count->count();

        fputcsv($file, [
            'ID',
            'ФИО',
            'ИИН',
            'Количество купленных кредитов',
            'Стоимость кредита',
            'Скидка',
            'Сумма потраченная',
            'Баланс'
        ]);

        $this->output->progressStart($count);

        $profileList->chunk(500, function($rows) use($file){
            foreach ($rows as $profile)
            {
                fputcsv($file, [
                    $profile->user_id,
                    $profile->fio,
                    "'" . $profile->iin,
                    $profile->payed_credits,
                    $profile->user->credit_price,
                    $profile->discount,
                    $profile->payed_credits * $profile->user->credit_price,
                    $profile->user->balance
                ]);

                $this->output->progressAdvance();
            }
        });

        $this->output->progressFinish();
        fclose($file);
    }
}
