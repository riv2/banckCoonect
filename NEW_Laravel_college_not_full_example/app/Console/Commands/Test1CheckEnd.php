<?php

namespace App\Console\Commands;

use App\Semester;
use App\SpecialitySemester;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;

class Test1CheckEnd extends Command
{
    protected $signature = 'test1:check_end';

    protected $description = 'Command description';

    public function handle()
    {
        $today = Carbon::now()->startOfDay();
        $yesterday = Carbon::now()->subDay()->startOfDay();

        // TODO individual semesters for user

        $specialitySemesters = SpecialitySemester::getAllTest1();

        foreach ($specialitySemesters as $specialitySemester) {
            // ended yesterday
            if ($specialitySemester->end_date == $yesterday) {
                User::setZeroTest1BySpecialitySemester($specialitySemester);
            }
        }

        $userIdsBySpecialitySemesters = User::getIdsBySpecialitySemesters($specialitySemesters);

        $semesters = Semester::getFinishedYesterdayTest1($today);
        foreach ($semesters as $semester) {
            User::setZeroTest1BySemester($semester, $userIdsBySpecialitySemesters);
        }
    }
}
