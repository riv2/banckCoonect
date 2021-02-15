<?php

namespace App\Console\Commands\Test;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\Test\TestModule;

class Report1c extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:1c:report {--count=0} {--index=null} {--iin=null}';

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
        Log::useDailyFiles(storage_path('logs/test_1c_report_api_' . date('Y_m_d', time()) . '.log'));

        $index = $this->option('index');
        $count = $this->option('count');
        $iin = $this->option('iin');

        if($index == 'null')
        {
            $this->runList($count, $iin);
        }
        else
        {
            $this->runSingle($index, $iin);
        }
    }

    /**
     * @param $count
     * @param $iin
     * @param $iinCount
     */
    public function runList($count, $iin)
    {
        for ($i = 1; $i <= $count; $i++)
        {
            exec('php artisan test:1c:report --index=' . $i . ' --iin=' . $iin . ' > /dev/null 2>/dev/null &');
        }
    }

    /**
     * @param $index
     * @param $iin
     * @param $iinCount
     */
    public function runSingle($index, $iin)
    {
        $login = 'kit';
        $password = '1q2w3e4r';
        $url = 'http://' . $login . ':' . $password . '@10.0.6.217/site/hs/orders/list?i=' . $index;
        $testModule = new TestModule('test:1c:report', '10.0.6.217');

        $answer = $testModule->sendRequest($url, 'post', [
            'iin' => $iin,
            'date_from' => '01.08.2019',
            'date_to' => date('d.m.Y', time()),
        ], 'json');

        Log::info($answer->content);
        $testModule->deleteCookieFile();
    }
}
