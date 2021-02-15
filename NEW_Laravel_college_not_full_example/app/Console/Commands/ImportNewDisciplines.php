<?php

namespace App\Console\Commands;

use App\Discipline;
use Illuminate\Console\Command;

class ImportNewDisciplines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new_disciplines:import';

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
        $file = fopen(storage_path('import/new_discipline_list.csv'), 'r');

        while($row = fgetcsv($file))
        {
            $discipline = new Discipline();

            $inputLangs = $row[7] ?? '';
            $inputLangs = explode(',', str_replace(' ', '', $inputLangs));

            $langs = [
                'ru' => array_search('ru', $inputLangs) !== false,
                'kz' => array_search('kz', $inputLangs) !== false,
                'en' => array_search('en', $inputLangs) !== false
            ];

//            $polylang = null;
//            if(strtolower($row[20]) == 'да')
//            {
//                $polylang = true;
//            }
//
//            if(strtolower($row[20]) == 'нет')
//            {
//                $polylang = false;
//            }

            $formControl = null;

            if(strtolower($row[27]) == 'отчет')
            {
                $formControl = 'report';
            }

            if(strtolower($row[27]) == 'тест')
            {
                $formControl = 'test';
            }

            if(strtolower($row[27]) == 'диф. зач.')
            {
                $formControl = 'score';
            }

            if(strtolower($row[27]) == 'традиционный')
            {
                $formControl = 'traditional';
            }

            $discipline->fill([
                'ex_id'             => $this->getValidParam($row[1]),
                'name'              => $this->getValidParam($row[2]),
                'name_kz'           => $this->getValidParam($row[3]),
                'name_en'           => $this->getValidParam($row[4]),
                'credits'           => $this->getValidParam($row[5]) == null ? 0 : $row[5],
                'ects'              => $this->getValidParam($row[6]),
                'kz'                => $langs['kz'],
                'ru'                => $langs['ru'],
                'en'                => $langs['en'],
                'module_number'     => $this->getValidParam($row[8]),
                'num_ru'            => $this->getValidParam($row[9]),
                'num_kz'            => $this->getValidParam($row[10]),
                'num_en'            => $this->getValidParam($row[11]),
                'dependence'        => $this->getValidParam($row[12]),
                'dependence2'       => $this->getValidParam($row[13]),
                'dependence3'       => $this->getValidParam($row[14]),
                'dependence4'       => $this->getValidParam($row[15]),
                'dependence5'       => $this->getValidParam($row[16]),
                'description'       => $this->getValidParam($row[17]),
                'description_kz'    => $this->getValidParam($row[18]),
                'description_en'    => $this->getValidParam($row[19]),

// polylang удалён
//                'polylang'          => $polylang,


                'discipline_cicle'  => $this->getValidParam($row[21]),
                'mt_tk'             => $this->getValidParam($row[22]),
                'lecture_hours'     => $this->getValidParam($row[23]),
                'practical_hours'   => $this->getValidParam($row[24]),
                'laboratory_hours'   => $this->getValidParam($row[25]),
                'sro_hours'         => $this->getValidParam($row[26]),
                'control_form'      => $formControl
            ]);

            $discipline->save();
        }
    }

    private function getValidParam($val)
    {
        if($val == '')
        {
            return null;
        }

        return $val;
    }
}
