<?php

namespace App\Console\Commands;

use App\Discipline;
use Illuminate\Console\Command;

class ExportStudyPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:study_plan';

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
        app()->setLocale('ru');
        $disciplineList = Discipline::get();
        $file = fopen(storage_path('export/study_plan.csv'), 'w');


        fputcsv($file, [
            'ID',
            'Внешний ID',
            'Название дисциплины (ru)',
            'Название дисциплины (kz)',
            'Название дисциплины (en)',
            'Кредиты KZT',
            'Кредиты ECTS',
            'Языки',
            'Номер модуля',
            'Шифр дисциплины (ru)',
            'Шифр дисциплины (kz)',
            'Шифр дисциплины (en)',
            'Пререквизит',
            'Пререквизит 2',
            'Пререквизит 3',
            'Пререквизит 4',
            'Пререквизит 5',
            'Описание (ru)',
            'Описание (kz)',
            'Описание (en)',
            'Полиязычность',
            'Цикл',
            'Тип',
            'Количество лекционных часов',
            'Количество практических(семинар) часов',
            'Количество лабораторных часов',
            'Количество СРО часов',
            'Форма контроля'
        ]);

        if($disciplineList)
        {
            foreach ($disciplineList as $discipline)
            {
                $langs = [];

                if($discipline->ru) $langs[] = 'ru';
                if($discipline->kz) $langs[] = 'kz';
                if($discipline->en) $langs[] = 'en';

                $row = [
                    $discipline->id,
                    $discipline->ex_id,
                    $discipline->name,
                    $discipline->name_kz,
                    $discipline->name_en,
                    $discipline->credits,
                    $discipline->ects,
                    implode(', ', $langs),
                    $discipline->module_number,
                    $discipline->num_ru,
                    $discipline->num_kz,
                    $discipline->num_en,
                    $discipline->dependence,
                    $discipline->dependence2,
                    $discipline->dependence3,
                    $discipline->dependence4,
                    $discipline->dependence5,
                    $discipline->description,
                    $discipline->description_kz,
                    $discipline->description_en,

                    // polylang удалён
//                    $discipline->polylang ? 'да' : 'нет',
                '',


                    $discipline->pivot->discipline_cicle,
                    $discipline->pivot->mt_tk,
                    $discipline->lecture_hours,
                    $discipline->practical_hours,
                    $discipline->laboratory_hours,
                    $discipline->sro_hours,
                    __($discipline->control_form)
                ];
                fputcsv($file, $row);
                $exportContent[] = $row;
            }
        }

        fclose($file);
    }
}
