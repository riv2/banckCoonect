<?php

use App\Profiles;
use Illuminate\Database\Seeder;

class Semesters2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $forms = [
            Profiles::EDUCATION_STUDY_FORM_FULLTIME,
            Profiles::EDUCATION_STUDY_FORM_ONLINE,
            Profiles::EDUCATION_STUDY_FORM_EVENING,
            Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL
        ];

        $systems = [true, false];

        foreach ($systems as $system) {
            foreach ($forms as $form) {
                $dataList = [
                    [
                        'study_form' => $form,
                        'new_system' => $system,
                        'number' => 1,
                        'type' => 'study',
                        'start_date' => '2019-09-01',
                        'end_date' => '2020-01-26',
                        'created_at' => DB::raw('NOW()')
                    ],
                    [
                        'study_form' => $form,
                        'new_system' => $system,
                        'number' => 1,
                        'type' => 'quiz',
                        'start_date' => '2019-10-28',
                        'end_date' => '2010-11-15',
                        'created_at' => DB::raw('NOW()')
                    ],
                    [
                        'study_form' => $form,
                        'new_system' => $system,
                        'number' => 1,
                        'type' => 'session',
                        'start_date' => '2019-12-18',
                        'end_date' => '2020-01-10',
                        'created_at' => DB::raw('NOW()')
                    ],
                    [
                        'study_form' => $form,
                        'new_system' => $system,
                        'number' => 2,
                        'type' => 'study',
                        'start_date' => '2020-01-27',
                        'end_date' => '2020-06-14',
                        'created_at' => DB::raw('NOW()')
                    ],
                    [
                        'study_form' => $form,
                        'new_system' => $system,
                        'number' => 2,
                        'type' => 'quiz',
                        'start_date' => '2020-03-09',
                        'end_date' => '2020-03-13',
                        'created_at' => DB::raw('NOW()')
                    ],
                    [
                        'study_form' => $form,
                        'new_system' => $system,
                        'number' => 2,
                        'type' => 'session',
                        'start_date' => '2020-05-11',
                        'end_date' => '2020-05-29',
                        'created_at' => DB::raw('NOW()')
                    ]
                ];

                \App\Semester::insert($dataList);
            }
        }


    }
}
