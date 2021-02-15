<?php

namespace App\Console\Commands;

use App\BcApplications;
use App\MgApplications;
use App\Nationality;
use App\Profiles;
use App\Speciality;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ImportUsersMicro extends Command
{
    const FIELD_EX_ID = 0;
    const FIELD_IIN = 1;
    const FIELD_FIO = 2;
    const FIELD_SPECIALITY = 3;
    const FIELD_EDUCATION_FORM = 4;
    const FIELD_BASE_EDICATION = 6;
    const FIELD_GROUP = 7;
    const FIELD_LANG = 8;
    const FIELD_COURSE = 9;
    const FIELD_GENDER = 10;
    const FIELD_ADDRESS = 11;
    const FIELD_BDATE = 12;
    const FIELD_AGE = 13;
    const FIELD_NATIONALITY = 14;
    const FIELD_CITIZENSHIP = 15;
    const FIELD_LAST_EDUCATION = 16;
    const FIELD_EDUCATION_ADDRESS = 17;
    const FIELD_AREA = 18;
    const FIELD_PHONE = 19;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:import:micro {--from=0} {--to=0}';

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
        $from = $this->option('from');
        $to = $this->option('to');

        $file = fopen(storage_path('import/import_users_micro.csv'), 'r');
        $usersCreatedCount = 0;
        $usersUpdatedCount = 0;
        $counter = [];
        $createdUsers = [];
        $i = 0;

        $this->output->progressStart(sizeof (file (storage_path('import/import_users_micro.csv'))));

        /*$users = User::select(['id'])->where('keycloak', true)->whereNotNull('ex_id')->get();
        foreach ($users as $user)
        {
            $createdUsers[] = $user->ex_id;
        }*/


        while($row = fgetcsv($file, 0, ',', "'"))
        {
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

            $userId = $row[self::FIELD_EX_ID];

            if(count($row) < 21)
            {
                $this->error('Invalid column count. Userid: ' . $userId);
                Log::error('Invalid column count. Userid: ' . $userId);
                continue;
            }

            $addNewUser = false;
            $user = User::where('ex_id', $userId)->with('studentProfile')->first();

            $startYear = $this->getStartYear($row[self::FIELD_COURSE]);
            if (!$user) {

                $user = $this->createStudent($row, $startYear);

                if ($user) {
                    $addNewUser = true;
                }
            }

            if ($user) {
                $user->created_at = $startYear . '-01-01';

                if(!$user->password) {
                    $user->password = bcrypt('Miras2019');
                    $user->keycloak = 0;
                }

                $user->save();
                $user->studentProfile->updateDisciplines();

                $user->setRole('client');

                if($addNewUser)
                {
                    $usersCreatedCount++;
                }
                else
                {
                    $usersUpdatedCount++;
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

    public function createStudent($row, $year)
    {

        $user = new User();
        $user->ex_id = $row[self::FIELD_EX_ID];
        $user->name = $row[self::FIELD_FIO];
        $user->email = $row[self::FIELD_PHONE];
        $user->password = bcrypt('Miras2019');
        $user->phone = $row[self::FIELD_PHONE];
        $user->status = 1;
        $user->keycloak = 0;

        /*if($row[40])
        {
            $user->referral_source = 'At the invitation of the agitator';
            $user->referral_name = $row[40] ?? '';
        }*/

        $profile = new Profiles();
        $profile->status = 'active';
        $profile->education_status = 'student';
        $profile->check_level = 'or_cabinet';
        $profile->category = 'standart';
        $profile->iin = $row[self::FIELD_IIN];
        $profile->fio = $row[self::FIELD_FIO];
        $profile->bdate = date('Y-m-d', strtotime($row[self::FIELD_BDATE]));
        $profile->docnumber = null;
        $profile->docseries = null;
        $profile->issuing = null;
        $profile->issuedate = null;
        $profile->expire_date = null;
        $profile->sex = $this->convertGender($row[self::FIELD_GENDER]);
        $profile->mobile = $row[self::FIELD_PHONE];
        $profile->import_full = 1;
        $profile->front_id_photo = '';
        $profile->back_id_photo = '';
        $profile->paid = true;
        $profile->education_lang = $this->convertEducationLang($row[self::FIELD_LANG]);
        $profile->education_speciality_id = $this->convertSpeciality($row[self::FIELD_SPECIALITY], $year);
        $profile->education_study_form = $this->convertEducationStudyForm($row[self::FIELD_EDUCATION_FORM]);
        $profile->nationality_id = $this->convertNationality($row[self::FIELD_NATIONALITY]);
        $profile->ignore_debt = false;
        $profile->readonly = false;
        $profile->registration_step = 'finish';
        $profile->family_status = null;
        $profile->course = $row[self::FIELD_COURSE];
        $profile->team = $row[self::FIELD_GROUP];

        $codeChar = $this->convertCodeChar($row[self::FIELD_SPECIALITY]);

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

            $application->nationality_id = $this->convertNationality($row[self::FIELD_NATIONALITY]);
            $application->citizenship_id = $this->convertCitizenship($row[self::FIELD_CITIZENSHIP]);
            $application->city_id = null;
            $application->country_id = null;
            $application->street = $row[self::FIELD_ADDRESS];
            $application->residence_registration_status = 'moderation';
            $application->r086_status = 'moderation';
            $application->r063_status = 'moderation';
            $application->education = $this->convertBaseEducation($row[self::FIELD_BASE_EDICATION]);
            $application->nameeducation = $row[self::FIELD_LAST_EDUCATION];
            $application->numeducation = null;
            $application->dateeducation = null;
            $application->atteducation_status = 'moderation';
            $application->eduspecialty = null;
            $application->edudegree = null;
            $application->part = 'finish';
            $application->nostrification_status = 'moderation';

            if($codeChar == 'b')
            {
                $application->ikt = null;
                $application->ent_total = null;
            }


            if(!$profile->education_speciality_id)
            {
                $this->error('Speciality not found: ' . $row[self::FIELD_SPECIALITY]);
                Log::error('Speciality not found: ' . $row[self::FIELD_SPECIALITY]);

                return false;
            }
            else
            {

                $user->save();
                $profile->user_id = $user->id;
                $profile->save();
                $application->user_id = $user->id;
                $application->save();
            }

            return $user;
        }
        else
        {
            $this->error('Code char error. Speciality - ' . $row[self::FIELD_SPECIALITY]);
            Log::error('Code char error. Speciality - ' . $row[self::FIELD_SPECIALITY]);
        }

        return null;
    }

    public function convertSpeciality($str, $year)
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

        if($specialityName == 'Финансы')
        {
            $specialityName = $specialityName . '. ' . 'Банковское дело';
        }

        if($specialityName == 'Дизайн' && $year == 2017)
        {
            $specialityName = $specialityName . '. ' . 'Архитектурный дизайн';
        }

        if($specialityName == 'Биология' && $year == 2018)
        {
            $specialityName = 'Биология. Учитель биологии учреждений среднего и профессионального образования';
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

    public function getStartYear($course)
    {
        $year = date('Y', time());
        return $year - $course + 1;
    }

    public function convertGender($str)
    {
        if($str == 'женский') return 0;
        if($str == 'мужской') return 1;
    }

    public function convertEducationLang($str)
    {
        $list = [
            'Казахский' => 'kz',
            'Русский' => 'ru',
            'Английский' => 'en'
        ];

        return $list[$str] ?? null;
    }

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

    public function convertNationality($str)
    {
        $nationality = Nationality::where(DB::raw('LOWER(name_ru)'), mb_strtolower($str))->first();

        return $nationality->id ?? null;
    }

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

    public function convertBaseEducation($str)
    {
        $list = [
            'Среднее' => 'high_school',
            'Средне-профессиональное' => 'vocational_education',
            'Высшее' => 'bachelor'
        ];

        return $list[$str] ?? null;
    }
}
