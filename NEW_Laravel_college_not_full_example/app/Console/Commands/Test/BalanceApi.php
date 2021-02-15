<?php

namespace App\Console\Commands\Test;

use App\Services\Test\TestModule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BalanceApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:api:balance {--count=0} {--index=null} {--iin=null} {--iin_count=1}';

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
        $index = $this->option('index');
        $count = $this->option('count');
        $iin = $this->option('iin');
        $iinCount = $this->option('iin_count');

        Log::useDailyFiles(storage_path('logs/test_balance_api_' . date('H', time()) . '_' . $count . '_' . $iinCount . '.log'));

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
            exec('php artisan test:api:balance --index=' . $i . ' --count=' . $count . ' --iin=' . $iin . ' --iin_count=' . $iinCount . ' > /dev/null 2>/dev/null &');
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
        $url = 'http://' . $login . ':' . $password . '@10.0.6.217/site/hs/Balance/list/?i=' . $index;
        $testModule = new TestModule('test:1c:balance', '10.0.6.217');

        $answer = $testModule->sendRequest($url, 'post', $iinList, 'json');

        Log::info($answer->content);
        $testModule->deleteCookieFile();
    }
}
