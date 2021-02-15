<?php

namespace App\Console\Commands\Fix;

use App\StudyGroupTeacher;
use Illuminate\Console\Command;

class StudyGroupTeacherDouble extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:study:group:teacher';

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
        $rows = \DB::select("select user_id, discipline_id, study_group_id, count(*) as cnt  from study_group_teacher
                    group by user_id, discipline_id, study_group_id
                    having COUNT(*) > 1");

        $this->output->progressStart(count($rows));
        $forDelete = [];

        foreach ($rows as $row)
        {
            /*if($row->user_id != 157)
            {
                continue;
                $this->output->progressAdvance();
            }*/

            $doubles = StudyGroupTeacher
                ::where('user_id', $row->user_id)
                ->where('discipline_id', $row->discipline_id)
                ->where('study_group_id', $row->study_group_id)
                ->get();


            foreach ($doubles as $k => $double)
            {
                if($k > 0)
                {
                    $forDelete[] = $double->id;
                }
            }

            $this->output->progressAdvance();

        }

        StudyGroupTeacher::whereIn('id', $forDelete)->delete();

        $this->output->progressFinish();

    }
}
