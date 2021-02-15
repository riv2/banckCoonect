<?php

namespace App\Console\Commands\Test;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\Test\TestModule;

class Pay1c extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:1c:pay {--count=0} {--index=null} {--iin=null} {--iin_count=1}';

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
        Log::useDailyFiles(storage_path('logs/test_1c_pay_api_' . date('Y_m_d', time()) . '.log'));

        $index = $this->option('index');
        $count = $this->option('count');
        $iin = $this->option('iin');
        $iinCount = $this->option('iin_count');

        if($index == 'null')
        {
            $this->runList($count, $iin, $iinCount);
        }
        else
        {
            $this->runSingle($index, $iin, $iinCount);
        }
    }

    /**
     * @param $count
     */
    public function runList($count, $iin, $iinCount)
    {
        for ($i = 1; $i <= $count; $i++)
        {
            exec('php artisan test:1c:pay --index=' . $i . ' --iin=' . $iin . ' --iin_count=' . $iinCount . ' > /dev/null 2>/dev/null &');
        }
    }

    /**
     * @param $index
     */
    public function runSingle($index, $iin, $iinCount)
    {
        $iinList = [];

        for($i = 0; $i < $iinCount; $i++)
        {
            $iinList[] = $iin;
        }

        $login = 'kit';
        $password = '1q2w3e4r';
        $url = 'http://' . $login . ':' . $password . '@10.0.6.217/site/hs/Client/pay?i=' . $index;
        $testModule = new TestModule('test:1c:pay', '10.0.6.217');

        $answer = $testModule->sendRequest($url, 'post', [
            'code' => '00000006539',
            'cost' => '1000',
            'iin_list' => $iinList
        ], 'json');

        Log::info($answer->content);
        $testModule->deleteCookieFile();
    }
}
