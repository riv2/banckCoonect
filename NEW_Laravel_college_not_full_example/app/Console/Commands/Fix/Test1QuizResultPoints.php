<?php

namespace App\Console\Commands\Fix;

use App\QuizResult;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class Test1QuizResultPoints extends Command
{
    protected $signature = 'fix:test1QuizResultPoints';

    protected $description = 'Command description';

    public function handle()
    {
        QuizResult::where('type', QuizResult::TYPE_TEST1)
            ->where('points', '<=', 4)
            ->where('value', '!=', 0)
            ->chunk(1000, function ($results) {
                foreach ($results as $result) {
                    /** @var QuizResult $result */
                    $points = StudentDiscipline::getTest1ResultPoints($result->value);

                    if ($result->points != $points) {
                        $result->points = $points;
                        $result->save();
                    }
                }
            });
    }
}
