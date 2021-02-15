<?php

namespace App\Console\Commands\Fix;

use App\StudentDiscipline;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteResult30 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'result:delete:30';

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
        $studentDisciplines = StudentDiscipline
            //::whereHas('quizeResults')
            ::whereNotNull('test1_result');

        $rowsCount = $studentDisciplines;
        $rowsCount = $rowsCount->count();
        //$studentDisciplines->with('quizeResults');
        $this->output->progressStart($rowsCount);
        $updateCount = 0;

        $studentDisciplines->chunk(1000, function($studentDisciplines) use ($updateCount){
            foreach ($studentDisciplines as $studentDiscipline)
            {
                $large30 = (bool)DB::table('quize_result')
                    ->where('student_discipline_id', $studentDiscipline->id)
                    ->where('value', '>=', 30)
                    ->count();

                if(!$large30)
                {
                    $this->info($studentDiscipline->id);

                    DB::table('quize_result')
                        ->where('student_discipline_id', $studentDiscipline->id)
                        ->delete();

                    $studentDiscipline->test1_result = null;
                    $studentDiscipline->test1_result_letter = null;
                    $studentDiscipline->test1_result_points = null;
                    $studentDiscipline->test1_date = null;
                    $studentDiscipline->test1_result_trial = null;
                    $studentDiscipline->test1_blur = 0;
                    $studentDiscipline->save();
                }

                $this->output->progressAdvance();
            }
        });

        $this->output->progressFinish();
        $this->info('Updated rows: ' . $updateCount);
    }
}
