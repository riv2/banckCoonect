<?php

namespace App\Console\Commands\Export;

use App\Profiles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SumPayForDiscount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:sumpay:for:discount';

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
        $fileName = storage_path('export/export_sumpay_for_discount.csv');
        $file = fopen($fileName, 'w');
        $iinList = $this->getIinList();

        foreach ($iinList as $iin)
        {
            $profile = Profiles
                ::select([
                    'id',
                    'user_id',
                    DB::raw('payed_credit_sum(user_id) as payed_credits'),
                ])
                ->with('user')
                ->whereHas('user')
                ->where('iin', $iin)
                ->first();

            if($profile)
            {
                $this->info($iin);
                fputcsv($file, [
                    $profile->iin,
                    $profile->payed_credits,
                    $profile->user->credit_price,
                    $profile->payed_credits * $profile->user->credit_price
                ]);
            }
        }

        fclose($file);
    }

    public function getIinList()
    {
        return [
'990530301371',
'970924400526',
'960217401063',
'000925600721',
'981011400557',
'000421600305',
'990811301517',
'001202500519',
'991225300249',
'991223300654',
'000513500210',
'980410301426',
'020119501120',
'000802601146',
'990805400463',
'991216300854',
'010313500363',
'010127501093',
'000921601113',
'010320600863',
'010325600246',
'020523600086',
'010516600571',
'020402600925',
'020812600186',
'940107301749',
'020106600702',
'011001501378',
'990617400194',
'990811400931',
'000414600347',
'970721300635',
'020528602149',
'011014601593',
'001222500796',
'010704500864',
'010610501514',
'820816301293',
'910715301302',
'900220300857',
'000411600896',
'020625600982',
'980813401146',
'850505401630',
'980408400116',
'991101401010',
'000609601454',
'000116600456',
'000731600390',
'770707300528',
'000802600960',
'990316400762',
'990130300468',
'970714301135',
'991023400188',
'991006400442',
'010308601727',
'000624500174',
'990327400267',
'001222500796',
'010212601170',
'971016301683',
'970810400734',
'990816400898',
'000107500669',
'960222301590',
'011118500584',
'000708600820',
'010314601750',
'010803501137',
'000129500847',
'021018600717',
'011020500402',
'020402600226',
'990222400682',
'011001500746',
'011204500767',
'990819301709',
'901029400332'
        ];
    }
}
