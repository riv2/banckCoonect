<?php

namespace App\Console\Commands;

use App\Speciality;
use App\SpecialityDiscipline;
use Illuminate\Console\Command;

class ExportEsuvoSpeciality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'esuvo:export:speciality {--id=}';

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
        $specialityId = $this->option('id');
        $speciality = Speciality
            ::with('disciplines')
            ->with('trend')
            ->where('id', $specialityId)
            ->first();

        if(!$speciality)
        {
            $this->error('Speciality not found');
            return;
        }

        /*export professions*/
        $profession = [
            $speciality->id,
            $speciality->trend->training_code ?? '',
            date('d.m.Y', strtotime($speciality->created_at)),
            'false',
            $speciality->description_en ? $speciality->description_en : $speciality->goals_en,
            $speciality->description_kz ? $speciality->description_kz : $speciality->goals_kz,
            $speciality->description ? $speciality->description : $speciality->goals,
            'false',
            '',
            '',
            $speciality->name_en,
            $speciality->name_kz,
            $speciality->name,
            $speciality->qualification->name_en,
            $speciality->qualification->name_kz,
            $speciality->qualification->name_ru,
            '2',
            ''
        ];

        $file = fopen(storage_path('export/esuvo/professions.csv'), 'w');
        fputcsv($file, $profession);
        fclose($file);

        /*export subjects and tupsubjects*/
        $file1 = fopen(storage_path('export/esuvo/subjects.csv'), 'w');
        $file2 = fopen(storage_path('export/esuvo/tupsubjects.csv'), 'w');

        foreach ($speciality->disciplines as $discipline)
        {
            fputcsv($file1, [
                $discipline->id,
                $discipline->description_kz,
                $discipline->description_en,
                $discipline->description,
                '0',
                '',
                '',
                '',

// polylang удалён
//                $discipline->polylang,
                '',

                '',
                '',
                $discipline->num_kz,
                $discipline->num_en,
                $discipline->num_ru,
                $discipline->name_en,
                $discipline->name_kz,
                $discipline->name,
            ]);

            $cycle = 0;

            if($discipline->pivot->discipline_cicle == 'ООД')
            {
                $cycle = 1;
            }

            if($discipline->pivot->discipline_cicle == 'БД')
            {
                $cycle = 2;
            }

            if($discipline->pivot->discipline_cicle == 'ПД')
            {
                $cycle = 3;
            }

            fputcsv($file2, [
                $discipline->id,
                $cycle,
                '',
                $discipline->credits,
                $speciality->id,
                $discipline->ects,
                __($discipline->control_form),
                'true',
                $discipline->pivot->mt_tk == 'ОК' ? 'true' : '',
                '',
                $discipline->num_ru,
                $discipline->num_en,
                $discipline->num_kz,
                $discipline->id,
                '',
                '',
                $discipline->pivot->mt_tk == 'ВК' ? 'true' : ''
            ]);
        }

        fclose($file1);
        fclose($file2);
    }
}
