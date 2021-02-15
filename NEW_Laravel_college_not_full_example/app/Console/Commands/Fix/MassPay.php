<?php

namespace App\Console\Commands\Fix;

use App\Profiles;
use App\Services\Service1C;
use Illuminate\Console\Command;

class MassPay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pay:mass';

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
        $file = fopen(storage_path('import/mass_pay.csv'), 'r');

        $iinGroups = [];

        while($row = fgetcsv($file, 0, ',', "'"))
        {
            $userId = $row[0];
            $cost = $realCost = $row[1] < 0 ? (-1 * $row[1]) : $row[1];
            //$nomenclature = str_replace("'", '', $row[2]);
            $profile = Profiles::where('user_id', $userId)->first();

            if(!empty($profile->iin))
            {
                $iinGroups[ $cost ][] = $profile->iin;
            }
            else
            {
                $this->warn('User iin not found ' . $userId);
            }
        }

        foreach ($iinGroups as $cost => $group)
        {


            $payResult = Service1C::sendRequest(Service1C::API_PAY, [
                'id_miras_app' => '',
                'iin_list' => $group,
                'code' => "БК000000829",
                'cost' => "$cost"
                //'bik_partner' => '170140004920',
                //'name_partner' => '',
                //'contract' => 'ЗУ0017596'
            ]);

            $message = 'Payed: ' . count($group) . ' iins, cost = ' . $cost . ', Result = ' . ($payResult ? count($payResult) : '');

            if($payResult)
            {
                $this->info($message);
            }
            else
            {
                $this->error($message);
            }
        }
    }
}
