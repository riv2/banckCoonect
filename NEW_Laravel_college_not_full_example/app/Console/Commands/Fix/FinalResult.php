<?php

namespace App\Console\Commands\Fix;

use App\Services\StudentRating;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class FinalResult extends Command
{
    protected $signature = 'fix:final_result';

    protected $description = 'Command description';

    public $i = 0;

    public function handle()
    {
//        StudentDiscipline::whereNotNull('test1_result')
//            ->whereNotNull('test_result')
//            ->whereNotNull('final_result')
//            ->whereNotNull('task_result')
//            ->whereRaw('`final_result` != (`test1_result_points` + `test_result_points` + `task_result_points`) ')
//            ->orderBy('id')
//            ->chunk(100, function($studentDisciplines) {
//                foreach ($studentDisciplines as $sd) {
//                    /** @var StudentDiscipline $sd */
//                    $this->i++;
//                    $this->info($this->i . '. id=' . $sd->id);
//
//                    $sd->calculateFinalResult();
//                }
//            });

        /*$ids = [
            '385254'
        ];*/

        $count = StudentDiscipline::where('plan_semester', '2019-20.2')
            ->whereNotNull('test_result')
            ->whereNotNull('final_result')
            ->whereNotNull('task_result')->count();

        $this->output->progressStart($count);

        StudentDiscipline::where('plan_semester', '2019-20.2')
            ->whereNotNull('test_result')
            ->whereNotNull('final_result')
            ->whereNotNull('task_result')
            ->chunk(1000, function($studentDisciplines) {
                foreach ($studentDisciplines as $sd) {
                    /** @var StudentDiscipline $sd */
                    $this->i++;
                    $sd->calculateFinalResult();
                    $this->info($this->i . '. id=' . $sd->id . ' Result=' . $sd->final_result);

                    $this->output->progressAdvance();
                }
            });

        $this->output->progressFinish();
    }
}
