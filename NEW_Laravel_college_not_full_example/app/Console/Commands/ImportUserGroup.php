<?php

namespace App\Console\Commands;

use App\{Profiles};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{DB,Log};

class ImportUserGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:user:group';

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

        //$fileName = storage_path('import/import_user_group_12092019.csv');
        $fileName = storage_path('import/import_user_group_06092019.csv');

        if(!file_exists($fileName))
        {
            $this->error('Import file not found');
            return;
        }

        $file = fopen($fileName, 'r');
        $aCount = 0;

        $this->output->progressStart( sizeof( file($fileName)));
        $failCount = 0;

        while(($row = fgetcsv($file, 1000, ";")) !== FALSE)
        {

            // INFO //
            /*
            0  => id
            1  => ФИО
            2  => Специальность
            3  => Специальность после перевода
            4  => Email
            5  => Телефон
            6  => Форма обучения
            7  => ИИН
            8  => Баз. обр.
            9  => Степень
            10 => Категория
            11 => Группа
            12 => Телефон
            13 => Язык обучения
            14 => Пол
            15 => Адрес
            16 => Удостоверение личности
            17 => Наименование Учебного Заведения
            18 => Серия ДО
            19 => Номер ДО
            20 => Дата выдачи ДО
            21 => Данные ЕНТ/ИКТ
            22 => Национальность
            23 => Гражданство
            24 => Иностранец
            25 => Дата приема
            26 => Агитатор
           */

            $aCount++;

            // continue 1 line - titles
            if( $aCount == 1 ) { continue; }

            $oProfiles = Profiles::
            select([
                'id',
                'user_id',
                DB::RAW('LOWER(fio) as fio')
            ])->
            where('user_id',$row[0])->
            orWhere('fio', 'like', '%' . strtolower($row[1]) . '%')->
            first();
            if( !empty($oProfiles) )
            {
                $oProfiles->team = $row[11];
                $oProfiles->save();
            }
            else
            {
                $failCount++;
            }
            unset($oProfiles);
            $this->output->progressAdvance();
        }

        fclose($file);
        $this->output->progressFinish();

        $this->info('Updated profile group: ' . ($aCount - 1) );
        $this->info('Failed: ' . $failCount );

    }
}
