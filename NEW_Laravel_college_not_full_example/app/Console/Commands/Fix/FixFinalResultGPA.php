<?php

namespace App\Console\Commands\Fix;

use App\Discipline;
use App\Services\StudentRating;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class FixFinalResultGPA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:final_gpa';

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
        $credits = Discipline::pluck('ects', 'id');

        StudentDiscipline::whereNotNull('final_result')
            ->chunk(1000, function($studentDisciplines) use ($credits) {
                foreach ($studentDisciplines as $studentDiscipline) {
                    /** @var StudentDiscipline $studentDiscipline */

                    if ($studentDiscipline->discipline_id == 627) {
                        continue;
                    }

                    $studentDiscipline->final_result_gpa = StudentRating::getDisciplineGpa($studentDiscipline->final_result, $credits[$studentDiscipline->discipline_id]);
                    $studentDiscipline->save();
                }
            });
    }
}
