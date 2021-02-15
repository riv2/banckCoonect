<?php

use Illuminate\Database\Seeder;

class StudentLectureType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $studentLectureList = \App\StudentLecture::get();

        foreach ($studentLectureList as $studentLecture)
        {
            $lecture = \App\Lecture::where('id', $studentLecture->lecture_id)->first();
            if($lecture->type == 'offline' || $lecture->type == 'all')
            {
                $studentLecture->type = 'offline';
            }

            if($lecture->type == 'online')
            {
                $studentLecture->type = 'online';
            }

            $studentLecture->save();
        }
    }
}
