<?php

namespace App\Console\Commands;

use App\{
    DiscountSemester,
    DiscountStudent,
    Semester,
    SpecialityDiscipline,
    StudentDiscipline,
    User};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{Log};

class GenerateTranslationStudentsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:translation:students {--user=0}';

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

        $userId = $this->option('user');

        $oUser = User::
        with('studentProfile')->
        where('id',$userId)->
        first();


        if( !empty($oUser) )
        {

            $iCourse      = $oUser->studentProfile->course ?? 0;
            $iCurSemester = $iCourse + 2;


            // получаем список дисциплин для этого студика
            $oSpecialityDiscipline = SpecialityDiscipline::
            where('speciality_id',$oUser->studentProfile->education_speciality_id)->
            where('semester',$iCurSemester)->
            get();


            if( !empty($oSpecialityDiscipline) && (count($oSpecialityDiscipline) > 0) )
            {
                foreach($oSpecialityDiscipline as $itemSD)
                {
                    $StudentDiscipline = StudentDiscipline::
                    where('student_id',$oUser->id)->
                    where('discipline_id',$itemSD->discipline_id)->
                    first();
                    if( empty($StudentDiscipline) )
                    {
                        $StudentDiscipline = new StudentDiscipline();
                        $StudentDiscipline->student_id     = $oUser->id;
                        $StudentDiscipline->discipline_id  = $itemSD->discipline_id;
                        $StudentDiscipline->remote_access  = 1;
                        $StudentDiscipline->corona_distant = 1;

                        $StudentDiscipline->plan_semester               = '2019-20.2';
                        $StudentDiscipline->plan_semester_date          = date('Y-m-d H:i:s');
                        $StudentDiscipline->plan_semester_user_id       = 7575;
                        $StudentDiscipline->plan_admin_confirm          = 1;
                        $StudentDiscipline->plan_admin_confirm_date     = date('Y-m-d H:i:s');
                        $StudentDiscipline->plan_admin_confirm_user_id  = 19298;
                        $StudentDiscipline->plan_student_confirm        = 1;
                        $StudentDiscipline->plan_student_confirm_date   = date('Y-m-d H:i:s');

                        $StudentDiscipline->save();
                    }
                }
            }

        }


        // add discount
        $oDiscountStudent = new DiscountStudent();
        $oDiscountStudent->type_id       = 1;
        $oDiscountStudent->user_id       = $userId;
        $oDiscountStudent->status        = DiscountStudent::STATUS_APPROVED;
        $oDiscountStudent->comment       = 'add discount';
        $oDiscountStudent->date_approve  = date('Y-m-d');
        $oDiscountStudent->moderator_id  = 96;
        $oDiscountStudent->save();


        // discount
        $oDiscountSemester = new DiscountSemester();
        $oDiscountSemester->discount_student_id = $oDiscountStudent->id;
        $oDiscountSemester->semester            = '2019-20.2';
        $oDiscountSemester->save();


    }
}
