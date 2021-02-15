<?php

namespace App\Console\Commands;

use App\Profiles;
use App\User;
use Illuminate\Console\Command;
use App\Services\Test\TestModule;

class RefreshUserBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:balance:refresh {--iin=null}';

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
        $iin = $this->option('iin');
        $iinList = [];

        if($iin == 'null')
        {
            $profileList = Profiles::get();

            foreach ($profileList as $profile)
            {
                $iinList[] = $profile->iin;
            }
        }
        else
        {
            $iinList[] = $iin;
        }

        $login = 'kit';
        $password = '1q2w3e4r';
        $url = 'http://' . $login . ':' . $password . '@10.0.6.217/site/hs/Balance/list/';
        $testModule = new TestModule('get:1c:balance', '10.0.6.217');

        $answer = $testModule->sendRequest($url, 'post', $iinList, 'json');
        $content = json_decode($answer->content);
        $testModule->deleteCookieFile();

        $updatedCount = 0;

        foreach ($content as $record)
        {
            if(isset($record->balance) && isset($record->iin))
            {
                $iin = $record->iin;

                $user = User::whereHas('studentProfile', function ($query) use ($iin) {
                    $query->where('iin', $iin);
                })->first();

                if($user)
                {
                    $user->balance = $record->balance;
                    $user->save();

                    $updatedCount++;
                }
            }
        }

        $this->info('Updated ' . $updatedCount . ' users');
    }
}
