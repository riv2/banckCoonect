<?php

namespace App\Console\Commands\Fix;

use App\ManualResult;
use App\Services\StudentRating;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class FixResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:results';

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
        StudentDiscipline::chunk(1000, function($studentDisciplines) {
            foreach ($studentDisciplines as $studentDiscipline) {
                /** @var $studentDiscipline StudentDiscipline */
                if ($studentDiscipline->test1_result !== null) {
                    $points = StudentDiscipline::getTest1ResultPoints($studentDiscipline->test1_result);

                    if ($studentDiscipline->test1_result_points != $points) {
                        $studentDiscipline->test1_result_points = $points;
                        $studentDiscipline->test1_result_letter = StudentRating::getLetter($studentDiscipline->test1_result);
                        $studentDiscipline->save();

                        if ($studentDiscipline->task_result !== null && $studentDiscipline->test_result !== null) {
                            $studentDiscipline->calculateFinalResult();
                        }
                    }
                }

                if ($studentDiscipline->task_result !== null) {
                    $manualSRO = ManualResult::hasNewSRO($studentDiscipline->student_id, $studentDiscipline->discipline_id);
                    if (!empty($manualSRO) && $studentDiscipline->test1_result !== null && $studentDiscipline->test_result !== null) {
                        $studentDiscipline->calculateFinalResult();
                    }
                }
            }
        });
    }
}
