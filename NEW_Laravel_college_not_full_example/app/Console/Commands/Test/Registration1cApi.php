<?php

namespace App\Console\Commands\Test;

use App\Services\Test\TestModule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Registration1cApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:1c:registration {--count=0} {--index=null}';

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
        Log::useDailyFiles(storage_path('logs/test_1c_registration_' . date('Y_m_d', time()) . '.log'));

        $index = $this->option('index');
        $count = $this->option('count');

        if($index == 'null')
        {
            $this->runList($count);
        }
        else
        {
            $this->runSingle($index);
        }
    }

    /**
     * @param $count
     */
    public function runList($count)
    {
        for ($i = 1; $i <= $count; $i++)
        {
            exec('php artisan test:1c:registration --index=' . $i . ' > /dev/null 2>/dev/null &');
        }
    }

    /**
     * @param $index
     */
    public function runSingle($index)
    {
        sleep(rand(1, 5));
        $login = 'kit';
        $password = '1q2w3e4r';
        $url = 'http://' . $login . ':' . $password . '@10.0.6.217/site/hs/Client/Registration/?i=' . $index;
        $testModule = new TestModule('test:1c:registration', '10.0.6.217');

        $answer = $testModule->sendRequest($url, 'post', [
            'iin' => (string)rand(100000000000, 999999999999),
            'full_name' => 'Тест Тест Тест',
            'gender'    => 'male',
            'bdate'     => date('Y-m-d', time())
        ], 'json');

        Log::info($answer->content);
        $testModule->deleteCookieFile();
    }
}
