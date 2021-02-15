<?php

namespace App\Console\Commands\Fix;

use App\DisciplineSubmodule;
use App\Profiles;
use App\SpecialityDiscipline;
use App\StudentDiscipline;
use App\StudentSubmodule;
use App\StudyPlanLog;
use Illuminate\Console\Command;

class ArchiveStudentDiscipline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archive:student_discipline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $submoduleDisciplines = [];

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
        $profileCount = Profiles::count();
        $this->submoduleDisciplines = $this->getSubmoduleDisciplines();
        $logFile = fopen(storage_path('export/archive_student_discipline_report.csv'), 'w');

        //print_r($this->submoduleDisciplines);
        //exit;

        fputcsv($logFile, [
            'ID студента',
            'ФИО',
            'Специальность',
            'ID специальности',
            'Курс',
            'Дисциплина',
            'ID дисциплины',
            'Cабмодуль',
            'Рекомендуемый семестр',
            'Действие'
        ]);
        $this->output->progressStart($profileCount);
        Profiles::with('speciality')->chunk(1000, function($profiles) use($logFile){
            foreach ($profiles as $profile)
            {
                if(!$profile->education_speciality_id)
                {
                    $this->output->progressAdvance();
                    continue;
                }

                $logTemp = [
                    $profile->user_id,
                    $profile->fio,
                    $profile->speciality->name,
                    $profile->education_speciality_id,
                    $profile->course
                ];

                $specialityDisciplineList = SpecialityDiscipline
                    ::where('speciality_id', $profile->education_speciality_id)
                    ->get();

                $originDisciplineIdList = $specialityDisciplineList->pluck('discipline_id')->toArray();

                $studentDisciplineList = StudentDiscipline
                    ::where('student_id', $profile->user_id)
                    ->with('discipline')
                    ->get();

                foreach ($studentDisciplineList as $studentDiscipline)
                {
                    $log = $logTemp;
                    $log[] = $studentDiscipline->discipline->name;
                    $log[] = $studentDiscipline->discipline_id;
                    $log[] = $studentDiscipline->submodule_id;
                    $log[] = $studentDiscipline->recommended_semester;

                    if( in_array($studentDiscipline->discipline_id, $originDisciplineIdList))
                    {
                        if($studentDiscipline->is_elective)
                        {
                            $studentDiscipline->is_elective = false;
                            $studentDiscipline->save();
                            $log[] = 'unset elective';
                            fputcsv($logFile, $log);
                        }
                    }
                    else
                    {
                        if( !$this->studentDisciplineIsEmpty($studentDiscipline) )
                        {
                            $studentDiscipline->archive = true;
                            $studentDiscipline->save();
                            $log[] = 'to archive';
                            fputcsv($logFile, $log);
                        }
                        else
                        {
                            if( $studentDiscipline->submodule_id &&
                                !$this->SpecialityDisciplinesHasSubmodule($originDisciplineIdList, $studentDiscipline->submodule_id))
                            {
                                StudentSubmodule
                                    ::where('student_id', $profile->user_id)
                                    ->where('submodule_id', $studentDiscipline->submodule_id)
                                    ->delete();

                                $log[] = 'deleted submodule';
                            }

                            StudyPlanLog::where('student_discipline_id', $studentDiscipline->id)->delete();
                            $studentDiscipline->delete();

                            $log[] = 'deleted';
                            fputcsv($logFile, $log);
                        }
                    }


                }

                /*$missingDisciplineList = $this->getMissingDisciplines($specialityDisciplineList, $studentDisciplineList);

                foreach ($missingDisciplineList as $discId)
                {
                    $log = $logTemp;
                    $log[] = '';
                    $log[] = $discId;
                    $log[] = '';
                    $log[] = '';
                    $log[] = 'create';

                    //fputcsv($logFile, $log);
                }*/


                $this->output->progressAdvance();
            }
        });

        fclose($logFile);
        $this->output->progressFinish();
    }

    /**
     * @param $studentDiscipline
     * @return bool
     */
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

    /**
     * @return array
     */
    public function getSubmoduleDisciplines()
    {
        $result = [];

        $submoduleDisciplines = DisciplineSubmodule::get();

        foreach ( $submoduleDisciplines as $submoduleDiscipline )
        {
            $result[$submoduleDiscipline->submodule_id][] = $submoduleDiscipline->discipline_id;
        }

        return $result;
    }

    /**
     * @param $specDisList
     * @param $submoduleId
     * @return bool
     */
    public function SpecialityDisciplinesHasSubmodule($specDisList, $submoduleId)
    {
        foreach ($specDisList as $specDis)
        {
            if( in_array($specDis, $this->submoduleDisciplines[$submoduleId]) )
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $specDisList
     * @param $studentDisciplineList
     * @return array
     */
    public function getMissingDisciplines($specDisList, $studentDisciplineList)
    {
        $studentDisciplineList = $studentDisciplineList->pluck('disicpline_id')->toArray();
        $resultList = [];

        foreach ($specDisList as $specDis)
        {
            if(!in_array($specDis, $studentDisciplineList))
            {
                $resultList[] = $specDis;
            }
        }

        return $resultList;
    }
}
