<?php

namespace App\Console\Commands;

use App\{DiscountStudent,DiscountTypeList,Profiles};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{DB,Log};

class ImportDiscountForStud1 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:discount:new1';

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

        $aData = [
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
            ],
        ];

        $aNoImportUsers = [];


        $iCount = 0;
        $iImportCount = 0;

        $this->output->progressStart( count($aData) );
        foreach( $aData as $aRow )
        {

            /* INFO */
            /*
             * 0 => iin
             * 1 => fio
             * 2 => discount name
             * 3 => percent
             * 4 => expire date
             * */

            //Log::info('row: ' . var_export($aRow,true));
            //continue;


            $iCount++;

            // fix
            if( $aRow[2] == 'Жолдама' )
            {
                $aRow[2] = 'Грант Президента Мирас';
            } elseif ( $aRow[2] == 'Семьи, имеющие и воспитывающие детей-инвалидов' ){
                $aRow[2] = 'Семьи, имеющие или воспитывающие инвалидов';
            } elseif ( $aRow[2] == 'Родственник студента' ){
                $aRow[2] = 'Для родственников студентов';
            } elseif ( $aRow[2] == 'Родственник сотрудника' ){
                $aRow[2] = 'Для сотрудников и их родственников';
            } elseif ( $aRow[2] == 'Многодетные семьи' ){
                $aRow[2] = 'Многодетные семьи';
            } elseif ( $aRow[2] == 'Кандидат в мастера спорта' ){
                $aRow[2] = 'Мастер спорта/кандидат в мастера спорта';
            } elseif ( $aRow[2] == 'Грант Президента Мирас' ){
                $aRow[2] = 'Грант Президента Мирас';
            } elseif ( $aRow[2] == 'Сотрудник университета, колледжа' ){
                $aRow[2] = 'Для сотрудников и их родственников';
            }


            $oDiscountTypeList = DiscountTypeList::
            where('name_ru', 'like', '%' . strtolower($aRow[2]) . '%')->
            first();
            if( empty($oDiscountTypeList) )
            {
                // if not find type in table
                $aNoImportUsers[] = $aRow[0] . ':' . $aRow[2];
                continue;
            }

            $oProfiles = Profiles::
            select([
                'id',
                'user_id',
                DB::RAW('LOWER(fio) as fio')
            ])->
            where('iin',$aRow[0])->
            orWhere('fio', 'like', '%' . strtolower($aRow[1]) . '%')->
            first();
            if( empty($oProfiles) )
            {
                // if not find type in table
                $aNoImportUsers[] = $aRow[0] . ':' . $aRow[2];
                continue;
            }


            // import
            $oDiscountStudent = new DiscountStudent();
            $oDiscountStudent->type_id = $oDiscountTypeList->id;
            $oDiscountStudent->user_id = $oProfiles->user_id;
            $oDiscountStudent->status  = DiscountStudent::STATUS_NEW;
            $oDiscountStudent->valid_till = date('Y-m-d H:i:s',strtotime($aRow[4]));
            $oDiscountStudent->save();
            unset($oDiscountStudent);

            ++$iImportCount;

            //Log::info('row: ' . var_export($aRow,true));


            $this->output->progressAdvance();

        }

        $this->output->progressFinish();

        $this->info('create discount count: ' . $iImportCount );
        $this->info('error import data count: ' . count($aNoImportUsers) );
        $this->info('error import data: ' . implode(';',$aNoImportUsers) );
        Log::info('import discount 22-09-2019: ' . var_export( implode(';',$aNoImportUsers) ,true));

    }
}
