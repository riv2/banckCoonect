<?php

namespace App\Console\Commands;

use App\StudentDiscipline;
use App\StudentSubmodule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSubmoduleRestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:submodule:restore';

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
        $this->backupDisciplines();
        $this->backupSubmodules();
    }

    public function backupDisciplines()
    {
        $relations = DB::connection('miras_restore')->table('students_disciplines')->get();
        $this->output->progressStart(count($relations));
        $emptyRelations = [];
        $notPayedRelation = [];

        foreach ($relations as $relation)
        {
            $model = StudentDiscipline::where('id', $relation->id)->first();

            if(!$model)
            {
                $emptyRelations[] = $relation->id;
                DB::table('students_disciplines')->insert((array)$relation);
            }
            else
            {
                if(!$model->payed_credits && $relation->payed_credits > 0)
                {
                    $notPayedRelation[] = $relation->id;
                }
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        print_r($emptyRelations);
        print_r($notPayedRelation);
    }

    public function backupSubmodules()
    {
        $relations = DB::connection('miras_restore')->table('student_submodule')->get();
        $this->output->progressStart(count($relations));
        $emptyRelations = [];

        foreach ($relations as $relation)
        {
            $model = StudentSubmodule::where('id', $relation->id)->first();

            if(!$model)
            {
                $emptyRelations[] = $relation->id;
                DB::table('student_submodule')->insert((array)$relation);
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        print_r($emptyRelations);
    }
}
