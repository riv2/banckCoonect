<?php

namespace App\Console\Commands\Fix;

use App\Profiles;
use App\Services\Service1C;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EqualizePay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pays:equalize';

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
        $file = fopen(storage_path('import/equalize_pays.csv'), 'r');
        $this->output->progressStart();
        $updatedCount = 0;

        while($row = fgetcsv($file, 0, ',', "'"))
        {
            $studentId = $row[0];
            $amount = $row[1];

            if($amount) {
                $profile = Profiles::where('user_id', $studentId)->first();

                if ($profile) {
                    $log = ['id = ' . $profile->user_id];
                    $log[] = 'amount = ' . $amount;

                    $payResult = 'none';

                    if($amount > 0 && !empty($profile->iin)) {
                        $nomenclatureCode = '00000006539';
                        //$payResult = Service1C::pay($profile->iin, $nomenclatureCode, $amount);
                        $updatedCount++;
                    }
                    $log[] = '1c_result = ' . $payResult;

                    Log::info('Pay: ' . implode(' : ', $log));
                    $this->info('Pay: ' . implode(' : ', $log));
                } else {
                    $this->warn('Profile not found ' . $studentId);
                }
            }

            $this->output->progressAdvance();
        }

        fclose($file);
        $this->output->progressFinish();

        $this->info('Updated count: ' . $updatedCount);
    }
}
