<?php

use Illuminate\Database\Seeder;

class SemestersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataList = [
            [
                'number' => 1,
                'year'   => 2019,
                'start_date' => '2019-09-01',
                'end_date' => '2020-01-26',
                'type'       => 'study',
                'created_at'    => DB::raw('NOW()')
            ],
            [
                'number' => 1,
                'year'   => 2019,
                'start_date' => '2019-10-14',
                'end_date' => '2010-10-18',
                'type'       => 'quiz',
                'created_at'    => DB::raw('NOW()')
            ],
            [
                'number' => 1,
                'year'   => 2019,
                'start_date' => '2019-12-18',
                'end_date' => '2020-01-10',
                'type'       => 'session',
                'created_at'    => DB::raw('NOW()')
            ],

            [
                'number' => 2,
                'year'   => 2019,
                'start_date' => '2020-01-27',
                'end_date' => '2020-06-14',
                'type'       => 'study',
                'created_at'    => DB::raw('NOW()')
            ],
            [
                'number' => 2,
                'year'   => 2019,
                'start_date' => '2020-03-09',
                'end_date' => '2020-03-13',
                'type'       => 'quiz',
                'created_at'    => DB::raw('NOW()')
            ],
            [
                'number' => 2,
                'year'   => 2019,
                'start_date' => '2020-05-11',
                'end_date' => '2020-05-29',
                'type'       => 'session',
                'created_at'    => DB::raw('NOW()')
            ]
        ];

        \App\Semester::insert($dataList);
    }
}
