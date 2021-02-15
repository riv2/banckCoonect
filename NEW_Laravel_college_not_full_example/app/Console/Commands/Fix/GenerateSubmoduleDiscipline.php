<?php

namespace App\Console\Commands\Fix;

use App\OrderUser;
use App\Profiles;
use App\StudentDiscipline;
use App\StudentSubmodule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateSubmoduleDiscipline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:submodule:discipline';

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
        $reportFile = fopen(storage_path('export/generate_submodule_discipline.csv'), 'w');

        $this->output->progressStart($profilesCount);

        Profiles
            ::where('course', 1)
            ->with('speciality')
            ->whereNull('transfer_specialty')
            ->chunk(1000, function($profiles) use ($reportFile) {
            foreach ($profiles as $profile) {

                if( !$this->isTransfer($profile->user_id) ) {

                    $reportRow = [
                        $profile->user_id,
                        $profile->fio,
                        $profile->course,
                        $profile->speciality->name,
                        $profile->education_speciality_id
                    ];

                    $studentDisciplinesKz = StudentDiscipline
                        ::with('discipline')
                        ->where('student_id', $profile->user_id)
                        ->whereIn('submodule_id', [3, 4])
                        ->where('recommended_semester', 1)->first();

                    $studentDisciplinesEn = StudentDiscipline
                        ::with('discipline')
                        ->where('student_id', $profile->user_id)
                        ->whereIn('submodule_id', [1, 2])
                        ->where('recommended_semester', 1)->first();

                    if (!$studentDisciplinesKz) {
                        $this->addDisciplineToStudent($profile->user_id, 901, 3, 1, $reportRow);
                        $this->addDisciplineToStudent($profile->user_id, 2721, 3, 2, $reportRow);
                        $reportRow[] = 'add 901';
                        $reportRow[] = 'add 2721';
                    }

                    if (!$studentDisciplinesEn) {
                        $this->addDisciplineToStudent($profile->user_id, 2549, 1, 1, $reportRow);
                        $this->addDisciplineToStudent($profile->user_id, 2550, 2, 2, $reportRow);
                        $reportRow[] = 'add 2549';
                        $reportRow[] = 'add 2550';
                    }

                    if (!$studentDisciplinesKz || !$studentDisciplinesEn) {
                        fputcsv($reportFile, $reportRow);
                        //break;
                    }
                }

                $this->output->progressAdvance();

            }
        });

        fclose($reportFile);
        $this->output->progressFinish();
    }

    /**
     * @param $student_id
     * @param $disciplineId
     * @param $submoduleId
     * @param $semester
     * @return int
     */
    public function addDisciplineToStudent($student_id, $disciplineId, $submoduleId, $semester, &$reportFile)
    {
        $studentDiscipline = new StudentDiscipline();

        $studentDiscipline->student_id = $student_id;
        $studentDiscipline->discipline_id = $disciplineId;
        $studentDiscipline->submodule_id = $submoduleId;
        $studentDiscipline->recommended_semester = $semester;

        if($semester == 1)
        {
            $studentDiscipline->final_result = 0;
            $studentDiscipline->final_result_points = 0;
            $studentDiscipline->final_result_gpa = 0;
            $studentDiscipline->final_result_letter = 'F';
            $studentDiscipline->final_date = date('Y-m-d H:i:s', time());
            $studentDiscipline->archive = true;
            $studentDiscipline->at_semester = 1;
        }

        if($semester == 2)
        {
            $studentDiscipline->plan_semester = '2019-20.' . $semester;
            $studentDiscipline->plan_semester_user_id = 96;
            $studentDiscipline->plan_semester_date = date('Y-m-d H:i:s', time());
            $studentDiscipline->archive = false;

            $planSemesterAdmin = $this->getPlanSemesterAdmin( $student_id );
            $planSemesterStudent = $this->getPlanSemesterStudent( $student_id );

            $studentDiscipline->plan_admin_confirm = $planSemesterAdmin[0];
            $studentDiscipline->plan_admin_confirm_date = $planSemesterAdmin[1];
            $studentDiscipline->plan_admin_confirm_user_id = $planSemesterAdmin[2];
            $studentDiscipline->corona_distant = true;
            $studentDiscipline->remote_access = true;

            if($studentDiscipline->plan_admin_confirm_user_id)
            {
                $reportFile[] = 'admin_confirm';
            }

            $studentDiscipline->plan_student_confirm = $planSemesterStudent[0];
            $studentDiscipline->plan_student_confirm_date = $planSemesterStudent[1];

            if($studentDiscipline->plan_student_confirm)
            {
                $reportFile[] = 'student_confirm';
            }
        }

        $studentDiscipline->save();

        StudentSubmodule::where('submodule_id', $submoduleId)->where('student_id', $student_id)->delete();

        return $studentDiscipline->id;
    }

    /**
     * @param $userId
     * @return array
     */
    public function getPlanSemesterAdmin($userId)
    {
        $studentDiscipline = StudentDiscipline
            ::where('student_id', $userId)
            ->where('recommended_semester', 2)
            ->whereNotNull('plan_admin_confirm_user_id')
            ->first();

        if($studentDiscipline)
        {
            return [
                $studentDiscipline->plan_admin_confirm,
                $studentDiscipline->plan_admin_confirm_date,
                $studentDiscipline->plan_admin_confirm_user_id
            ];
        }

        return [
            null,
            null,
            null
        ];
    }

    /**
     * @param $userId
     * @return array
     */
    public function getPlanSemesterStudent($userId)
    {
        $studentDiscipline = StudentDiscipline
            ::where('student_id', $userId)
            ->where('recommended_semester', 2)
            ->whereNotNull('plan_student_confirm')
            ->first();

        if($studentDiscipline)
        {
            return [
                $studentDiscipline->plan_student_confirm,
                $studentDiscipline->plan_student_confirm_date
            ];
        }

        return [
            null,
            null
        ];
    }

    /**
     * @param $userId
     * @return bool
     */
    public function isTransfer($userId)
    {
        return (bool)OrderUser
            ::leftJoin('orders', 'orders.id', '=' ,'order_user.order_id')
            ->where('user_id', $userId)
            ->where('orders.order_name_id', 2)
            ->count();
    }
}
