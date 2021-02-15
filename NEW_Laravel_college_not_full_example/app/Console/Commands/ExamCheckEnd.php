<?php

namespace App\Console\Commands;

use App\Semester;
use App\SpecialitySemester;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ExamCheckEnd extends Command
{
    protected $signature = 'exam:check_end';

    protected $description = 'Command description';

    public function handle()
    {
        $today = Carbon::now()->startOfDay();
        $yesterday = Carbon::now()->subDay()->startOfDay();

        // TODO individual semesters for user

        $specialitySemesters = SpecialitySemester::getAllExam();

        foreach ($specialitySemesters as $specialitySemester) {
            // ended yesterday
            if ($specialitySemester->end_date == $yesterday) {
                User::setZeroExamBySpecialitySemester($specialitySemester);
            }
        }

        $userIdsBySpecialitySemesters = User::getIdsBySpecialitySemesters($specialitySemesters);

        $semesters = Semester::getFinishedYesterdayExam($today);
        foreach ($semesters as $semester) {
            User::setZeroExamBySemester($semester, $userIdsBySpecialitySemesters);
        }
    }
}
