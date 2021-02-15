<?php

use App\Semester;
use Illuminate\Database\Seeder;

class SemestersAddTaskType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        \DB::table('semesters')->insert([
            0 => [
                'study_form'   => '',
                'new_system'   => 0,
                'number'       => 1,
                'year'         => date('Y'),
                'type'         => Semester::TYPE_TASK,
                'start_date'   => date('2019-09-01'),
                'end_date'     => date('2019-12-31'),
                'created_at'   => date('Y-m-d H:i:s')
            ],
            1 => [
                'study_form'   => '',
                'new_system'   => 0,
                'number'       => 2,
                'year'         => date('Y'),
                'type'         => Semester::TYPE_TASK,
                'start_date'   => date('2020-01-01'),
                'end_date'     => date('2020-07-01'),
                'created_at'   => date('Y-m-d H:i:s')
            ]
        ]);

    }
}
