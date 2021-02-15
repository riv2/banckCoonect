<?php

namespace App\Console\Commands\Import;

use App\StudentDiscipline;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RatingFromProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:rating:from_production';

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
        $count = StudentDiscipline::count();
        $this->output->progressStart($count);

        StudentDiscipline::chunk(1000, function($rows){
            foreach($rows as $row)
            {
                $prodRow =  DB::connection('miras_prod')
                    ->table('students_disciplines')->where('id', $row->id)->first();

                if($prodRow)
                {
                    $row->payed = $prodRow->payed;
                    $row->final_result = $prodRow->final_result;
                    $row->final_result_points = $prodRow->final_result_points;
                    $row->final_result_gpa = $prodRow->final_result_gpa;
                    $row->final_result_letter = $prodRow->final_result_letter;
                    $row->final_date = $prodRow->final_date;
                    $row->final_manual = $prodRow->final_manual;

                    $row->save();
                }
                else
                {
                    $this->warn('Not found ' . $row->id);
                }

                $this->output->progressAdvance();
            }
        });

        $this->output->progressFinish();
    }
}
