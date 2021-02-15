<?php

namespace App\Console\Commands;

use App\Wifi;
use Illuminate\Console\Command;

class InitBuyWifiPackageForTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buy:wifipackage:fortest {--user=}';

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

        $iUser = $this->option('user');

        $aData = [ '76481-09436', '44909-44675', '44262-22105'];

        foreach( $aData as $key => $one )
        {

            $oWifi = Wifi::
            where('code',$one)->
            first();

            try {

                $oWifi->user_id = $iUser;
                $oWifi->status  = ($key < 2) ? Wifi::STATUS_INACTIVE : Wifi::STATUS_ACTIVE;
                $oWifi->save();

            } catch( \Exception $e){}

            unset($oWifi);

        }


    }
}
