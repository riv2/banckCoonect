<?php

namespace App\Console\Commands;

use App\Semester;
use App\SpecialitySemester;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SROCheckEnd extends Command
{
    protected $signature = 'sro:check_end';

    protected $description = 'Command description';

    public function handle()
    {
        $today = Carbon::now()->startOfDay();
        $yesterday = Carbon::now()->subDay()->startOfDay();

        // TODO individual semesters for user

        $specialitySemesters = SpecialitySemester::getAllSRO();

        foreach ($specialitySemesters as $specialitySemester) {
            // ended yesterday
            if ($specialitySemester->end_date == $yesterday) {
                User::setZeroSROBySpecialitySemester($specialitySemester);
            }
        }

        $userIdsBySpecialitySemesters = User::getIdsBySpecialitySemesters($specialitySemesters);

        $semesters = Semester::getFinishedYesterdaySRO($today);
        foreach ($semesters as $semester) {
            User::setZeroSROBySemester($semester, $userIdsBySpecialitySemesters);
        }
    }
}
