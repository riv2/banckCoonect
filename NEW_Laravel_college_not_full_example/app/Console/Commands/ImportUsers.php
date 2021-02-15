<?php

namespace App\Console\Commands;

use App\AdminStudentComment;
use App\BcApplications;
use App\City;
use App\Discipline;
use App\MgApplications;
use App\Nationality;
use App\Profiles;
use App\Speciality;
use App\StudentDiscipline;
use App\StudyGroup;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:import {--year=2019} {--from=0} {--to=0} {--usersonly=false}';

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
        //Log::useDailyFiles(storage_path('logs/import_users' . date('Y_m_d', time()) . '.log'));

        $year = $this->option('year');
        $from = $this->option('from');
        $to = $this->option('to');
        $usersonly = $this->option('usersonly') == 'true' ? true : false;

        $this->info($usersonly);

        $file = fopen(storage_path('import/import_users_' . $year . '.csv'), 'r');
        $usersCreatedCount = 0;
        $usersUpdatedCount = 0;
        $counter = [];
        $createdUsers = [];
        $i = 0;

        $this->output->progressStart(sizeof (file (storage_path('import/import_users_' . $year . '.csv'))));

        $users = User::select(['id'])->where('keycloak', true)->whereNotNull('ex_id')->get();
        foreach ($users as $user)
        {
            $createdUsers[] = $user->ex_id;
        }


        while($row = fgetcsv($file, 0, ',', "'"))
        {
            $specialIdList = $this->getSpecialIdList();

            if( !in_array($row[0], $specialIdList))
            {
                continue;
            }

            if($i < $from)
            {
                $i++;
                $this->output->progressAdvance();
                continue;
            }

            if($i > $to && $to > 0)
            {
                break;
            }

            $userId = $row[0];

            if(count($row) < 45)
            {
                $this->error('Invalid column count. Userid: ' . $userId);
                Log::error('Invalid column count. Userid: ' . $userId);
                continue;
            }

            $addNewUser = false;

            if(!in_array($userId, $createdUsers)) {
                $user = User::where('ex_id', $userId)->with('studentProfile')->first();

                $startYear = $this->getStartYear($row[13]);
                if (!$user) {

                    $user = $this->createStudent($row, $startYear);

                    if ($user) {
                        $addNewUser = true;
                    }
                }

                if ($user) {
                    $createdAt = strtotime($row[24]);
                    $user->created_at = date('Y-m-d H:i:s', $createdAt > 0 ? $createdAt : time());
                    if(!$user->password) {
                        $user->password = bcrypt('Miras2019');
                        $user->keycloak = 0;
                    }

                    if(!$addNewUser)
                    {
                        $speciality = $this->convertSpeciality($row[14], $row[15], $startYear);
                        if($speciality)
                        {
                            $user->studentProfile->education_speciality_id = $speciality;
                        }

                    }

                    $user->save();

                    $user->studentProfile->updateDisciplines();
                    $user->studentProfile->updateSubmodules();

                    $user->setRole('client');

                    if (!$usersonly) {
                        $this->addDisciplineResult($row[21], $row[22], $row[23], $user->id, $counter);
                    }

                    if($addNewUser)
                    {
                        $usersCreatedCount++;
                    }
                    else
                    {
                        $usersUpdatedCount++;
                    }
                }
            }
            $createdUsers[] = $userId;
            $this->output->progressAdvance();

            $i++;
        }

        $this->output->progressFinish();

        $this->info('Created: ' . $usersCreatedCount);
        $this->info('Updated: ' . $usersUpdatedCount);
    }

    /**
     * @param $row
     * @param $year
     * @return User|null
     */
    public function createStudent($row, $year)
    {
        $user = new User();
        $user->ex_id = $row[0];
        $user->name = $row[1];
        $user->email = $row[45];
        $user->password = bcrypt('Miras2019');
        $user->phone = $row[44];
        $user->status = 1;
        $user->keycloak = 0;

        if($row[40])
        {
            $user->referral_source = 'At the invitation of the agitator';
            $user->referral_name = $row[40] ?? '';
        }

        $profile = new Profiles();
        $profile->status = 'active';
        $profile->education_status = 'student';
        $profile->check_level = 'or_cabinet';
        $profile->category = 'standart';
        $profile->iin = $row[11];
        $profile->fio = $row[1];
        $profile->bdate = date('Y-m-d', strtotime($row[3]));
        $profile->docnumber = $row[7];
        $profile->docseries = '';
        $profile->issuing = $row[8];
        $profile->issuedate = date('Y-m-d', strtotime($row[9]));
        $profile->expire_date = date('Y-m-d', strtotime($row[10]));
        $profile->sex = $this->convertGender($row[2]);
        $profile->mobile = $row[44];
        $profile->import_full = 1;
        $profile->front_id_photo = '';
        $profile->back_id_photo = '';
        $profile->paid = true;
        $profile->education_lang = $this->convertEducationLang($row[17]);
        $profile->education_speciality_id = $this->convertSpeciality($row[14], $row[15], $year);
        $profile->education_study_form = $this->convertEducationStudyForm($row[16]);
        $profile->nationality_id = $this->convertNationality($row[4]);
        $profile->ignore_debt = false;
        $profile->readonly = false;
        $profile->registration_step = 'finish';
        $profile->family_status = $this->convertFamilyStatus($row[12]);
        $profile->course = $row[13];
        $profile->team = $row[20];
        $profile->study_group_id = $this->getStudyGroupId($row[20]);

        $codeChar = $this->convertCodeChar($row[14]);

        if(in_array($codeChar, ['m', 'b']))
        {
            $application = null;

            if($codeChar == 'm')
            {
                $application = new MgApplications();
            }
            else
            {
                $application = new BcApplications();
            }

            $application->nationality_id = $this->convertNationality($row[4]);
            $application->citizenship_id = $this->convertCitizenship($row[5]);
            $application->city_id = $this->convertCity($row[42]);
            $application->country_id = $application->citizenship_id;
            $application->street = $row[43];
            $application->residence_registration_status = 'moderation';
            $application->r086_status = 'moderation';
            $application->r063_status = 'moderation';
            $application->education = $this->convertBaseEducation($row[18]);
            $application->nameeducation = $row[25];
            $application->numeducation = $row[28];
            $application->dateeducation = date('Y-m-d', strtotime($row[26]));
            $application->atteducation_status = 'moderation';
            $application->eduspecialty = $row[38];
            $application->edudegree = $row[27];
            $application->part = 'finish';
            $application->nostrification_status = 'moderation';

            if($codeChar == 'b')
            {
                $application->ikt = $row[29];
                $application->ent_total = $row[30] ? $row[30] : 0;
            }


            if(!$profile->education_speciality_id)
            {
                $this->error('Speciality not found: ' . $row[14] . ' user id: ' . $row[0]);
                Log::error('Speciality not found: ' . $row[14]);

                return false;
            }
            else
            {

                $user->save();
                $profile->user_id = $user->id;
                $profile->save();
                $application->user_id = $user->id;
                $application->save();

                $textList = [
                    'О себе: ' . $row[32],
                    'Наличие гранта: ' . $row[31],
                    'Служил: ' . $row[33],
                    'Сирота: ' . $row[34],
                    'Инвалид: ' . $row[35],
                    'С какого курса перевелся: ' . $row[36],
                    'История: ' . $row[41],
                ];

                foreach ($textList as $text)
                {
                    AdminStudentComment::insert([
                        'author_id' => 96,
                        'user_id' => $user->id,
                        'check_level' => $user->studentProfile->scheck_level,
                        'text' => $text,
                        'created_at' => DB::raw('NOW()')
                    ]);
                }
            }

            return $user;
        }
        else
        {
            $this->error('Code char error. Speciality - ' . $row[14]);
            Log::error('Code char error. Speciality - ' . $row[14]);
        }

        return null;
    }

    /**
     * @param $disciplineExId
     * @param $disciplineName
     * @param $val
     * @param $userId
     * @return bool
     */
    public function addDisciplineResult($disciplineExId, $disciplineName, $val, $userId, &$counter)
    {
        $disciplines = Discipline::where('ex_id', $disciplineExId)->get();

        if(!$disciplines)
        {
            $this->error('Discipline not found: ' . $disciplineName);
            Log::error('Discipline not found: ' . $disciplineName);
            return false;
        }

        $disciplineListId = [];

        foreach ($disciplines as $disItem)
        {
            $disciplineListId[] = $disItem->id;
        }

        $counter[$userId][$disciplineExId] = $counter[$userId][$disciplineExId] ?? 0;
        $counter[$userId][$disciplineExId] = $counter[$userId][$disciplineExId] + 1;

        $studentDiscipline = StudentDiscipline
            ::whereIn('discipline_id', $disciplineListId)
            ->where('student_id', $userId)
            ->offset($counter[$userId][$disciplineExId] - 1)
            ->first();

        if(!$studentDiscipline)
        {
            $this->error('Student discipline relation not found. user_id = ' . $userId . ' discipline_name = ' . $disciplineName . ' ' . $counter[$userId][$disciplineExId]);
            Log::error('Student discipline relation not found. user_id = ' . $userId . ' discipline_name = ' . $disciplineName . ' ' . $counter[$userId][$disciplineExId]);
            return false;
        }

        $studentDiscipline->setFinalResult($val);
        $studentDiscipline->save();
    }

    /**
     * @param $str
     * @return int
     */
    public function convertGender($str)
    {
        if($str == 'FEMALE') return 0;
        if($str == 'MALE') return 1;
    }

    /**
     * @param $str
     * @return |null
     */
    public function convertNationality($str)
    {
        $nationality = Nationality::where(DB::raw('LOWER(name_ru)'), mb_strtolower($str))->first();

        return $nationality->id ?? null;
    }

    /**
     * @param $str
     * @return mixed|null
     */
    public function convertCitizenship($str)
    {
        $list = [
            'Казахстан' => 1,
            'Узбекистан' => 4,
            'Украина'   => 3,
            'Россия' => 2
        ];

        return $list[$str] ?? null;
    }

    /**
     * @param $str
     * @return mixed|nulleduspeciality
     */
    public function convertFamilyStatus($str)
    {
        $list = [
            'не женат/ не замужем' => 'single',
            'женат/замужем' => 'marital'
        ];

        return $list[$str] ?? null;
    }

    /**
     * @param $str
     * @return string
     */
    public function convertCodeChar($str)
    {
        $code = mb_substr($str, 1, 1);

        if($code == 'B' || $code == 'В') return 'b';
        if($code == 'M' || $code == 'М') return 'm';

        return false;
    }

    public function convertTrajectory($str)
    {
        $str = trim($str);

        if($str && !in_array($str,['А', 'А', 'А1', 'А1', 'A2', 'А2', 'А3', 'А3', 'А4', 'А4', 'А5', 'А5', 'А6', 'А6']))
        {
            return $str;
        }

        return '';
    }

    /**
     * @param $str
     * @param $year
     * @return |null
     */
    public function convertSpeciality($str, $trajectory, $year)
    {
        $codeChar = $this->convertCodeChar($str);
        if(!$codeChar || !in_array($codeChar, ['b', 'm']))
        {
            return null;
        }

        $mached = [];

        preg_match('|-.+|', $str, $mached);
        $specialityName = $mached[0] ?? null;
        $specialityName = str_replace('- ', '', $specialityName);

        if(!$specialityName)
        {
            return null;
        }

        $trajectory = $this->convertTrajectory($trajectory);

        if($trajectory) {
            $specialityName = $specialityName . '. ' . $trajectory;
        }

        if($specialityName == 'Финансы')
        {
            $specialityName = $specialityName . '. ' . 'Банковское дело';
        }

        if($specialityName == 'Дизайн' && $year == 2017)
        {
            $specialityName = $specialityName . '. ' . 'Архитектурный дизайн';
        }

        $speciality = Speciality
            ::where(\DB::raw('LOWER(name)'), $specialityName)
            ->where('code_char', $codeChar)
            ->where('year', $year)
            ->first();

        if(!$speciality)
        {
            return null;
        }

        return $speciality->id ?? null;
    }

    /**
     * @param $str
     * @return mixed|null
     */
    public function convertEducationStudyForm($str)
    {
        $list = [
            'Вечерняя' => 'evening',
            'Очная' => 'fulltime',
            'Онлайн'    => 'online',
            'Дистанционная' => 'distant',
            'Заочная' => 'extramural'
        ];

        return $list[$str] ?? null;
    }

    /**
     * @param $str
     * @return mixed|null
     */
    public function convertEducationLang($str)
    {
        $list = [
            'Казахский' => 'kz',
            'Русский' => 'ru',
            'Английский' => 'en'
        ];

        return $list[$str] ?? null;
    }

    /**
     * @param $str
     * @return mixed|null
     */
    public function convertBaseEducation($str)
    {
        $list = [
            'Среднее' => 'high_school',
            'СПО' => 'vocational_education',
            'ВО' => 'bachelor'
        ];

        return $list[$str] ?? null;
    }

    /**
     * @param $str
     * @return |null
     */
    public function convertCity($str)
    {
        $cityName = $this->mb_str_replace(['г.', 'с.', 'а.', 'ст.'], '', $str);
        $city = City::where('name', $cityName)->first();

        return $city->id ?? null;
    }


    /**
     * @param $search
     * @param $replace
     * @param $string
     * @return mixed
     */
    function mb_str_replace($search, $replace, $string)
    {
        $charset = mb_detect_encoding($string);
        $unicodeString = iconv($charset, "UTF-8", $string);

        return str_replace($search, $replace, $unicodeString);
    }

    /**
     * @param $course
     * @return false|int|string
     */
    public function getStartYear($course)
    {
        $year = date('Y', time());
        return $year - $course ;
    }

    /**
     * @param $groupName
     * @return |null
     */
    public function getStudyGroupId($groupName)
    {
        $group = StudyGroup::where('name', $groupName)->first();

        return $group->id ?? null;
    }

    public function getSpecialIdList()
    {
        return [
            18740
        ];
    }
}
