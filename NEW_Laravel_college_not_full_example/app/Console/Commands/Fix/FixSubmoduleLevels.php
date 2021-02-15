<?php

namespace App\Console\Commands\Fix;

use App\Profiles;
use App\StudentDiscipline;
use App\StudyPlanLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSubmoduleLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:submodule:levels';

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
        $profilesCount = Profiles::where('course', 1)->count();
        $reportFile = fopen(storage_path('export/fix_submodule_levels_report.csv'), 'w');

        $this->output->progressStart($profilesCount);

        Profiles::where('course', 1)->with('speciality')->chunk(1000, function($profiles) use ($reportFile){
            foreach ($profiles as $profile)
            {
                $reportRowTemplate = [
                    $profile->user_id,
                    $profile->fio,
                    $profile->course,
                    $profile->speciality->name,
                    $profile->education_speciality_id
                ];

                $studentDisciplines = StudentDiscipline
                    ::with('discipline')
                    ->where('student_id', $profile->user_id)
                    ->whereNotNull('submodule_id')
                    ->where('recommended_semester', 1)->get();

                if($studentDisciplines && count($studentDisciplines) <= 2)
                {
                    $validForNextSemester = [];
                    foreach ($studentDisciplines as $studentDiscipline)
                    {
                        $reportRow = $reportRowTemplate;

                        $reportRow[] = $studentDiscipline->discipline->name;
                        $reportRow[] = $studentDiscipline->discipline_id;
                        $reportRow[] = $studentDiscipline->submodule_id;
                        $reportRow[] = $studentDiscipline->recommended_semester;

                        $studentDisciplineForNextSemester = $this->studentDisciplineForNextSemester(
                            $profile->user_id,
                            $studentDiscipline->discipline_id,
                            $studentDiscipline->submodule_id);

                        if(!$studentDisciplineForNextSemester)
                        {
                            $nexDisciplineId = $this->getNextLevel($studentDiscipline->submodule_id, $studentDiscipline->discipline_id);

                            if($nexDisciplineId) {
                                $studentDisciplineForNextSemester = $this->addDisciplineToStudent(
                                    $profile->user_id,
                                    $nexDisciplineId,
                                    $studentDiscipline->submodule_id,
                                    2
                                );

                                $reportRow[] = 'add to semester 2 (' . $nexDisciplineId . ')';
                            }
                            else
                            {
                                $reportRow[] = 'next disc not found';
                            }
                        }

                        $validForNextSemester[] = $studentDisciplineForNextSemester;
                        fputcsv($reportFile, $reportRow);
                    }

                    $studentDisciplinesSem2 = StudentDiscipline
                        ::where('student_id', $profile->user_id)
                        ->whereNotNull('submodule_id')
                        ->where('recommended_semester', 2)
                        ->whereNotIn('id', $validForNextSemester)
                        ->get();

                    foreach ($studentDisciplinesSem2 as $studentDiscipline)
                    {
                        $reportRow = $reportRowTemplate;

                        $reportRow[] = $studentDiscipline->discipline->name;
                        $reportRow[] = $studentDiscipline->discipline_id;
                        $reportRow[] = $studentDiscipline->submodule_id;
                        $reportRow[] = $studentDiscipline->recommended_semester;

                        if( $this->studentDisciplineIsEmpty($studentDiscipline) )
                        {

                            $submoduleList = [];

                            if($studentDiscipline->submodule_id == 1 || $studentDiscipline->submodule_id == 2)
                            {
                                $submoduleList = [1,2];
                            }

                            if($studentDiscipline->submodule_id == 3 || $studentDiscipline->submodule_id == 4)
                            {
                                $submoduleList = [3,4];
                            }

                            $submodInSem1 = StudentDiscipline
                                ::where('student_id', $profile->user_id)
                                ->whereIn('submodule_id', $submoduleList)
                                ->where('recommended_semester', 1)
                                ->count();

                            if($submodInSem1)
                            {
                                $reportRow[] = 'remove ' . $studentDiscipline->discipline_id;

                                StudyPlanLog::where('student_discipline_id', $studentDiscipline->id)->delete();
                                $studentDiscipline->delete();
                            }
                        }
                        else
                        {
                            $reportRow[] = 'not empty';
                        }

                        fputcsv($reportFile, $reportRow);
                    }


                }
                /*elseif(count($studentDisciplines) > 2)
                {
                    $reportRow[] = 'many disciplines';
                }*/

                $this->output->progressAdvance();

                /*$studentdisciplines = StudentDiscipline
                    ::where('student_id', $profile->user_id)
                    ->whereNotNull('submodule_id')
                    ->where('recommended_semester', 2)->get();

                $exceptList = [];
                foreach ($studentdisciplines as $mainsubmodule)
                {
                    if($this->studentDisciplineIsEmpty($mainsubmodule))
                    {
                        $mainsubmodule->delete();
                    }
                    else
                    {
                        $exceptList[] = $mainsubmodule->discipline_id;
                    }
                }

                if(count($exceptList) == 0)
                {
                    $studentdisciplines = StudentDiscipline
                        ::where('student_id', $profile->user_id)
                        ->whereNotNull('submodule_id')
                        ->where('recommended_semester', 1)->get();

                    foreach ($studentdisciplines as $mainsubmodule)
                    {
                        $nextLevel = $this->getNextLevel($mainsubmodule->submodule_id, $studentdisciplines->discipline_id);

                        //if()
                    }
                }*/
            }

        });

        fclose($reportFile);

        $this->output->progressFinish();
    }

    public function getNextLevel($submoduleId, $currentDisciplineId)
    {

        $submodules = [
            1 => [
                2549,
                2550,
                897,
                898,
            ],
            2 => [
                2550,
                897,
                898,
                899
            ],
            3 => [
                901,
                2721,
                902,
                2722,
                903,
                2723,
                904,
                2724,
                905
            ],
            4 => [
                902,
                2722,
                903,
                2723,
                904,
                2724,
                905,
                906
            ]
        ];

        foreach ($submodules[$submoduleId] as $k => $disciplineId)
        {
            if($disciplineId == $currentDisciplineId)
            {
                return $submodules[$submoduleId][$k + 1] ?? null;
            }
        }

        return null;
    }

    public function studentDisciplineIsEmpty($studentDiscipline)
    {
        $notEmpty = $studentDiscipline->test1_result ||
            $studentDiscipline->test1_result_points ||
            $studentDiscipline->test1_result_letter ||

            $studentDiscipline->test_result ||
            $studentDiscipline->test_result_letter ||
            $studentDiscipline->test_result_points ||
            $studentDiscipline->test_manual ||

            $studentDiscipline->final_result ||
            $studentDiscipline->final_result_letter ||
            $studentDiscipline->final_result_points ||
            $studentDiscipline->final_manual ||

            $studentDiscipline->payed == 1 ||
            $studentDiscipline->payed_credits ||
            $studentDiscipline->remote_access ||

            $studentDiscipline->task_result ||
            $studentDiscipline->task_result_points ||
            $studentDiscipline->task_result_letter ||
            $studentDiscipline->task_manual;

        return !$notEmpty;
    }

    public function addDisciplineToStudent($student_id, $disciplineId, $submoduleId, $semester)
    {
        $studentDiscipline = new StudentDiscipline();

        $studentDiscipline->student_id = $student_id;
        $studentDiscipline->discipline_id = $disciplineId;
        $studentDiscipline->submodule_id = $submoduleId;
        $studentDiscipline->recommended_semester = $semester;

        $studentDiscipline->save();

        return $studentDiscipline->id;
    }

    /**
     * @param $student_id
     * @param $disciplineId
     * @param $submoduleId
     * @return bool
     */
    public function studentDisciplineForNextSemester($student_id, $disciplineId, $submoduleId)
    {
        $nextLevel = $this->getNextLevel($submoduleId, $disciplineId);

        $submoduleList = [];

        if($submoduleId == 1 || $submoduleId == 2)
        {
            $submoduleList = [1,2];
        }

        if($submoduleId == 3 || $submoduleId == 4)
        {
            $submoduleList = [3,4];
        }

        if($nextLevel)
        {
            $studentDiscipline = StudentDiscipline
                ::where('student_id', $student_id)
                ->whereIn('submodule_id', $submoduleList)
                ->where('discipline_id', $nextLevel)
                ->where('recommended_semester', 2)->first();

            return $studentDiscipline->id ?? false;
        }

        return false;
    }
}
