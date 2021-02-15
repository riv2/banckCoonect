<?php

namespace App\Console\Commands;

use App\{DiscountStudent,DiscountTypeList,Profiles};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{DB,Log};

class ImportDiscountForStud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:discount';

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
        $iImportCount = 0;
        $aDuplicateData = [];
        $aDuplicateEqualTypeData = [];

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

            //Log::info('row: ' . var_export($aRow,true));
            //continue;


            $iCount++;
            // continue 1 line - titles
            if( $iCount == 1 ) { continue; }

            // fix
            if( $aRow[4] == 'Жолдама' )
            {
                $aRow[4] = 'Грант Президента Мирас';
            } elseif ( $aRow[4] == 'Семьи, имеющие и воспитывающие детей-инвалидов' ){
                $aRow[4] = 'Семьи, имеющие или воспитывающие инвалидов';
            } elseif ( $aRow[4] == 'Родственник студента' ){
                $aRow[4] = 'Для родственников студентов';
            } elseif ( $aRow[4] == 'Родственник сотрудника' ){
                $aRow[4] = 'Для сотрудников и их родственников';
            } elseif ( $aRow[4] == 'Многодетные семьи' ){
                $aRow[4] = 'Многодетные семьи';
            } elseif ( $aRow[4] == 'Кандидат в мастера спорта' ){
                $aRow[4] = 'Мастер спорта/кандидат в мастера спорта';
            } elseif ( $aRow[4] == 'Грант Президента Мирас' ){
                $aRow[4] = 'Грант Президента Мирас';
            } elseif ( $aRow[4] == 'Дети-сироты и дети, оставшиеся без попечения родителей, не достигшие двадцати трех лет, потерявшие родителей до совершеннолетия' ){
                $aRow[4] = 'Дети-сироты';
            } elseif ( $aRow[4] == 'Трудостройство' ){
                $aRow[4] = 'За трудоустройство';
            } elseif ( $aRow[4] == 'Для выпускников колледжа и университета Мирас' ){
                $aRow[4] = 'Для выпускников колледжа/университета Мирас';
            } elseif ( $aRow[4] == 'Заслуженный мастер спорта / Мастер спорта международного класса' ){
                $aRow[4] = 'Заслуженный мастер спорта / Мастер спорта международного класса';
            } elseif ( $aRow[4] == 'Мастер спорта / Кандидат в мастера спорта' ){
                $aRow[4] = 'Мастер спорта/кандидат в мастера спорта';
            } elseif ( $aRow[4] == 'Для  родственников студентов' ){
                $aRow[4] = 'Для родственников студентов';
            } elseif ( $aRow[4] == 'Обьемная скидка' ){
                $aRow[4] = 'Объемная скидка';
            } elseif ( $aRow[4] == 'Для сотрудников и их родственников Университета и колледжа Мирас' ){
                $aRow[4] = 'Для сотрудников и их родственников';
            } elseif ( $aRow[4] == 'Лучший GPA' ){
                $aRow[4] = 'Балл GPA';
            } elseif ( $aRow[4] == 'КДМ' ){
                $aRow[4] = 'Комитет по делам молодежи';
            }

            $oDiscountTypeList = DiscountTypeList::
            where('name_ru', 'like', '%' . strtolower($aRow[4]) . '%')->
            first();
            if( empty($oDiscountTypeList) )
            {
                // if not find type in table
                $aNoImportUsers[] = $aRow[0] . ':' . $aRow[4] . ':1';
                continue;
            }

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
            where('user_id',$oProfiles->user_id)->
            first();
            if( empty($oDiscountStudent) ){

                try {

                    $oDiscountStudentNew = new DiscountStudent();
                    $oDiscountStudentNew->type_id = $oDiscountTypeList->id;
                    $oDiscountStudentNew->user_id = $oProfiles->user_id;
                    $oDiscountStudentNew->status  = DiscountStudent::STATUS_APPROVED;
                    $oDiscountStudentNew->valid_till = date('Y-m-d H:i:s',strtotime($aRow[7]));
                    $oDiscountStudentNew->save();
                } catch (\Exception $e){

                    $aNoImportUsers[] = $aRow[0] . ':' . $aRow[4] . ':3';
                    continue;
                }

                unset($oDiscountStudentNew);
                ++$iImportCount;

            } else {

                // test duplicate
                $oCurDiscountTypeList = DiscountTypeList::
                where('id',$oDiscountStudent->type_id)->
                first();
                if( !empty($oCurDiscountTypeList) && ( $oCurDiscountTypeList->name_ru != $aRow[4] ) )
                {
                    // duplicate discount other type
                    $aDuplicateData[] = $aRow[0] . ':' . $aRow[4];
                } else {

                    // duplicate discount equal type
                    $aDuplicateEqualTypeData[] = $aRow[0] . ':' . $aRow[4];
                }

                continue;
            }

            //Log::info('row: ' . var_export($aRow,true));

            $this->output->progressAdvance();

        }

        fclose($file);
        $this->output->progressFinish();

        $this->info('read count: ' . ($iCount - 1) );
        $this->info('create discount count: ' . $iImportCount );
        $this->info('duplicate other data count: ' . count($aDuplicateData) );
        $this->info('duplicate other data: ' . implode(';',$aDuplicateData) );
        $this->info('duplicate equal data count: ' . count($aDuplicateEqualTypeData) );
        $this->info('duplicate equal data: ' . implode(';',$aDuplicateEqualTypeData) );
        $this->info('error import data count: ' . count($aNoImportUsers) );
        $this->info('error import data: ' . implode(';',$aNoImportUsers) );
        //Log::info('import discount 01-10-2019: ' . var_export( implode(';',$aNoImportUsers) ,true));

    }
}
