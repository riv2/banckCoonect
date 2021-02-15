<?php

namespace App;

use App\Models\StudentDisciplineDay;
use App\Models\StudentDisciplineFile;
use App\ProfileDoc;
use App\Services\Auth;
use App\Services\{ImportMirasFull, LanguageService, SearchCache, StepByStep};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{DB,Log};
use Carbon\Carbon;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class Profiles
 * @package App
 *
 * @property int id
 * @property int user_id
 * @property string status
 * @property string education_status
 * @property string check_level
 * @property string category
 * @property string iin
 * @property string fio
 * @property int alien
 * @property string education_lang
 * @property int education_speciality_id
 * @property string education_study_form
 * @property int course Курс обучения
 * @property string team
 * @property int study_group_id
 * @property int is_transfer переведен из другого вуза
 * @property int elective_speciality_id
 * @property int semester_credits_limit _____Do not use it______. Use Auth::user()->semester_credits_limit
 * @property bool buying_allow
 * @property bool remote_exam_qr
 *
 * @property-read bool is_resident
 * @property-read string native_language
 * @property-read string second_language
 *
 * @property Speciality speciality
 * @property User user
 *
 * @property $studyGroupIdUpdated
 */
class Profiles extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    const STATUS_MODERATION = 'moderation';
    const STATUS_ACTIVE = 'active';
    const STATUS_BLOCK = 'block';

    // Form

    /** @var string Очная */
    const EDUCATION_STUDY_FORM_FULLTIME = 'fulltime';

    /** @var string Дистанционная (онлайн) */
    const EDUCATION_STUDY_FORM_ONLINE = 'online';

    /** @var string Вечерняя */
    const EDUCATION_STUDY_FORM_EVENING = 'evening';

    /** @var string Заочная */
    const EDUCATION_STUDY_FORM_EXTRAMURAL = 'extramural';

    // Status

    /** @var string Абитуриент */
    const EDUCATION_STATUS_MATRICULANT = 'matriculant';

    /** @var string Студент */
    const EDUCATION_STATUS_STUDENT = 'student';

    /** @var string Отчислен */
    const EDUCATION_STATUS_SEND_DOWN = 'send_down';

    /** @var string Академический отпуск */
    const EDUCATION_STATUS_ACADEMIC_LEAVE = 'academic_leave';

    /** @var string Выпускающийся */
    const EDUCATION_STATUS_PREGRADUATE = 'pregraduate';

    /** @var string Выпускник */
    const EDUCATION_STATUS_GRADUATE = 'graduate';

    /** @var string Временно отчистлен (отчисление резерв) */
    const EDUCATION_STATUS_TEMP_SUSPENDED = 'temp_suspended';

    // registration type
    const REGISTRATION_TYPE_CLIENT   = 'registration_step';
    const REGISTRATION_TYPE_AGITATOR = 'agitator_registration_step';

    const CATEGORY_STANDART = 'standart';
    const CATEGORY_STANDART_RECOUNT = 'standart_recount';
    const CATEGORY_TRANSIT = 'transit';
    const CATEGORY_TRAJECTORY_CHANGE = 'trajectory_change';
    const CATEGORY_MATRICULANT = 'matriculant';
    const CATEGORY_RETAKE_ENT = 'retake_ent';
    const CATEGORY_TRANSFER = 'transfer';

    const DOCS_STATUS_REJECT = 'reject';
    const DOCS_STATUS_ACCEPT = 'accept';
    const DOCS_STATUS_EDIT = 'change';

    const CHECK_LEVEL_INSPECTION = 'inspection';
    const CHECK_LEVEL_OR_CABINET = 'or_cabinet';

    const EDUCATION_LANG_RU = "ru";
    const EDUCATION_LANG_KZ = "kz";
    const EDUCATION_LANG_EN = "en";
    const EDUCATION_LANG_FR = "fr";
    const EDUCATION_LANG_AR = "ar";
    const EDUCATION_LANG_DE = "de";

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 0;

    // семейное положение
    const FAMILY_STATUS_SINGLE = 'single';
    const FAMILY_STATUS_MARITAL = 'marital';

    // номера учебных курсов
    const EDUCATION_COURSE_1 = 1;
    const EDUCATION_COURSE_2 = 2;
    const EDUCATION_COURSE_3 = 3;
    const EDUCATION_COURSE_4 = 4;
    const EDUCATION_COURSE_5 = 5;
    const EDUCATION_COURSE_6 = 6;

    // закончил школу с отличием/без
    const EDUCATION_WITH_HONORS_ACTIVE = 1;
    const EDUCATION_WITH_HONORS_INACTIVE = 0;

    // переведен с другова вуза
    const STUDENT_TRANSFER_ACTIVE = 1;
    const STUDENT_TRANSFER_INACTIVE = 0;


    // registration steps
    const REGISTRATION_STEP_USER_PROFILE_ID                  = 'userProfileID';              // step 1
    const REGISTRATION_STEP_USER_PROFILE_ID_MANUAL           = 'userProfileIDManual';        // step 1
    const REGISTRATION_STEP_EMAIL                            = 'profileEmail';               // step 2
    const REGISTRATION_STEP_FAMILY_STATUS                    = 'profileFamilyStatus';        // step off
    const REGISTRATION_STEP_REFERRAL_SOURCE                  = 'referralSource';             // step off
    const REGISTRATION_STEP_AFTER_ID                         = 'afterID';                    // step 4
    const REGISTRATION_STEP_SPECIALITY_SELECT                = 'specialitySelect';           // step 5
    const REGISTRATION_STEP_STUDENT_EDUCATION_LANGUAGE       = 'studentEducationLanguage';   // step 6
    const REGISTRATION_STEP_STUDENT_STUDY_FORM               = 'studyForm';                  // step 7
    const REGISTRATION_STEP_ADDRESS                          = 'address';                    // step 8
    const REGISTRATION_STEP_ENT                              = 'ent';                        // step 9
    const REGISTRATION_STEP_KT_CERTIFICATE                   = 'kt_certificate';             // step 9
    const REGISTRATION_STEP_PUBLICATIONS                     = 'publications';               // step 10
    const REGISTRATION_STEP_EDUCATION                        = 'education';                  // step 11
    const REGISTRATION_STEP_AGITATOR                         = 'profileAddAgitator';         // step 12
    const REGISTRATION_STEP_WITHOUT_AGITATOR                 = 'profileWithoutAgitator';     // step 12
    const REGISTRATION_STEP_PAYMENT                          = 'profileRegisterPayment';     // step 13
    const REGISTRATION_STEP_FINISH                           = 'profileRegisterFinish';      // finish
    const REGISTRATION_STEP_FINISH_STUDY                     = 'study';                      // finish to study
    const REGISTRATION_STEP_FINISH_OLD                       = 'finish';                     // finish to study
    const REGISTRATION_STEP_BC_APPLICATION_PART              = 'bcApplicationPart';          //
    const REGISTRATION_STEP_MG_APPLICATION_PART              = 'mgApplicationPart';          //
    const REGISTRATION_STEP_FAMILY_STATUS_POST               = 'profileFamilyStatusPost';
    const REGISTRATION_STEP_EMAIL_POST                       = 'profileEmailPost';

    // agitator registration steps
    const AGITATOR_REGISTRATION_STEP_USER_PROFILE_ID         = "agitatorRegisterProfileID";              // step 1
    const AGITATOR_REGISTRATION_STEP_USER_PROFILE_ID_MANUAL  = "agitatorRegisterProfileIdManual";        // step 1
    const AGITATOR_REGISTRATION_STEP_TERMS                   = "agitatorRegisterProfileTerms";           // step 2
    const AGITATOR_REGISTRATION_STEP_INPUT_IBAN              = "agitatorRegisterProfileIban";            // step 3
    const AGITATOR_REGISTRATION_STEP_FINISH                  = "agitatorRegisterProfileFinish";          // finish

    //task - https://tasks.hubstaff.com/app/organizations/16298/projects/130300/tasks/1071350
    const PLAN_ADMIN_CONFIRM_USER_ID                         = 1;
    const PLAN_ADMIN_CONFIRM                                 = 1;
    const PLAN_STUDENT_CONFIRM                               = 1;
    
    protected $table = 'profiles';

    //protected $fillable = ['id', 'expire_m', 'expire_y', 'owner_name', 'owner_last', 'card_name', 'user_id'];

    protected $dates = [
        'bdate',
        'issuedate',
        'expire_date'
    ];

    protected $casts = [
        'buying_allow' => 'boolean',
        'remote_exam_qr' => 'boolean',
    ];

    protected $fillable = [
        'user_id',
        'fio',
        'mobile',
        'iin',
        'bdate',
        'nationality_id',
        'sex',
        'family_status',
        'docnumber',
        'issuedate',
        'expire_date',
        'issuing',
        'education_lang',
        'ecudation_speciality_id',
        'front_id_photo',
        'back_id_photo'
    ];
    /*
    protected $fillable = [
        'family_status',
        'course',
        'team',
        'workplace',
        'previous_document',
        'with_honors',
        'date_certificate',
        'is_transfer',
        'transfer_course',
        'transfer_study_form',
        'transfer_specialty',
        'transfer_university',
    ];
    */

    protected $docs;

    protected $studyGroupIdUpdated = false;


    // register priority
    public static $register_priority = [

        self::REGISTRATION_STEP_USER_PROFILE_ID             => 100,
        self::REGISTRATION_STEP_USER_PROFILE_ID_MANUAL      => 200,
        self::REGISTRATION_STEP_EMAIL                       => 300,
        self::REGISTRATION_STEP_FAMILY_STATUS               => 400,
        self::REGISTRATION_STEP_REFERRAL_SOURCE             => 500,
        self::REGISTRATION_STEP_AFTER_ID                    => 600,
        self::REGISTRATION_STEP_SPECIALITY_SELECT           => 700,
        self::REGISTRATION_STEP_STUDENT_EDUCATION_LANGUAGE  => 800,
        self::REGISTRATION_STEP_STUDENT_STUDY_FORM          => 900,
        self::REGISTRATION_STEP_ADDRESS                     => 1000,
        self::REGISTRATION_STEP_ENT                         => 1100,
        self::REGISTRATION_STEP_KT_CERTIFICATE              => 1100,
        self::REGISTRATION_STEP_PUBLICATIONS                => 1200,
        self::REGISTRATION_STEP_EDUCATION                   => 1300,
        self::REGISTRATION_STEP_AGITATOR                    => 1400,
        self::REGISTRATION_STEP_PAYMENT                     => 1500,
        self::REGISTRATION_STEP_FINISH                      => 1600,
    ];

    public static $register_finish_list = [
        'profileAddAgitator',
        'profileRegisterPayment'
    ];

    // step by step keys
    public static $stepByStep = [
        'specialitySelect',
        'studentEducationLanguage',
        'studyForm',
        'bcApplication',
        'mgApplication',
        'study',
    ];
    // application steps
    public static $applicationStep = [
        'address',
        'ent',
        'kt_certificate',
        'publications',
        'education',
        'profileAddAgitator',
        'profileRegisterPayment',
    ];

    // agitator registration steps
    public static $applicationAgitatorStep = [
        'agitatorRegisterProfileID',
        'agitatorRegisterProfileIdManual',
        'agitatorRegisterProfileTerms',
        'agitatorRegisterProfileIban',
        'agitatorRegisterProfileFinish'
    ];

    // agitator register priority
    public static $agitator_register_priority = [
        self::AGITATOR_REGISTRATION_STEP_USER_PROFILE_ID            => 100,
        self::AGITATOR_REGISTRATION_STEP_USER_PROFILE_ID_MANUAL     => 200,
        self::AGITATOR_REGISTRATION_STEP_TERMS                      => 300,
        self::AGITATOR_REGISTRATION_STEP_INPUT_IBAN                 => 400,
        self::AGITATOR_REGISTRATION_STEP_FINISH                     => 500
    ];

    public static  $degree = [
        'Бакалавр',
        'Магистр'
    ];

    public static $list = [
        'kz' => 'Казахский',
        'ru' => 'Русский',
        'en' => 'Английский'
    ];

    public static $studyForms = [
        Profiles::EDUCATION_STUDY_FORM_FULLTIME     => 'Очная',
        Profiles::EDUCATION_STUDY_FORM_ONLINE       => 'Дистанционная (онлайн)',
        Profiles::EDUCATION_STUDY_FORM_EVENING      => 'Вечерняя',
        Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL   => 'Заочная',
    ];

    public static $categories = [
        Profiles::CATEGORY_MATRICULANT          => 'Абитуриент',
        Profiles::CATEGORY_STANDART             => 'Стандарт',
        Profiles::CATEGORY_STANDART_RECOUNT     => 'Стандарт (перезачеты)',
        Profiles::CATEGORY_TRAJECTORY_CHANGE    => 'Смена траектории',
        Profiles::CATEGORY_RETAKE_ENT           => 'Пересдача ЕНТ',
        Profiles::CATEGORY_TRANSIT              => 'Транзит',
        Profiles::CATEGORY_TRANSFER             => 'Переводник',
    ];

    public static $profilesRedisTable = 'profiles';

    public function getNativeLanguageAttribute() : string
    {
        return $this->education_lang;
    }

    public function getSecondLanguageAttribute() : string
    {
        return LanguageService::getSecond($this->education_lang);
    }

    public static function getRedisData()
    {
        return SearchCache::totalCount(self::$profilesRedisTable);
    }

    static function getProfileVisitsList(
        ?string $fio = '',
        ?string $groupFilter = null,
        int $start = 0,
        int $length = 10,
        int $orderColumn = 0,
        string $orderDirection = 'asc'
    )
    {
        $orderColumnName = 'id';

        $recordsTotal = SearchCache::totalCount(self::$profilesRedisTable);

        $query = self::select(['id', 'name'])
            ->with(['users', 'study_groups'])->orderBy($orderColumnName, $orderDirection);

        $recordsFiltered = 0;

        // Without filters
        if (empty($fio) && empty($groupFilter)) {
            $recordsFiltered = $recordsTotal;
        } else {
            // Full Code filter
            if (!empty($groupFilter)) {
                $query->where("name", $groupFilter);
            }

            // Search string $fio
            if (!empty($fio)) {
                // Get ids
                $idList = SearchCache::searchFull(self::$profilesRedisTable, $fio, 'fio');
                $query->whereIn('id', $idList);

                if (is_numeric($fio)) {
                    $query->orWhere('id', (int)$fio);
                }
            }

            $recordsFiltered = $query->count();
        }

        // Get result
        $filterResult = $query
            ->offset($start)
            ->take($length)
            ->get();

        $data = [];
        foreach ($filterResult as $row) {
            $data[] = [
                $row->id,
                $row->users->name,
                $row->year->study_groups->name,
            ];
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    /**
     * @param int $studyGroupId
     */
    public function setStudyGroupIdAttribute($studyGroupId): void
    {
        if ($this->study_group_id != $studyGroupId) {
            $this->studyGroupIdUpdated = true;
            $this->attributes['study_group_id'] = $studyGroupId;
        }
    }

    public function save(array $options = [])
    {
        $result = parent::save($options);

        // Saving groupId log if need
        if ($result && $this->studyGroupIdUpdated) {
            StudentGroupsSemesters::add($this->user_id, $this->study_group_id, Semester::current($this->education_study_form, $this->speciality->year));
            $this->studyGroupIdUpdated = false;
        }

        return $result;
    }

    /**
     * Array for selects
     * @return array
     */
    public static function getStudyFormsArray() : array
    {
        $array = [];

        foreach (self::getStudyFormsArrayFlat() as $studyForm) {
            $array[$studyForm] = __($studyForm);
        }

        return $array;
    }

    /**
     * Array for selects
     * @return array
     */
    public static function getStudyFormsArrayFlat() : array
    {
        return [
            self::EDUCATION_STUDY_FORM_FULLTIME,
            self::EDUCATION_STUDY_FORM_EVENING,
            self::EDUCATION_STUDY_FORM_ONLINE,
            self::EDUCATION_STUDY_FORM_EXTRAMURAL
        ];
    }

    /**
     * Array for selects
     * @return array
     */
    public static function getLangsArray() : array
    {
        return [
            self::EDUCATION_LANG_KZ => self::EDUCATION_LANG_KZ,
            self::EDUCATION_LANG_RU => self::EDUCATION_LANG_RU
        ];
    }

    /**
     * @param $sRegisterStep
     * @return mixed
     */
    public static function getRegisterPriority( $sRegisterStep )
    {

        $iResponse = 0;
        foreach( static::$register_priority as $key => $item )
        {
            if( strpos($sRegisterStep,$key) !== false )
            {
                $iResponse = $item;
                break;
            }
        }
        return $iResponse;

    }


    /**
     * @param $sRegisterStep
     * @return int|mixed
     */
    public static function getRegisterPriorityAgitator( $sRegisterStep )
    {
        $iResponse = 0;
        foreach( static::$agitator_register_priority as $key => $item )
        {
            if( strpos($sRegisterStep,$key) !== false )
            {
                $iResponse = $item;
                break;
            }
        }
        return $iResponse;
    }


    /**
     * @param $sRegisterStep
     * @return bool
     */
    public function isRedirectToRegisterStep( $sRegisterStep )
    {

        if( !empty( $this->registration_step ) && ( $this->registration_step != '' ) &&
            ( $this->registration_step != $sRegisterStep ) &&
            ( self::getRegisterPriority($sRegisterStep) < self::getRegisterPriority($this->registration_step) )
        )
        {
            return true;
        }
        return false;
    }


    /**
     * @param $sRegisterStep
     * @return bool
     */
    public function isRedirectToRegisterAgitatorStep( $sRegisterStep )
    {
        if( !empty( $this->agitator_registration_step ) && ( $this->agitator_registration_step != '' ) &&
            ( $this->agitator_registration_step != $sRegisterStep ) &&
            ( self::getRegisterPriorityAgitator($sRegisterStep) < self::getRegisterPriorityAgitator($this->agitator_registration_step) )
        )
        {
            return true;
        }
        return false;
    }


    /**
     * @param $sRegisterStep
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getRegisterRoute( $sRegisterStep )
    {

        $aData = explode('?',$sRegisterStep);
        $sRegisterStep = $aData[0] ?? $sRegisterStep;
        $param = $aData[1] ?? false;

        $application = '';
        $config = '';
        $codeChar = \App\Services\Auth::user()->studentProfile->speciality->code_char ?? '';

        if( $sRegisterStep == self::REGISTRATION_STEP_FINISH )
        {
            return redirect()->route( self::REGISTRATION_STEP_FINISH_STUDY);
        }

        if( in_array($sRegisterStep,static::$register_finish_list) !== false )
        {
            return redirect()->route( $sRegisterStep );
        }

        if( (!empty($codeChar) && ($codeChar == 'b') ) || !empty(\App\Services\Auth::user()->bcApplication) ||
            ( !empty($param) && ( $param == 'bachelor' ) )
        )
        {
            $application = 'bachelor';
            $config = 'bc_application';
        }
        if( (!empty($codeChar) && ($codeChar == 'm') ) || !empty(\App\Services\Auth::user()->mgApplication) ||
            ( !empty($param) && ( $param == 'master' ) )
        )
        {
            $application = 'master';
            $config = 'mg_application';
        }

        if( !empty($config) && (in_array($sRegisterStep,static::$stepByStep) !== false) )
        {
            return redirect()->route( $sRegisterStep, ['application' => $application]);
        }

        if( !empty($config) && ( $config == 'bc_application' ) && (in_array($sRegisterStep,static::$applicationStep) !== false) )
        {
            return redirect()->route( self::REGISTRATION_STEP_BC_APPLICATION_PART , ['part' => $sRegisterStep]);
        }

        if( !empty($config) && ( $config == 'mg_application' ) && (in_array($sRegisterStep,static::$applicationStep) !== false) )
        {
            return redirect()->route( self::REGISTRATION_STEP_MG_APPLICATION_PART , ['part' => $sRegisterStep]);
        }

        return redirect()->route( $sRegisterStep );

    }


    /**
     * @param $sRegisterStep
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getRegisterRouteAgitator( $sRegisterStep )
    {

        if( in_array($sRegisterStep,static::$applicationAgitatorStep) !== false )
        {
            return redirect()->route( $sRegisterStep );
        }

        return redirect()->route( 'home' );

    }


    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function speciality()
    {
        return $this->hasOne(Speciality::class, 'id', 'education_speciality_id');
    }

    public function originalSpeciality()
    {
        return $this->hasOne(Speciality::class, 'id', 'education_original_speciality_id');
    }

    public function nationalityItem()
    {
        return $this->hasOne(Nationality::class, 'id', 'nationality_id');
    }

    public function disciplines()
    {
        return $this->hasMany(StudentDiscipline::class, 'student_id', 'user_id')->with('discipline');
    }

    public function studentCheckins()
    {
        return $this->hasMany(StudentCheckin::class, 'student_id', 'user_id');
    }

    public function studentsDisciplines()
    {
        return $this->hasMany(StudentDiscipline::class, 'student_id', 'user_id');
    }

    public function getNationalityRuAttribute()
    {
        return $this->nationalityItem->name_ru ?? $this->attributes['nationality'];
    }

    public function getNationalityEnAttribute()
    {
        return $this->nationalityItem->name ?? $this->attributes['nationality'];
    }

    public function getNationalityKzAttribute()
    {
        return $this->nationalityItem->name_kz ?? $this->attributes['nationality'];
    }

    public function getIsResidentAttribute() : bool
    {
        return $this->alien == 0;
    }

    public function studyGroup()
    {
        return $this->hasOne(StudyGroup::class, 'id', 'study_group_id');
    }

    /**
     * @return bool
     */
    public function importFromMirasFull()
    {
        /*if(!$this->iin)
        {
            return false;
        }

        $user = Auth::user();

        $mirasFullPerson = $this->importProfileFull($user);
        if(!$mirasFullPerson)
        {
            return false;
        }

        $this->importDisciplineFullList($mirasFullPerson->id);
        $this->importDisciplineRateFull($mirasFullPerson->id);*/

        return false;
    }

    /**
     * @return bool|Model|null|object|static
     */
    private function importProfileFull()
    {
        $mirasFullPerson = DB::connection('miras_full')
            ->table('person')
            ->select([
                'person.id as id',
                'person.iin as iin',
                'person.last_name as last_name',
                'person.first_name as first_name',
                'person.middle_name as middle_name',
                'person.birth_date as birth_date',
                'person.gender as gender',
                'credential.type as doc_type',
                'credential.number as doc_number',
                'credential.serial as doc_serial',
                'credential.issue_date as doc_issue_date',
                'd_doc_issuers.name as doc_issue_name'
            ])
            ->leftJoin('student', 'student.person_id', '=', 'person.id')
            ->leftJoin('credential', 'credential.person_id', '=', 'person.id')
            ->leftJoin('d_doc_issuers', 'credential.issuer_id', '=', 'd_doc_issuers.id')
            ->where('person.iin', $this->iin)
            ->whereIn('student.status', [
                'STUDENT'/*,
                'STUDENT_CE'*/
            ])
            ->first();

        if(!$mirasFullPerson)
        {
            return false;
        }

        $mirasFullPhone = DB::connection('miras_full')
            ->table('contact')
            ->leftJoin('person_contact', 'person_contact.contact_id','=', 'contact.id')
            ->leftJoin('student', 'student.person_id', '=', 'person_contact.person_id')
            ->where('person_contact.person_id', $mirasFullPerson->id)
            ->whereIn('contact.type', ['PHONE', 'MOBILE'])
            ->whereIn('student.status', [
                'STUDENT'/*,
                'STUDENT_CE'*/
            ])
            ->orderBy('contact.id', 'desc')
            ->first();

        $this->fio      = $mirasFullPerson->last_name . ' ' . $mirasFullPerson->first_name . ' ' . $mirasFullPerson->middle_name;
        $this->bdate    = ImportMirasFull::transformDate($mirasFullPerson->birth_date);
        $this->sex      = ImportMirasFull::transformGender($mirasFullPerson->gender);
        $this->mobile   = $mirasFullPhone->value ?? null;
        $this->paid     = true;

        $this->pass      = ImportMirasFull::transformDocType($mirasFullPerson->doc_type);
        $this->docnumber = $mirasFullPerson->doc_number ?? '';
        $this->docseries = $mirasFullPerson->doc_serial ?? '';
        $this->issuing   = $mirasFullPerson->doc_issue_name ?? '';
        $this->issuedate = ImportMirasFull::transformDate($mirasFullPerson->doc_issue_date);
        $this->import_full = true;

        $this->user->name = $this->fio;
        $this->user->phone = $this->mobile;
        $this->user->save();
        $this->save();

        return $mirasFullPerson;
    }

    /**
     * @param $personFullId
     * @return bool
     */
    private function importDisciplineFullList($personFullId)
    {
        $disciplineList = $this->disciplines;
        $disciplineAllList = Discipline::get();
        $disciplineNameList = [];
        $disciplineNameAllList = [];

        foreach ($disciplineList as $item)
        {
            $disciplineNameList[] = $item->discipline->name;
        }

        foreach ($disciplineAllList as $item)
        {
            $disciplineNameAllList[] = $item->name;
        }

        $mirasFullDisciplineList = DB::connection('miras_full')
            ->table('student as s')
            ->select([
                'discipline.name as name'
            ])
            ->join('study_plan_v2 as spv', function($join){
                $join->on('spv.year', '=', 's.year');
                $join->on('spv.speciality_id', '=', 's.speciality_id');
                $join->on('spv.education_id', '=', 's.education_id');
                $join->on('spv.study_form_id', '=', 's.study_form_id');
            })
            ->join('study_plan_item_v2 as spiv',  'spiv.study_plan_id', '=', 'spv.id')
            ->join('study_plan_item_discipline_v2 as spidv', 'spidv.plan_item_id', '=', 'spiv.id')
            ->join('discipline', 'discipline.id', '=', 'spidv.discipline_id')

            ->where('s.person_id', $personFullId)
            ->whereNotIn('discipline.name', $disciplineNameList)
            ->whereIn('discipline.name', $disciplineNameAllList)
            ->groupBy(['discipline.id', 'discipline.name'])
            ->get();

        foreach ($mirasFullDisciplineList as $item)
        {
            $discipline = Discipline::where('name', $item->name)->first();
            if($discipline)
            {
                $studentDiscipline = new StudentDiscipline();
                $studentDiscipline->discipline_id = $discipline->id;
                $studentDiscipline->student_id = $this->user_id;
                $studentDiscipline->payed = 0;
                $studentDiscipline->save();
            }
        }

        return true;
    }

    /**
     * @param $personFullId
     * @return bool
     */
//    private function importDisciplineRateFull($personFullId)
//    {
//        $disciplineList = $this->disciplines()->get();
//        $disciplineNameList =[];
//        foreach ($disciplineList as $item)
//        {
//            $disciplineNameList[] = $item->discipline->name;
//        }
//
//        $mirasFullDisciplineRateList = DB::connection('miras_full')
//            ->table('grade_total')
//            ->leftJoin(
//                'student',
//                'student.id',
//                '=',
//                'grade_total.student_id')
//            ->leftJoin(
//                'study_plan_item_discipline_v2',
//                'grade_total.plan_item_discipline_id',
//                '=',
//                'study_plan_item_discipline_v2.id')
//            ->leftJoin(
//                'discipline',
//                'discipline.id',
//                '=',
//                'study_plan_item_discipline_v2.discipline_id')
//            ->where('student.person_id', $personFullId)
//            ->whereIn('discipline.name', $disciplineNameList)
//            ->get();
//
//        $disciplineCreditSum = $this->user->getDisciplineCreditSum();
//
//        foreach ($mirasFullDisciplineRateList as $item)
//        {
//            $studentDiscipline = StudentDiscipline
//                ::select(['students_disciplines.*'])
//                ->leftJoin('disciplines', 'students_disciplines.discipline_id', '=', 'disciplines.id')
//                ->where('disciplines.name', $item->name)
//                ->where('students_disciplines.student_id', $this->user_id)
//                ->first();
//
//            if($studentDiscipline) {
//                $quizeResult = new QuizResult();
//                $quizeResult->discipline_id = $studentDiscipline->discipline_id;
//                $quizeResult->student_discipline_id = $studentDiscipline->id;
//                $quizeResult->user_id = $this->user_id;
//                $quizeResult->value = $item->value;
//                $quizeResult->save();
//                $quizeResult->setValue($item->value);
//
//                $studentDiscipline->payed = true;
//                $studentDiscipline->setTestResult($item->value);
//            }
//        }
//
//        return true;
//    }

    /**
     * @return bool
     */
    public function needMilitary()
    {
        $age = Carbon::parse($this->bdate)->age;
        return $age >= 18 && $age <= 27 && $this->sex == 1 && $this->education_study_form == self::EDUCATION_STUDY_FORM_FULLTIME;
    }

    /**
     * @return bool
     */
    public function need063()
    {
        $age = Carbon::parse($this->bdate)->age;
        return $age < 23;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profileDocs()
    {
        return $this->hasMany(ProfileDoc::class, 'user_id', 'user_id');
    }

    /**
     * @param $type
     * @param $status
     * @return bool
     */
    public function updateStatusDoc($type, $status, $delivered)
    {
        $docModel = $this->profileDocs->where('doc_type', $type)->last();

        if (!$docModel)
        {
            return false;
        }

        $docModel->status = $status ?? $docModel->status;
        $docModel->delivered = $delivered ?? false;

        return $docModel->save();
    }

    /**
     * @param $type
     * @param $status
     * @return bool
     */
    public function updateStatusDocContracts($status, $delivered)
    {

        $oProfileDocs = $this->profileDocs->where('doc_type', ProfileDoc::TYPE_EDUCATION_CONTRACT);
        if( !empty($oProfileDocs) )
        {
            $i=0;
            foreach( $oProfileDocs as $one )
            {
                $one->status = !empty($status[$i]) ? $status[$i] : $one->status;
                $one->delivered = !empty($delivered[$i]) ? $delivered[$i] : false;
                $one->save();
                $i++;

            }
            return true;
        }

        return false;

    }

    /**
     * @return mixed
     */
    public function getFrontIdPhotoAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_FRONT_ID)->last();
    }

    public function getBackIdPhotoAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_BACK_ID)->last();
    }

    /**
     * @return mixed
     */
    public function getDiplomaPhotoAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_DIPLOMA)->last();
    }

    /**
     * @return mixed
     */
    public function getKtCertificateAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_KT_CERTIFICATE)->last();
    }

    /**
     * @return mixed
     */
    public function getEducationContractAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_EDUCATION_CONTRACT)->last();
    }

    /**
     * @return mixed
     */
    public function getEducationContractsAttribute()
    {

        $aResponse = [];

        /*
        $data = $this->profileDocs->
        where('doc_type', ProfileDoc::TYPE_EDUCATION_CONTRACT)->
        where('profile_docs.last',1)->
        orderBy('profile_docs.created_ad','desc');
        */

        $oData = ProfileDoc::
        where('user_id',$this->user_id)->
        where('doc_type',ProfileDoc::TYPE_EDUCATION_CONTRACT)->
        where('last',1)->
        orderBy('created_at','desc')->
        limit(5)->
        get();

        if( !empty($oData) )
        {
            $i=0;
            foreach( $oData as $one )
            {
                $aResponse[$i++] = $one;
            }
        }

        /*
        if( !empty($data) )
        {
            $i=0;
            foreach( $data as $one )
            {
                $aResponse[$i++] = $one;
            }
        }
        */

        return $aResponse;

    }

    /**
     * @return mixed
     */
    public function getEducationStatementAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_EDUCATION_STATEMENT)->last();
    }

    /**
     * @return mixed
     */
    public function getDocResidenceRegistrationAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_RESIDENCE_REGISTRATION)->last();
    }

    /**
     * @return mixed
     */
    public function getDocR086Attribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_R086)->last();
    }

    /**
     * @return mixed
     */
    public function getDocR086BackAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_R086_BACK)->last();
    }

    /**
     * @return mixed
     */
    public function getDocR063Attribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_R063)->last();
    }

    /**
     * @return mixed
     */
    public function getDocMilitaryAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_MILITARY)->last();
    }

    /**
     * @return mixed
     */
    public function getDocAtteducationAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_ATTEDUCATION)->last();
    }

    /**
     * @return mixed
     */
    public function getDocAtteducationBackAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_ATTEDUCATION_BACK)->last();
    }

    /**
     * @return mixed
     */
    public function getDocNostrificationAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_NOSTRIFICATION)->last();
    }

    public function getDocConConfirmAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_CON_CONFIRM)->last();
    }

    /**
     * @return mixed
     */
    public function getDocWorkBookAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_WORK_BOOK)->last();
    }

    /**
     * @return mixed
     */
    public function getDocFrontIdAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_FRONT_ID)->last();
    }

    /**
     * @return mixed
     */
    public function getDocBackIdAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_BACK_ID)->last();
    }

    /**
     * @return mixed
     */
    public function getDocEntAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_ENT_CERTIFICATE)->last();
    }

    /**
     * @return mixed
     */
    public function getDocKtAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_KT_CERTIFICATE)->last();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentDisciplineFiles()
    {
        return $this->hasMany(StudentDisciplineFile::class, 'discipline_id', 'id');
    }

    /**
     * @param int $count
     *
     * Return array as [speciality_id => disciplines_count]
     */
    public function equalSpecialities($count = 5)
    {
        $specialityList = [];
        $disciplineIdList = [];
        $specialityStatistic = [];

        $originalSpecialityId = $this->education_original_speciality_id ?? $this->education_speciality_id;

        $speciality = Speciality::where('id', $originalSpecialityId)->first();
        if( empty($speciality) || empty($speciality->disciplines) )
        {
            return [];
        }

        $specialityList = Speciality::where('id', '!=', $speciality->id)->get();

        foreach ($speciality->disciplines as $discipline)
        {
            $disciplineIdList[] = $discipline->id;
        }

        if( count($specialityList) > 0 )
        {
            foreach ($specialityList as $specialityEq)
            {
                $countModels = SpecialityDiscipline
                    ::where('speciality_id', $specialityEq->id)
                    ->whereIn('discipline_id', $disciplineIdList)
                    ->count();

                if($countModels)
                {
                    $specialityStatistic[$specialityEq->id] = $countModels;
                }
            }
        }

        arsort($specialityStatistic, SORT_NUMERIC);
        return array_slice($specialityStatistic, 0, $count, true);
    }

    public function studyFormNumber()
    {
        $result = 0;

        $result = $this->education_study_form == self::EDUCATION_STUDY_FORM_EVENING ? 1 : 0;
        $result = $this->education_study_form == self::EDUCATION_STUDY_FORM_ONLINE ? 2 : 0;
        $result = $this->education_study_form == self::EDUCATION_STUDY_FORM_FULLTIME ? 3 : 0;

        return $result;
    }

    /**
     * Speciality semester
     * @return int|null
     * @codeCoverageIgnore
     */
    public function currentSemester() : ?int
    {
        return Semester::inSpeciality($this->education_study_form, $this->speciality->year ?? date('Y'));
    }

    /**
     * @param bool $sDocType
     * @return object
     */
    public function getDocs($sDocType = false)
    {

        $oDocs = ProfileDoc::where('user_id', $this->user_id)->where('last', 1)->orderBy('created_at','desc');
        if( !empty($sDocType) )
        {
            $oDocs->where('doc_type',$sDocType);
        }
        $docs = $oDocs->get();

        $i = 0;
        $docList = [];
        foreach ($docs as $doc) {

            if( $doc->doc_type == ProfileDoc::TYPE_EDUCATION_CONTRACT )
            {
                if( $i < 5 )
                {
                    $aTempData = [];
                    $aTempData['filename'] = $doc->filename;
                    $aTempData['status'] = $doc->status;
                    $aTempData['path'] = $doc->getPathForDoc($doc->doc_type, $doc->filename);
                    $docList[$doc->doc_type][] = $aTempData;
                }
                $i++;
            } else {

                $docList[$doc->doc_type]['filename'] = $doc->filename;
                $docList[$doc->doc_type]['status'] = $doc->status;
                $docList[$doc->doc_type]['path'] = $doc->getPathForDoc($doc->doc_type, $doc->filename);
            }
        }

        $this->docs = (object) $docList;

        return $this->docs;

    }

    public static function getUserIdsBySpecialities(array $specialityIds)
    {
        return self::select('user_id')
            ->whereIn('education_speciality_id', $specialityIds)
            ->pluck('user_id')
            ->toArray();
    }

    public static function setElectiveSpecialityId(int $userId, int $electiveSpecialityId)
    {
        return self::where('user_id', $userId)->update(['elective_speciality_id' => $electiveSpecialityId]);
    }

    public function updateDisciplines()
    {
        $speciality = $this->speciality;

        if (!$speciality) {
            return false;
        }

        $disciplineIdList = [];

        foreach ($speciality->disciplines as $discipline) {
            $studentDiscipline = StudentDiscipline::getOneOrderByPayedCredits($this->user_id, $discipline->id);

            if (!$studentDiscipline) {
                //Check discipline in submodule
                $inSubmodule = (bool)SpecialitySubmodule
                    ::leftJoin('discipline_submodule', 'discipline_submodule.submodule_id', '=', 'speciality_submodule.submodule_id')
                    ->where('speciality_submodule.speciality_id', $speciality->id)
                    ->where('discipline_submodule.discipline_id', $discipline->id)
                    ->count();

                if (!$inSubmodule) {  
                    $studentDiscipline = new StudentDiscipline();
                    $studentDiscipline->archive = 0;
                    $studentDiscipline->discipline_id = $discipline->id;
                    $studentDiscipline->student_id = $this->user_id;
                    $studentDiscipline->payed = false;
                    $studentDiscipline->iteration = 0;
                    $studentDiscipline->is_elective = false;
					
                    if (self::PLAN_ADMIN_CONFIRM == 1) {
                        $studentDiscipline->plan_admin_confirm         = self::PLAN_ADMIN_CONFIRM;
                        $studentDiscipline->plan_admin_confirm_date    = date('Y-m-d h:i:s');
                        $studentDiscipline->plan_admin_confirm_user_id = self::PLAN_ADMIN_CONFIRM_USER_ID;                        
                    }
                    
                    if (self::PLAN_STUDENT_CONFIRM == 1) {
                       $studentDiscipline->plan_student_confirm = self::PLAN_STUDENT_CONFIRM;
                       $studentDiscipline->plan_student_confirm_date = date('Y-m-d h:i:s');                         
                    }
                     
                    if (self::PLAN_ADMIN_CONFIRM == 1 && self::PLAN_STUDENT_CONFIRM == 1) { 
                        $planSmester = 1; 
                        $specialityDiscipline = SpecialityDiscipline::getSemester($speciality->id, $discipline->id);
                        
                        dumpLog($specialityDiscipline);
                        
                        //todo
                        
                        $studentDiscipline->plan_semester = $planSmester;
                    }
					
                    $studentDiscipline->recommended_semester = SpecialityDiscipline::getSemester($speciality->id, $discipline->id);
                    $studentDiscipline->save();
                }
            } else {
                if ($studentDiscipline->is_elective) {
                    $studentDiscipline->is_elective = false;
                }

                $correctSemester = SpecialityDiscipline::getSemester($speciality->id, $discipline->id);

                if ($studentDiscipline->recommended_semester != $correctSemester) {
                    if (!$studentDiscipline->payed_credits) {
                        $studentDiscipline->recommended_semester = $correctSemester;
                    } else {
                        Log::info('Attach user disciplines. Can not change semester. payed_credits is not null.', ['student_discipline_id' => $studentDiscipline->id]);
                    }
                }

                $studentDiscipline->save();
            }

            $disciplineIdList[] = $studentDiscipline->id;
        }

        try {
            StudentDiscipline
                ::where('student_id', $this->user_id)
                ->whereNotIn('id', $disciplineIdList)
                ->where('is_elective', false)
                ->whereNull('submodule_id')
                ->delete();

            $this->updateSubmodules();
            $this->updateElectives();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        return true;
    }

    public function updateElectives()
    {
        $speciality = Speciality::with('disciplines')->where('id', $this->elective_speciality_id)->first();

        if (!$speciality) {
            return false;
        }

        $disciplineIdList = [];

        foreach ($speciality->disciplines as $discipline) {

            $studentDiscipline = StudentDiscipline::getOneOrderByPayedCredits($this->user_id, $discipline->id);

            if (!$studentDiscipline) {
                //Check discipline in submodule
                $inSubmodule = (bool)SpecialitySubmodule
                    ::leftJoin('discipline_submodule', 'discipline_submodule.submodule_id', '=', 'speciality_submodule.submodule_id')
                    ->where('speciality_submodule.speciality_id', $speciality->id)
                    ->where('discipline_submodule.discipline_id', $discipline->id)
                    ->count();

                $inDisciplines = (bool)StudentDiscipline
                    ::where('student_id', $this->user_id)
                    ->where('discipline_id', $discipline->id)
                    ->count();

                if (!$inSubmodule && !$inDisciplines) {
                    $studentDiscipline = new StudentDiscipline();
                    $studentDiscipline->discipline_id = $discipline->id;
                    $studentDiscipline->student_id = $this->user_id;
                    $studentDiscipline->payed = false;
                    $studentDiscipline->iteration = 0;
                    $studentDiscipline->is_elective = true;
                    $studentDiscipline->recommended_semester = SpecialityDiscipline::getSemester($speciality->id, $discipline->id);
                    $studentDiscipline->save();
                }
            }

            $disciplineIdList[] = $studentDiscipline->id;
        }

        StudentDiscipline
            ::where('student_id', $this->user_id)
            ->whereNotIn('id', $disciplineIdList)
            ->where('is_elective', true)
            ->whereNull('submodule_id')
            ->delete();

        return true;
    }

    public function updateSubmodules()
    {
        $speciality = $this->speciality;

        if(!$speciality)
        {
            return false;
        }

        $submoduleIds = [];

        foreach ($speciality->submodules as $submodule) {

            $hasStudentDisciplines = (bool)StudentDiscipline
                ::where('student_id', $this->user_id)
                ->where('submodule_id', $submodule->id)
                ->count();

            if(!$hasStudentDisciplines) {
                $submoduleIds[] = $submodule->id;

                $studentSubmodule = StudentSubmodule
                    ::where('submodule_id', $submodule->id)
                    ->where('student_id', $this->user_id)
                    ->first();

                // Link not exists
                if (empty($studentSubmodule)) {
                    $newLink = new StudentSubmodule();
                    $newLink->submodule_id = $submodule->id;
                    $newLink->student_id = $this->user_id;
                    $newLink->save();
                }
            }
        }

        // Delete outdated
        StudentSubmodule::where('student_id', $this->user_id)
            ->whereNotIn('submodule_id', $submoduleIds)
            ->delete();

        return true;
    }

    public static function changeEducationStatus(int $userId, string $status)
    {
        self::where('user_id', $userId)->update(['education_status' => $status]);

        SearchCache::updateField(User::$adminRedisMatriculantTable, $userId, 'status', $status);
    }

    public static function setNextCourse(int $userId) : int
    {
        return self::where('user_id', $userId)->increment('course');
    }

    /**
     * @return bool
     * @deprecated
     */
    public function isTest1Time() : bool
    {
        return Semester::isTest1Time($this->education_study_form);
    }

    /**
     * @return bool
     * @deprecated
     */
    public function isExamTime() : bool
    {
        return Semester::isExamTime($this->education_study_form);
    }

    public function getLanguageByType(string $languageType) : string
    {
        return LanguageService::getByType($languageType, $this->education_lang);
    }

    public static function getFioByUserId($userId) : string
    {
        $profile = self::select(['fio'])->where('user_id', $userId)->first();
        return $profile->fio ?? '';
    }

    public static function getAllTeam() : Collection
    {
        return StudyGroup::select('name as team')->get();
    }

    public function isSROTime() : bool
    {
       return
           $this->education_study_form == self::EDUCATION_STUDY_FORM_FULLTIME &&
           Semester::isSROTime($this->education_study_form);
    }

    public function allowRemoteExam() : void
    {
        $this->remote_exam_qr = true;
        $this->save();
    }

    public static function getUserIdsByGroupId(int $groupId) : array
    {
         return self::select('user_id')
            ->where('study_group_id', $groupId)
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * save avatar photo for agitator
     * @param $profileImgSource
     * @return bool
     */
    public function saveProfilePhoto( $profileImgSource )
    {

        if( !empty($profileImgSource) && !empty($this->faceimg) )
        {
            $filename = 'img_' . $this->id . '_' . time() . rand(1, 1000) . '.' . pathinfo($this->faceimg, PATHINFO_EXTENSION);
            $this->faceimg = $filename;
            file_put_contents(public_path('images/uploads/faces') . '/' . $filename, base64_decode($profileImgSource));

            if( filesize(public_path('images/uploads/faces') . '/' . $filename) > 10000000 )
            {
                unlink( public_path('images/uploads/faces') . '/' . $filename );
                return false;
            }

        }

        return true;

    }

    public function activity_logs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id', 'user_id');
    }

    public function studentDisciplineDays()
    {
        return $this->hasMany(StudentDisciplineDay::class, 'user_id', 'user_id');
    }

    public function getDisciplineDays($disciplineId, $teacherId, $semester = null)
    {
        $disciplineDays =  $this->studentDisciplineDays()
            ->where('discipline_id', $disciplineId)
            ->where('teacher_id', $teacherId);
        if (isset($semester)){
            $disciplineDays = $disciplineDays->where('semester', $semester);
        }
        return $disciplineDays->get();
    }
}
