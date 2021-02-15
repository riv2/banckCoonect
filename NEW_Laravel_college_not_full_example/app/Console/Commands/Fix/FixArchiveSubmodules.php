<?php

namespace App\Console\Commands\Fix;

use App\DisciplineSubmodule;
use App\Profiles;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class FixArchiveSubmodules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:archive:submodules';

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
        $reportFile = fopen(storage_path('export/fix_archive_submodules.csv'), 'w');

        $this->output->progressStart(Profiles::where('course', 1)->count());

        Profiles::where('course', 1)->chunk(1000, function($profileList) use($reportFile){
            foreach ($profileList as $profile)
            {
                $submoduleDisciplineList = DisciplineSubmodule
                    ::select('discipline_submodule.*')
                    ->leftJoin('speciality_submodule', 'speciality_submodule.submodule_id', '=', 'discipline_submodule.submodule_id')
                    ->where('speciality_submodule.speciality_id', '=', $profile->education_speciality_id);

                $disciplineList = [];

                if($submoduleDisciplineList)
                {
                    $disciplineList = $submoduleDisciplineList->pluck('discipline_id')->toArray();
                }

                $studentDisciplineList = StudentDiscipline
                    ::with('discipline')
                    ->where('student_id', $profile->user_id)
                    ->whereIn('discipline_id', $disciplineList)
                    ->where('archive', true)
                    ->get();

                if($studentDisciplineList)
                {
                    foreach ($studentDisciplineList as $studentDiscipline)
                    {
                        $report = [];
                        $report[] = $profile->user_id;
                        $report[] = $profile->fio;
                        $report[] = $studentDiscipline->discipline->name;
                        $report[] = $studentDiscipline->discipline_id;
                        $report[] = 'dearchived';
                        fputcsv($reportFile, $report);
                    }
                }

                StudentDiscipline
                    ::with('discipline')
                    ->where('student_id', $profile->user_id)
                    ->whereIn('discipline_id', $disciplineList)
                    ->where('archive', true)
                    ->update(['archive' => 0]);

                $this->output->progressAdvance();
            }

        });

        fclose($reportFile);

        $this->output->progressFinish();
    }
}
