<?php

namespace App\Console\Commands;

use App\Wifi;
use Illuminate\Console\Command;

class ImportWifiPackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:wifi:package';

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

        $fileName = storage_path('import/import_wifi_package_230719.csv');

        if(!file_exists($fileName))
        {
            $this->error('Import file not found');
            return;
        }

        $aNoImportPackage = [];

        $file = fopen($fileName, 'r');
        $iCount = 0;
        $iImportCount = 0;

        $this->output->progressStart( sizeof( file($fileName) ) );
        while(($aRow = fgetcsv($file, 1000, ";")) !== FALSE)
        {

            /* INFO */
            /*
             * 0 => code
             * 1 => value
             * */

            //Log::info('row: ' . var_export($aRow,true));
            //continue;


            $iCount++;


            // import
            try {

                $aVal = explode(' ',$aRow[1]);

                $oWifi         = new Wifi();
                $oWifi->code   = $aRow[0];
                $oWifi->value  = !empty($aVal[0]) ? $aVal[0] : $aRow[1] ;
                $oWifi->status = Wifi::STATUS_NEW;
                $oWifi->save();
                unset($oWifi);
                ++$iImportCount;

            } catch( \Exception $e){

                $aNoImportPackage[] = $aRow[0] . ':' . $aRow[1];
            }



            //Log::info('row: ' . var_export($aRow,true));

            $this->output->progressAdvance();

        }

        fclose($file);
        $this->output->progressFinish();

        $this->info('create package count: ' . $iImportCount );
        $this->info('error import data count: ' . count($aNoImportPackage) );
        $this->info('error import data: ' . implode('; ',$aNoImportPackage) );


    }

}
