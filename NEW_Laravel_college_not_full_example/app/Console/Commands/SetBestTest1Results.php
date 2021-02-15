<?php

namespace App\Console\Commands;

use App\QuizResult;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class SetBestTest1Results extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test1:set_best_results';

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
        QuizResult::select('student_discipline_id')
            ->distinct()
            ->where('type', QuizResult::TYPE_TEST1)
            ->chunk(1000, function ($results) {
                foreach ($results as $result) {
                    $disciplineResults = QuizResult::where('student_discipline_id', $result->student_discipline_id)
                        ->orderBy('value', 'desc')
                        ->get();

                    if ($disciplineResults->count() < 2) {
                        continue;
                    }

                    /** @var QuizResult $bestResult */
                    $bestResult = $disciplineResults->first();

                    $studentDiscipline = StudentDiscipline::where('id', $result->student_discipline_id)->first();

                    if (empty($studentDiscipline)) {
                        continue;
                    }

                    // TODO Проверить если мануальныа оценка
                    // TODO использовать ф-ю setTestResult()

//                    $studentDiscipline->test1_result = $bestResult->value;
//                    $studentDiscipline->test1_result_points = $bestResult->points;
//                    $studentDiscipline->test1_result_letter = $bestResult->letter;
//                    $studentDiscipline->test1_date = $bestResult->created_at->timestamp;
//                    $studentDiscipline->test1_blur = $bestResult->blur;
//                    $studentDiscipline->save();
                }
            });
    }
}
