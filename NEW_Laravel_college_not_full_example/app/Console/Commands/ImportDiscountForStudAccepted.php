<?php

namespace App\Console\Commands;

use App\{DiscountStudent,DiscountTypeList,Profiles};
use Illuminate\Console\Command;

class ImportDiscountForStudAccepted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cimport:discount:accept';

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

        $fileName = storage_path('import/import_discount_17_09_19.csv');

        if(!file_exists($fileName))
        {
            $this->error('Import file not found');
            return;
        }

        $aUsers = [
            [
                0 => '000627600761',
                1 => 'Усмонова Шохиста Боходир қизи',
                2 => 'Победители международных предметных олимпиад',
                3 => '100%',
                4 => '30.06.2021'
            ],
            [
                0 => '000609601503',
                1 => 'Абдуллаева Динора Шайловбековна',
                2 => 'Победители международных предметных олимпиад',
                3 => '100%',
                4 => '30.06.2022'
            ],
            [
                0 => '010220600057',
                1 => 'Сайтмуратова Диёра Суратовна',
                2 => 'Победители международных предметных олимпиад',
                3 => '100%',
                4 => '30.06.2021'
            ],
            [
                0 => '000101601690',
                1 => 'Корикова Салина Мусаевна',
                2 => 'Победители международных предметных олимпиад',
                3 => '100%',
                4 => '01.07.2021'
            ],
            [
                0 => '990727401100',
                1 => 'Өсербек Айгерім Ағабекқызы',
                2 => 'Победители международных предметных олимпиад',
                3 => '100%',
                4 => '01.07.2021'
            ],
            [
                0 => '900413402185',
                1 => 'Алимбетова Гульжан Кененовна',
                2 => 'Сотрудник университета, колледжа',
                3 => '10%',
                4 => '01.07.2020'
            ],
            [
                0 => '890924400668',
                1 => 'Саваровская Любовь Викторовна',
                2 => 'Сотрудник университета, колледжа',
                3 => '10%',
                4 => '01.07.2020'
            ],
            [
                0 => '900117401900',
                1 => 'Утеева Гулзина Тойшиевна',
                2 => 'Сотрудник университета, колледжа',
                3 => '10%',
                4 => '01.07.2020'
            ]
        ];

        $iCount = 0;
        $iImportCount = 0;

        $file = fopen($fileName, 'r');
        while(($aRow = fgetcsv($file, 1000, ";")) !== FALSE)
        {

            /* INFO */
            /*
             * 0 => iin
             * 1 => fio
             * 2 => discount name
             * 3 => percent
             * 4 => expire date
             * */

            $aUsers[] = $aRow;
        }
        fclose($file);

        $this->output->progressStart( count($aUsers) );
        foreach( $aUsers as $key => $aUserItem )
        {

            $oProfiles = Profiles::
            select([
                'id',
                'user_id'
            ])->
            where('iin',$aUserItem[0])->
            orWhere('fio', 'like', '%' . strtolower($aUserItem[1]) . '%')->
            first();
            if( empty($oProfiles) )
            {
                continue;
            }

            $oDiscountStudent = DiscountStudent::
            where('user_id',$oProfiles->user_id)->
            first();

            if( !empty($oDiscountStudent) && !empty($oDiscountStudent->valid_till) )
            {
                $oDiscountStudent->status = DiscountStudent::STATUS_APPROVED;
                $oDiscountStudent->save();
            }

            unset($oDiscountStudent,$oProfiles);


            $this->output->progressAdvance();

        }
        unset($aUsers);


        $this->output->progressFinish();


    }

}
