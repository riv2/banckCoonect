<?php

namespace App\Console\Commands;

use App\MgApplications;
use App\Profiles;
use App\Role;
use App\Speciality;
use App\StudentDiscipline;
use App\User;
use App\UserRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportStudentsForGos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:student:gos {--type=gos_test}';

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
        $type = $this->option('type');

        $errorCount = 0;

        $fileName = 'gos_students.csv';
        if($type == 'eng_test')
        {
            $fileName = 'eng_students.csv';
        }

        $fileContent = file_get_contents(storage_path('import/' . $fileName));
        $fileContent = explode("\n", $fileContent);

        foreach ($fileContent as $str)
        {
            $str = explode('#', $str);
            if(isset($str[0]) && isset($str[1]) && isset($str[2])) {
                $createUserResult = $this->createUser(
                    $str[0],
                    $str[1],
                    $str[2],
                    $type
                );

                if (!$createUserResult) {
                    $errorCount++;
                }
            }
        }

        if($errorCount) {
            $this->error('Has ' . $errorCount . ' errors or warnings. Details in log file.');
        }
    }

    /**
     * @param $studentId
     * @param $studentName
     * @param $specialityName
     * @return bool
     */
    function createUser($studentId, $studentName, $specialityName, $type)
    {
        $student = DB::connection('miras_full')
            ->table('student')
            ->select([
                's_users.login as login',
                'student.study_language_id as study_language_id',
                'student.year as year'
            ])
            ->leftJoin('s_user_person', 's_user_person.person_id', '=', 'student.person_id')
            ->leftJoin('s_users', 's_user_person.user_id', '=', 's_users.id')
            ->where('student.id', $studentId)
            ->first();

        if(!$student || ($student && !$student->login))
        {
            Log::warning('import:student:gos User not found', ['student_id' => $studentId]);
            return false;
        }

        $specialityNamePart = explode('-', $specialityName);
        $specialityNamePart = $specialityNamePart[1] ?? '';

        if(!$specialityNamePart)
        {
            Log::warning('import:student:gos Invalid speciality name', [
                'student_id' => $studentId,
                'speciality' => $specialityName
            ]);
            return false;
        }

        $specialityName = trim(strtoupper($specialityNamePart));
        $speciality = Speciality
            ::with('disciplines')
            ->where('year', $student->year)
            ->where(function($query) use($specialityName){
                $query->where(DB::raw('UPPER(name)'), $specialityName)
                    ->orWhere(DB::raw('UPPER(name_en)'), $specialityName)
                    ->orWhere(DB::raw('UPPER(name_kz)'), $specialityName);
            })
            ->first();

        if(!$speciality)
        {
            Log::warning('import:student:gos Speciality not found', [
                'student_id' => $studentId,
                'speciality' => $specialityName
            ]);
            return false;
        }

        /* User section */
        $user = User::where('email', $student->login)->first();
        if($user)
        {
            if(!$user->hasClientRole())
            {
                $user->setRole(Role::NAME_CLIENT);
            }
            return true;
        }

        $newUser = new User();
        $newUser->name = $studentName;
        $newUser->email = $student->login;
        $newUser->status = true;
        $newUser->keycloak = true;
        $newUser->password = '';
        $newUser->import_type = $type;
        $newUser->save();

        $newUser->setRole(Role::NAME_CLIENT);

        $lang = 'ru';
        if($student->study_language_id == 1)
        {
            $lang = 'kz';
        }
        elseif($student->study_language_id == 3)
        {
            $lang = 'en';
        }

        /*Profile section*/
        $profile = new Profiles();
        $profile->user_id = $newUser->id;
        $profile->status = Profiles::STATUS_ACTIVE;
        $profile->fio = $studentName;
        $profile->import_full = false;
        $profile->front_id_photo = '';
        $profile->back_id_photo = '';
        $profile->paid = true;
        $profile->education_lang = $lang;
        $profile->education_speciality_id = $speciality->id;
        $profile->save();

        /*Relate disciplines*/
        foreach ($speciality->disciplines as $discipline)
        {
            $studentDiscipline = new StudentDiscipline();
            $studentDiscipline->discipline_id = $discipline->id;
            $studentDiscipline->student_id = $newUser->id;
            $studentDiscipline->payed = true;
            $studentDiscipline->iteration = 0;
            $studentDiscipline->save();
        }

        /*Create mgApplication*/
        $mgApplication = new MgApplications();
        $mgApplication->user_id = $newUser->id;
        $mgApplication->residence_registration_status = 'allow';
        $mgApplication->military_status = 'allow';
        $mgApplication->r086_status = 'allow';
        $mgApplication->r063_status = 'allow';
        $mgApplication->atteducation_status = 'allow';
        $mgApplication->nostrification_status = 'allow';
        $mgApplication->work_book_status = 'allow';
        $mgApplication->eng_certificate_status = 'allow';

        return true;
    }
}
