<?php

namespace App\Console\Commands\Fix;

use App\Profiles;
use App\SpecialityDiscipline;
use App\StudentDiscipline;
use App\StudyGroup;
use Illuminate\Console\Command;

class UpdateStudentParams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:student:params';

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
        $file = fopen(storage_path('import/update_student_params.csv'), 'r');
        $reportFile = fopen(storage_path('import/update_student_params_report.csv'), 'w');
        $rowCount = sizeof (file (storage_path('import/update_student_params.csv')));

        $this->output->progressStart($rowCount);

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $userId = $row[0];
            $course = $row[5];
            $specialityId = $row[8];
            $group = $row[10];
            $year = $row[11];

            $profile = Profiles::with('user')->where('user_id', $userId)->first();

            if($profile)
            {
                $groupId = $this->getGroupId($group);

                $params = [];

                if($groupId)
                {
                    $params['study_group_id'] = $groupId;
                }
                else
                {
                    $row[] = 'group not found';
                }

                $params['course'] = $course;
                $params['education_study_form'] = Profiles::EDUCATION_STUDY_FORM_FULLTIME;
                $profile->user->created_at = $this->setYearToDate($profile->user->created_at, $year);
                $profile->user->save();

                if($this->checkDisciplines($userId, $specialityId))
                {
                    $params['education_speciality_id'] = $specialityId;
                }
                else
                {
                    $row[] = 'fail speciality_discipline';
                }

                Profiles::where('user_id', $userId)->update($params);
            }
            else
            {
                $row[] = 'profile not found';
            }

            fputcsv($reportFile, $row);
            $this->output->progressAdvance();
        }

        fclose($reportFile);
        $this->output->progressFinish();
    }

    public function getGroupId($groupName)
    {
        $group = StudyGroup::where('name', $groupName)->first();

        return $group->id ?? null;
    }

    public function setYearToDate($date, $year)
    {
        $m = date('m', strtotime($date));
        $d = date('d', strtotime($date));
        $h = date('h', strtotime($date));
        $i = date('i', strtotime($date));
        $s = date('s', strtotime($date));

        return $year . '-' . $m . '-' . $d . ' ' . $h . ':' . $i . ':' . $s;
    }

    public function checkDisciplines($userId, $specialityId)
    {
        $studentDisciplineList = StudentDiscipline::where('student_id', $userId)->get();
        $studentDisciplineList = $studentDisciplineList->pluck('disicpline_id')->toArray();

        $specialityDisciplineList = SpecialityDiscipline::where('speciality_id', $specialityId)->get();
        $specialityDisciplineList = $specialityDisciplineList->pluck('disicpline_id')->toArray();

        if(count($studentDisciplineList) != count($specialityDisciplineList))
        {
            return false;
        }

        foreach ($studentDisciplineList as $studentDiscipline)
        {
            if(!in_array($studentDiscipline, $specialityDisciplineList))
            {
                return false;
            }
        }

        return true;
    }
}
