<?php

namespace App\Console\Commands\Fix;

use App\StudentDiscipline;
use Illuminate\Console\Command;

class Test1ResultPointsInStudentsDisciplines extends Command
{
    protected $signature = 'fix:Test1ResultPointsInStudentsDisciplines';

    protected $description = 'Command description';

    public function handle()
    {
        StudentDiscipline::whereNotNull('test1_result')
            ->where('test1_result', '>', 0)
            ->where('test1_result_points', '<=', 4)
            ->chunk(1000, function ($studentDisciplines) {
                foreach ($studentDisciplines as $studentDiscipline) {
                    /** @var StudentDiscipline $studentDiscipline */

                    $points = StudentDiscipline::getTest1ResultPoints($studentDiscipline->test1_result);

                    if ($studentDiscipline->test1_result_points != $points) {
                        $studentDiscipline->test1_result_points = $points;
                        $studentDiscipline->save();
                    }
                }
            });
    }
}
