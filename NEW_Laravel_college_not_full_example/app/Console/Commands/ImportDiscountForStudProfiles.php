<?php

namespace App\Console\Commands;

use App\{DiscountStudent,Profiles};
use Illuminate\Support\Facades\{DB};
use Illuminate\Console\Command;

class ImportDiscountForStudProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:discount:profiles';

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

        $fileName = storage_path('import/import_discount_101019.csv');

        if(!file_exists($fileName))
        {
            $this->error('Import file not found');
            return;
        }

        $aNoImportUsers = [];

        $file = fopen($fileName, 'r');
        $iCount = 0;

        $this->output->progressStart( sizeof( file($fileName) ) );
        while(($aRow = fgetcsv($file, 1000, ";")) !== FALSE)
        {

            /* INFO */
            /*
             * 0 => id
             * 1 => fio
             * 2 => iin
             * 3 => discount category
             * 4 => discount name
             * 5 => percent
             * 6 => start date
             * 7 => expire date
             * */

            $iCount++;
            // continue 1 line - titles
            if( $iCount == 1 ) { continue; }

            $oProfiles = Profiles::
            select([
                'id',
                'user_id',
                DB::RAW('LOWER(fio) as fio')
            ])->
            where('user_id',$aRow[0])->
            where('iin',$aRow[2])->
            orWhere('fio', 'like', '%' . strtolower($aRow[1]) . '%')->
            first();
            if( empty($oProfiles) )
            {
                // if not find user in table
                $aNoImportUsers[] = $aRow[0] . ':' . $aRow[1] . ':' . $aRow[4] . ':2';
                continue;
            }

            // import
            $oDiscountStudent = DiscountStudent::
            select([
                'discount_student.id',
                'type_id',
                'user_id',
                'status',
                'valid_till',
                'discount_type_list.discount'
            ])->
            where('user_id',$oProfiles->user_id)->
            leftJoin('discount_type_list', 'discount_type_list.id', '=', 'discount_student.type_id')->
            first();
            if( !empty($oDiscountStudent) ){


                if( empty($oProfiles->discount) )
                {
                    $oProfiles->discount = $oDiscountStudent->discount;
                    $oProfiles->save();
                }


            }

            //Log::info('row: ' . var_export($aRow,true));

            $this->output->progressAdvance();

        }

        fclose($file);
        $this->output->progressFinish();

        $this->info('read count: ' . ($iCount - 1) );
        $this->info('error import data count: ' . count($aNoImportUsers) );
        $this->info('error import data: ' . implode(';',$aNoImportUsers) );
        //Log::info('import discount 01-10-2019: ' . var_export( implode(';',$aNoImportUsers) ,true));

    }
}
