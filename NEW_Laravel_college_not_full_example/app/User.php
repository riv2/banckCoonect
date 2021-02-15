<?php

namespace App;

use App\Role;
use App\Services\{Auth,Domain,SearchCache,Service1C};
use App\Teacher\ProfileTeacher;
use App\Chatter\Models\Ban;
use App\Profiles;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use App\CreditPrice;
use Illuminate\Support\Facades\{DB,Log,Redis};
use OwenIt\Auditing\Contracts\Auditable;
use App\{AgitatorRefunds,
    AgitatorUsers,
    Models\StudentDisciplineFile,
    News,
    ProfileDoc,
    Refund,
    UserBusiness,
    UserNews};

/**
 * Class User
 * @package App
 *
 * @property int id
 * @property string name
 * @property string email
 * @property string password
 * @property string phone
 * @property int status
 * @property string confirmation_code
 * @property int keycloak
 * @property mixed created_at
 * @property float balance
 * @property string register_fio ФИО того кто зарегистрировал юзера через админку
 *
 * @property-read string base_education Базовое образование
 * @property int admission_year Год поступления именно в этот универ. Это может быть переводник на старший курс.
 * @property-read int speciality_admission_year Год поступления на 1 курс специальности даного юзера
 * @property-read int study_year Курс обучения. 1, 2, 3 и т.д.
 * @property-read int semester_credits_limit
 * @property-read int exam_bought_min
 * @property-read bool exam_available_by_bought_min
 * @property-read int remote_access_price
 * @property-read bool distance_learning
 * @property-read bool free_remote_access
 * @property-read int migrationMaxFreeCredits
 * @property-read int migrationMaxNotFreeCredits
 * @property-read float gpa
 * @property-read string fio
 * @property-read string last_semester_in_study_plan
 * @property-read bool study_plan_admin_confirmed
 *
 * @property Profiles studentProfile
 * @property ProfileTeacher teacherProfile
 * @property MgApplications mgApplication
 * @property BcApplications bcApplication
 * @property Discipline[] disciplines
 * @property Discipline[] teacherDisciplines
 * @property StudyGroup[] teacherGroups
 * @property StudentDiscipline[] studentDisciplines
 */
class User extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use Notifiable;
    use SoftDeletes;

    const IMPORT_TYPE_GOS_TEST = 'gos_test';
    const IMPORT_TYPE_ENG_TEST = 'eng_test';

    const REFERAL_AGITATOR = 'At the invitation of the agitator';

    const RESET_PASSWORD_ACTIVE   = 1;
    const RESET_PASSWORD_INACTIVE = 0;
    const RECOVERY_EXCEPTION_USER_LIST = [16156, 11966, 9801, 15960, 9956, 16007, 13706];

    private $semesterDates = [];

    public static $adminRedisTable = 'admin_all_users';
    public static $adminRedisGuestTable = 'admin_guest_users';
    public static $adminRedisUsersPollTable = 'admin_poll_users';

    private static $adminAjaxColumnList = [
        'id',
        'image_icon',
        'name',
        'email',
        'phone'
    ];

    private static $adminAjaxColumnGuestList = [
        'id',
        'phone',
        'created_at',
        'name',
    ];

    public static $adminRedisMatriculantTable = 'admin_matriculants';

    private static $adminsMatriculantAjaxColumnList = [
        'id',
        'profiles.fio',
        'email',
        'speciality',
        'created_at',
        'status',
        'base_education',
        'education_form',
        'education_degree',
        'education_lang',
        'category',
        'check_level'
    ];

    private static $adminsMatriculantCacheColumnListFiltered = [
        'id',
        'payed',
        'fio',
        'mobile',
        'speciality',
        'created_at',
        'status',
        'base_education',
        'education_form',
        'education_degree',
        'education_lang',
        'category',
        'check_level',
        'deleted'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        //todo Убрать из проекта usertype. usertype заменен на роли
        'name', 'email', 'password', 'usertype', 'register_fio'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Remote access credit's price with user's discount
     * @return int
     */
    public function getRemoteAccessPriceAttribute() : int
    {
        return SpecialityPrice::getRemoteAccessPrice(
            $this->studentProfile->education_speciality_id,
            $this->studentProfile->education_study_form,
            $this->base_education,
            $this->studentProfile->is_resident
        );
    }

    /**
     * Базовое образование
     * @return string|null
     */
    public function getBaseEducationAttribute() : ?string
    {
        if (!empty($this->bcApplication->education)) {
            return $this->bcApplication->education;
        } elseif (!empty($this->mgApplication->education)) {
            return $this->mgApplication->education;
        } else {
            return null;
        }
    }

    /**
     * Год поступления именно в этот универ. Это может быть переводник на старший курс.
     * @return int|null
     * @throws \Exception
     */
    public function getAdmissionYearAttribute() : ?int
    {
        if (!empty($this->created_at)) {
            return (new DateTimeImmutable($this->created_at))->format('Y');
        }
        return null;
    }

    /**
     * @param $year
     */
    public function setAdmissionYearAttribute($year)
    {
        $day = date('d', strtotime($this->created_at));
        $month = date('m', strtotime($this->created_at));

        $this->created_at = $year . '-' . $month . '-' . $day;
    }

    /**
     * Год первого курса специальности.AgitatorRefunds
     * @return int|null
     * @codeCoverageIgnore
     */
    public function getSpecialityAdmissionYearAttribute() : ?int
    {
        return $this->studentProfile->speciality->year ?? null;
    }

    /**
     * @return int
     */
    public function getSemesterCreditsLimitAttribute() : int
    {
        if (!empty($this->studentProfile->semester_credits_limit)) {
            return $this->studentProfile->semester_credits_limit;
        }

        return SpecialityPrice::getSemesterCreditsLimit(
            $this->studentProfile->education_speciality_id,
            $this->studentProfile->education_study_form,
            $this->base_education
        );
    }

    /**
     * Курс. 1, 2, 3 и т.д.
     */
    public function getStudyYearAttribute(): int
    {
        return ceil(Semester::inSpeciality($this->studentProfile->education_study_form, $this->speciality_admission_year) / 2);
    }

    /**
     * Distance learning education form
     * @return bool
     */
    public function getDistanceLearningAttribute() : bool
    {
        return
            !empty($this->studentProfile->education_study_form) &&
            in_array($this->studentProfile->education_study_form, [
                Profiles::EDUCATION_STUDY_FORM_ONLINE,
                Profiles::EDUCATION_STUDY_FORM_EVENING,
                Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL
            ]);
    }

    /**
     * Free remote access
     * @return bool
     */
    public function getFreeRemoteAccessAttribute() : bool
    {
        return (
            !empty($this->studentProfile->education_study_form) &&
            $this->studentProfile->education_study_form == Profiles::EDUCATION_STUDY_FORM_ONLINE &&
            $this->speciality_admission_year >= 2019
        );
    }

    public function getMigrationMaxFreeCreditsAttribute() : int
    {
        if (!empty($this->bcApplication)) {
            // переведен из другого вуза
            if ($this->studentProfile->is_transfer == 1) {
                return -1;
            }
            // Не новый студент
            elseif ($this->admission_year < 2019) {
                return -1;
            }
            // средне специальное
            elseif (BcApplications::EDUCATION_VOCATIONAL_EDUCATION == $this->bcApplication->education) {
                return 60;
            }
            // высшее (Бакалавр)
            elseif (BcApplications::EDUCATION_BACHELOR == $this->bcApplication->education) {
                return 120;
            }
        }

        return 0;
    }

    public function getMigrationMaxNotFreeCreditsAttribute() : int
    {
        if (!empty($this->bcApplication)) {
            // переведен из другого вуза
            if ($this->studentProfile->is_transfer == 1) {
                return 0;
            }
            // средне специальное
            elseif (BcApplications::EDUCATION_VOCATIONAL_EDUCATION == $this->bcApplication->education) {
                return 60;
            }
            // высшее (Бакалавр)
            elseif (BcApplications::EDUCATION_BACHELOR == $this->bcApplication->education) {
                return 60;
            }
        }

        return 0;
    }

    /**
     * ФИО
     * @return string|null
     */
    public function getFioAttribute() : ?string
    {
        if (!empty($this->studentProfile->id)) {
            return $this->studentProfile->fio;
        } elseif (!empty($this->teacherProfile->id)) {
            return $this->teacherProfile->fio;
        } else {
            return $this->name;
        }
    }

    /**
     * Phone number
     * @return string|null
     */
    public function getPhoneNumberAttribute() : ?string
    {
        if (!empty($this->studentProfile->id)) {
            return $this->studentProfile->mobile;
        } elseif (!empty($this->teacherProfile->id)) {
            return $this->teacherProfile->mobile;
        } else {
            return $this->phone;
        }
    }

    /**
     * GPA
     * @return string|null
     */
    public function getGpaAttribute() : float
    {
        return StudentGpa::getActual($this->id);
    }

    /**
     * @return string|null
     */
    public function getLastSemesterInStudyPlanAttribute() : string
    {
        $lastSemester = StudentDiscipline::select(['plan_semester'])
            ->where('student_id', $this->id)
            ->whereNotNull('plan_semester')
            ->orderBy('plan_semester', 'desc')
            ->first();

        return $lastSemester->plan_semester ?? '';
    }

    /**
     * @return string|null
     */
    public function getStudyPlanAdminConfirmedAttribute() : bool
    {
        if (empty($this->studentProfile)) {
            return false;
        }

        $semester = Semester::current($this->studentProfile->education_study_form);

        $confirmedCount = StudentDiscipline::where('student_id', $this->id)
            ->where('plan_semester', $semester)
            ->where('plan_admin_confirm', true)
            ->count();

        $notConfirmedCount = StudentDiscipline::where('student_id', $this->id)
            ->where('plan_semester', $semester)
            ->where('plan_admin_confirm', false)
            ->count();

        return $confirmedCount && !$notConfirmedCount;
    }

    /**
     * @param string $search
     * @param int $start
     * @param int $length
     * @param int $orderColumn
     * @param string $orderDirection
     * @param array $userAdminDiscipline
     * @return array
     */
    static function getListForAdmin(?string $search = '', int $start = 0, int $length = 10, int $orderColumn = 0, string $orderDirection = 'asc')
    {
        $orderColumnName = self::$adminAjaxColumnList[$orderColumn] ?? 'id';

        $recordsTotal = SearchCache::totalCount(self::$adminRedisTable);

        $query = self::select(['id', 'image_icon', 'name', 'email', 'phone'])->orderBy($orderColumnName, $orderDirection);

        // Search string $search
        if (!empty($search)) {
            // Get ids
            $idList = SearchCache::searchFull(self::$adminRedisTable, $search);
            $query->whereIn('id', $idList);

            if (is_numeric($search)) {
                $query->orWhere('id', (int)$search);
            }

            $recordsFiltered = count($idList);
        } else {
            $recordsFiltered = $recordsTotal;
        }

        // Get result
        $filterResult = $query
            ->offset($start)
            ->take($length)
            ->get();

        $data = [];
        foreach ($filterResult as $user) {
            $data[] = [
                $user->id,
                $user->image_icon,
                $user->name,
                $user->email,
                $user->studentProfile ?  $user->studentProfile->mobile : $user->phone,
                ''
            ];
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    static function getGuestListForAdmin(?string $search = '', int $start = 0, int $length = 10, int $orderColumn = 0, string $orderDirection = 'asc')
    {
        $orderColumnName = self::$adminAjaxColumnGuestList[$orderColumn] ?? 'id';
        $recordsTotal = SearchCache::totalCount(self::$adminRedisGuestTable);

        $query = self::select(['users.id as id', 'profiles.fio as name', 'mobile as phone', 'users.created_at'])
            ->leftJoin('user_role', 'user_role.user_id', '=', 'users.id')
            ->leftJoin('roles', 'user_role.role_id', '=', 'roles.id')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->where('roles.name', 'guest')
            ->orderBy($orderColumnName, $orderDirection);

        // Search string $search
        if (!empty($search)) {
            // Get ids
            $idList = SearchCache::searchFull(self::$adminRedisGuestTable, $search);
            $query->whereIn('users.id', $idList);

            if (is_numeric($search)) {
                $query->orWhere('users.id', (int)$search);
            }

            $recordsFiltered = count($idList);
        } else {
            $recordsFiltered = $recordsTotal;
        }

        // Get result
        $filterResult = $query
            ->offset($start)
            ->take($length)
            ->get();

        $data = [];
        foreach ($filterResult as $user) {
            $data[] = [
                $user->id,
                $user->phone,
                date('d.m.Y', strtotime($user->created_at)),
                $user->name,
                ''
            ];
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function teacherProfile()
    {
        return $this->hasOne('App\Teacher\ProfileTeacher', 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function studentProfile()
    {
        return $this->hasOne('App\Profiles', 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function nobdUser()
    {
        return $this->hasOne('App\Models\NobdUser', 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function resume(){
        return $this->hasMany('App\EmployeesUsersResume', 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function isAgitator()
    {
        $oAgitatorUsers = $this->hasMany('App\AgitatorUsers', 'user_id', 'id');
        return (!empty($oAgitatorUsers) && (count($oAgitatorUsers) > 0)) ? true : false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function agitatorUsers()
    {
        return $this->hasMany(AgitatorUsers::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function agitatorRefunds()
    {
        return $this->hasMany(AgitatorRefunds::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentDisciplines()
    {
        return $this->hasMany(StudentDiscipline::class, 'student_id', 'id');
    }

    /**
     * @return int
     */
    public function agitatorFullBalance()
    {

        $iBalance = 0;
        $oAgitatorUsers = AgitatorUsers::
        where('user_id',$this->id)->
        where('status','=',AgitatorUsers::STATUS_OK)->
        whereNotNull('cost')->
        whereNull('deleted_at')->
        get();
        if( !empty($oAgitatorUsers) && (count($oAgitatorUsers) > 0) )
        {
            foreach($oAgitatorUsers as $itemAU)
            {
                $iBalance += $itemAU->cost;
            }
        }
        unset($oAgitatorUsers);
        return $iBalance;
    }

    /**
     * @return int
     */
    public function agitatorAvailableBalance()
    {

        $iAvailableFullBalance = 0;
        $iWithdrawnBalance     = 0;
        $iRes                  = 0;

        // получаем агитаторский баланс
        $oAgitatorUsers = AgitatorUsers::
        with('stud.studentProfile')->
        where('user_id',$this->id)->
        where('status','=',AgitatorUsers::STATUS_OK)->
        whereNotNull('cost')->
        whereNull('deleted_at')->
        get();
        if( !empty($oAgitatorUsers) && (count($oAgitatorUsers) > 0) )
        {
            foreach($oAgitatorUsers as $itemAU)
            {
                if( !empty($itemAU->stud->studentProfile) && ($itemAU->stud->studentProfile->docs_status == Profiles::DOCS_STATUS_ACCEPT) )
                {
                    $iAvailableFullBalance += $itemAU->cost;
                }
            }
        }

        // получаем сумму агитаторских выплат - что было выведено на карту
        /*
        $oAgitatorRefunds = AgitatorRefunds::
        whereIn('status',[
            AgitatorRefunds::STATUS_PROCESS,
            AgitatorRefunds::STATUS_SUCCESS
        ])->
        where('user_id',$this->id)->
        whereNull('deleted_at')->
        get();
        if( !empty($oAgitatorRefunds) && (count($oAgitatorRefunds) > 0) )
        {
            foreach($oAgitatorRefunds as $itemAR)
            {
                $iWithdrawnBalance += $itemAR->cost;
            }
        }
        if( $iAvailableFullBalance > $iWithdrawnBalance )
        {
            $iRes = intval($iAvailableFullBalance - $iWithdrawnBalance);
        }
        */
        if( $iAvailableFullBalance > 0)
        {
            $iRes = intval( $iAvailableFullBalance );
        }

        return $iRes;

    }

    /**
     * @return array
     */
    public function getAgitatorUserPayStatus()
    {

        $aResponse = [];

        // проверяем было ли списание с баланса студика
        $oAgitatorUsers =  AgitatorUsers::
        where('stud_id',$this->id)->
        whereNotNull('cost')->
        whereNull('deleted_at')->
        first();

        if( !empty($oAgitatorUsers) && ( in_array($oAgitatorUsers->status,[AgitatorUsers::STATUS_PROCESS,AgitatorUsers::STATUS_ERROR]) !== false ) )
        {
            $aResponse['message'] = __('Payment by check is not passed');
        }

        if( !empty($oAgitatorUsers) && ( in_array($oAgitatorUsers->status,[AgitatorUsers::STATUS_PAYED]) !== false ) )
        {
            $aResponse['message'] = __('Payout on card');
        }

        // проверяем доки на статус
        $oProfiles = Profiles::
        where('user_id',$this->id)->
        first();
        if( empty($oProfiles) || empty($oProfiles->docs_status) || ($oProfiles->docs_status != Profiles::DOCS_STATUS_ACCEPT) )
        {
            $aResponse['message'] = __('There are documents in the moderation queue');
        }

        // проверяем была ли ошибка
        if( empty($aResponse['message']) )
        {
            $aResponse['status'] = true;

        } else {
            $aResponse['status'] = false;
        }

        return $aResponse;

    }

    /**
     * вывод детализации при выводе агитатором
     * @param $iCost
     * @return array
     */
    public function getWithdrawInfo( $iCost )
    {

        $bIsAlien           = false;      // не резидент
        $bIsIp              = false;      // наличие ИП
        $iPercent           = 0;
        $iAmountPercent     = 0;
        $iAmountWithDraw    = 0;

        $aResponse          = [
            'alien'           => false,
            'ip'              => false,
            'percent'         => 0,
            'amount'          => $iCost,
            'amountWithdraw'  => 0,
            'amountPercent'   => 0
        ];

        if( !empty(Auth::user()->id) && !empty(Auth::user()->studentProfile) )
        {

            $bIsAlien = !empty(Auth::user()->studentProfile->alien) ? true : false;

            $oUserBusiness = UserBusiness::
            where('user_id',Auth::user()->id)->
            whereNull('deleted_at')->
            first();

            if( !empty($oUserBusiness) && !empty($oUserBusiness->name) )
            {
                $bIsIp = true;
            }

            // если Юр лицо, то % 0
            if( $bIsIp )
            {
                $aResponse['ip']             = true;
                $aResponse['alien']          = false;
                $aResponse['amount']         = $iCost;
                $aResponse['amountPercent']  = 0;
                $aResponse['amountWithdraw'] = 0;
            } else {

                $aResponse['ip'] = false;

                if( $bIsAlien )
                {
                    // если не резидент
                    $aResponse['alien'] = true;
                    $iPercent           = 20;
                    $iAmountPercent     = intval( ($iCost * $iPercent) / 100);
                    $iAmountWithDraw    = intval( $iCost - $iAmountPercent );

                } else {
                    // если резидент
                    $aResponse['alien'] = false;
                    $iPercent           = 21;
                    $iAmountPercent     = intval( ($iCost * $iPercent) / 100);
                    $iAmountWithDraw    = intval( $iCost - $iAmountPercent );

                }

                $aResponse['ip']             = false;
                $aResponse['amount']         = $iCost;
                $aResponse['amountPercent']  = $iAmountPercent;
                $aResponse['amountWithdraw'] = $iAmountWithDraw;

            }

        }

        return $aResponse;

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payCards(){
        return $this->hasMany(PayCard::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function employeeUser()
    {
        return $this->hasOne('App\EmployeesUser', 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function positions()
    {
        return $this->hasMany('App\EmployeesUsersPosition', 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function decree()
    {
        return $this->hasMany('App\EmployeesUsersDecree', 'user_id', 'id');
    }

    public function debtTrusts()
    {
        return $this->hasMany(DebtTrust::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function educationDocumentList()
    {
        return $this->hasMany('App\UserEducationDocument', 'user_id', 'id');
    }

    public function adminComments()
    {
        return $this->hasMany(AdminStudentComment::class, 'user_id', 'id');
    }

    public function gpaList()
    {
        return $this->hasMany(StudentGpa::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'user_id', 'id');
    }

    public function teacherStudyGroups()
    {
        return $this->belongsToMany(
            StudyGroup::class,
            'study_group_teacher',
            'user_id',
            'study_group_id')->withPivot(
                'user_id',
                'study_group_id',
                'discipline_id',
                'date_from',
                'date_to');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function educationDocumentFirst()
    {
        return $this->educationDocumentList->first();
    }

    /**
     * @return bool
     */
    public function hasEducationDocument()
    {
        return (bool)UserEducationDocument::where('user_id', $this->id)->count();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id');
    }

    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class, 'students_disciplines', 'student_id', 'discipline_id');
    }

    public function submodules()
    {
        return $this->belongsToMany(Submodule::class, 'student_submodule', 'student_id', 'submodule_id');
    }

    public function studentCheckins()
    {
        return $this->hasMany(StudentCheckin::class, 'student_id', 'id');
    }

    public function languageEnglishLevels()
    {
        return $this->belongsToMany(LanguageLevel::class, 'student_language_level')
            ->where('language', Language::LANGUAGE_EN)
            ->withPivot(['deleted_at'])
            ->whereNull('student_language_level.deleted_at');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teacherDisciplines()
    {
        return $this->belongsToMany(Discipline::class, 'admin_user_discipline', 'user_id', 'discipline_id')
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teacherGroups()
    {
        return $this->belongsToMany(StudyGroup::class, 'study_group_teacher');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quizeResultKges()
    {
        return $this->hasMany(QuizeResultKge::class, 'user_id', 'id');
    }

    /**
     * @return |null
     */
    public function getQuizeResultKgeAttribute()
    {
        return $this->quizeResultKges[0] ?? null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quizResults()
    {
        return $this->hasMany(QuizResult::class, 'user_id', 'id');
    }

    public function practiceStudentDocuments()
    {
        return $this->hasMany(StudentPracticeDocuments::class);
    }

    public function practiceStudentFiles()
    {
        return $this->hasMany(StudentPracticeFiles::class);
    }

    public function disciplineStudentFiles()
    {
        return $this->hasMany(StudentDisciplineFile::class);
    }

    public function disciplineStudentFilesByDiscipline($disciplineId)
    {
        return $this->disciplineStudentFiles->where('discipline_id', $disciplineId);
    }

    public function practiceStudentFilesByDiscipline($disciplineId)
    {
        return $this->practiceStudentFiles->where('discipline_id', $disciplineId);
    }

    /**
     * @return mixed
     */
    public function getAdminDisciplineIdListAttribute()
    {
        if(!isset($this->attributes['admin_discipline_id_list']))
        {
            $this->attributes['admin_discipline_id_list'] = [];
            $relations = $this->teacherDisciplines;

            foreach ($relations as $relation)
            {
                $this->attributes['admin_discipline_id_list'][] = $relation->id;
            }
        }
        return $this->attributes['admin_discipline_id_list'];
    }

    /**
     * @return null
     */
    public function getLanguageEnglishLevelAttribute()
    {
        if(isset($this->languageEnglishLevels[0]))
        {
            return $this->languageEnglishLevels[0];
        }

        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function forumBanned()
    {
        return $this->hasMany(Ban::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function helpRequests()
    {
        return $this->hasMany(HelpRequest::class, 'user_id', 'id');
    }

    /**
     * @param $roleName
     * @return bool
     */
    public function hasRole($roleName)
    {
        $result = (bool)$this->roles()->where('name', $roleName)->count();

        if (!$result) {
            $positionRoles = $this->positions()->with('roles')->get()->pluck('roles')->toArray();
            foreach ($positionRoles as $positionRole) {
                foreach ($positionRole as $role) {
                    if ($roleName == $role['name']) {
                        $result = true;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return boolf
     */
    public function hasTeacherRole()
    {
        return $this->hasRole(Role::NAME_TEACHER);
    }

    /**
     * @return boolf
     */
    public function hasTeacherMirasRole()
    {
        return $this->hasRole(Role::NAME_TEACHER_MIRAS);
    }

    /**
     * @return bool
     */
    public function hasClientRole()
    {
        return $this->hasRole(Role::NAME_CLIENT);
    }

    /**
     * @return bool
     */
    public function hasAgitatorRole()
    {
        return $this->hasRole(Role::NAME_AGITATOR);
    }

    /**
     * @return bool
     */
    public function hasAdminRole()
    {
        return $this->hasRole(Role::NAME_ADMIN);
    }

    /**
     * @return bool
     */
    public function hasListenerCourseRole()
    {
        return $this->hasRole(Role::NAME_LISTENER_COURSE);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wifi()
    {
        return $this->hasMany(Wifi::class, 'user_id', 'id');
    }

    /**
     * @param $projectSection
     * @param $actionType
     * @return bool
     */
    public function hasRight($projectSection, $actionType)
    {
        if($this->hasAdminRole())
        {
            return true;
        }

        if($actionType == 'set_pay_in_orcabinet')
        {
            return (bool)$this->roles()->where('can_set_pay_in_orcabinet', true)->count();
        }

        if($actionType == 'upload_student_docs')
        {
            return (bool)$this->roles()->where('can_upload_student_docs', true)->count();
        }

        if($actionType == 'create_student_comment')
        {
            return (bool)$this->roles()->where('can_create_student_comment', true)->count();
        }

        if($actionType == 'add_aditional_service_to_user')
        {
            return (bool)$this->roles()->where('can_add_aditional_service_to_user', true)->count();
        }

        $result = (bool)$this->roles()
            ->whereHas('rights', function($query) use ($projectSection, $actionType){
                $query->whereHas('projectSection', function($query) use($projectSection) {
                    $query->where('url', $projectSection);
                });

                if(is_array($actionType)) {
                    $query->where(function ($query1) use ($actionType) {
                        foreach ($actionType as $item) {
                            $query1->orWhere('can_' . $item, true);
                        }
                    });
                }
                else
                {
                    $query->where('can_' . $actionType, true);
                }
            })->count();

        if($projectSection == 'themes' || ($projectSection == 'disciplines' && $actionType == 'read'))
        {
            $result = $result || (bool)count($this->teacherDisciplines);
        }

        if(!$result){
            $positionRoles = $this->positions()->with('roles')->get()->pluck('roles');
            foreach ($positionRoles as $positionRole) {
                foreach ($positionRole as $role) {
                    if(!empty($role)){
                        $result = (bool)Role::where('id', $role->id)->whereHas('rights', function($query) use ($projectSection, $actionType){
                            $query->whereHas('projectSection', function($query) use($projectSection) {
                                $query->where('url', $projectSection);
                            });

                            if(is_array($actionType)) {
                                $query->where(function ($query1) use ($actionType) {
                                    foreach ($actionType as $item) {
                                        $query1->orWhere('can_' . $item, true);
                                    }
                                });
                            }
                            else
                            {
                                $query->where('can_' . $actionType, true);
                            }
                        })->count();
                    }
                }
            }
        }

        return $result;
    }

    public function hasAccess($cabinetName)
    {
        if($this->hasAdminRole())
        {
            return true;
        }

        $result = (bool)$this->roles()
            ->whereHas('rights', function($query) use ($cabinetName){
                $query->whereHas('projectSection', function($query) use($cabinetName) {
                    $query->where('project', $cabinetName);
                });
                $query->where(function($query1){
                    $query1->where('can_read', true);
                    $query1->orWhere('can_create', true);
                    $query1->orWhere('can_edit', true);
                    $query1->orWhere('can_delete', true);
                });
            })->count();

        if($cabinetName == 'admin') {
            $result = $result || (bool)count($this->teacherDisciplines);
        }

        return $result;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bcApplication()
    {
        return $this->hasOne('App\BcApplications', 'user_id', 'id');
    }

    public function mgApplication()
    {
        return $this->hasOne('App\MgApplications', 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function promotions()
    {
        return $this
            ->belongsToMany(Promotion::class, 'promotion_user', 'user_id', 'promotion_id')
            ->withPivot(['status']);
    }

    /**
     * @param $promotionName
     * @return bool
     */
    public function getPromotionStatus($promotionName)
    {
        $relation = $this->promotions->where('name', $promotionName)->first();
        return $relation->pivot->status ?? false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function entranceTests()
    {
        return $this->belongsToMany(
            EntranceTest::class,
            'student_entrance_test',
            'user_id',
            'entrance_test_id')
            ->withTimestamps();
    }

    public function financeOperations()
    {
        return $this->hasMany(FinanceOperation::class);
    }

    /**
     * @param $cost
     * @param $nomenclature
     */
    public function changeBalance($cost, $nomenclature)
    {
        $this->financeOperations()
            ->create([
                'cost' => $cost - (-1 * $this->balance),
                'finance_nomenclature_id' => $nomenclature->id,
                'balance' => -1 * $cost
            ]);

        $this->balance = -1 * $cost;
        $this->save();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    static function getStudentListForAdmin($search = '', $start=0, $length=10, $orderColumn=0, $orderDir='asc')
    {
        $redisTable = 'admin_users';

        $query = self::with('studentProfile');
        $recordsTotal = SearchCache::totalCount($redisTable);
        $columnList = [
            0 => 'id',
            1 => 'name',
            2 => 'email'
        ];
        $orderColumnName = $columnList[$orderColumn] ?? 'id';

        $query
            ->whereHas('studentProfile')
            ->orderBy($orderColumnName, $orderDir);

        if($search)
        {
            $idList = SearchCache::search($redisTable, $search);
            $query->whereIn('id', $idList);

            if(is_numeric($search))
            {
                $query->orWhere('id', (int)$search);
            }

            $recordsFiltered = count($idList);
        }
        else
        {
            $recordsFiltered = $recordsTotal;
        }

        $filterResult = $query->offset($start)->take($length)->get();
        $data = [];

        foreach ($filterResult as $student)
        {
            $control = '';
            if(Auth::user()->hasRight('students','edit'))
            {
                $control = '<a class="btn btn-default" href="' . route('adminStudentEdit', ['id' => $student->id]) . '"><i class="md md-edit"></i></a>';
            }

            if(Auth::user()->hasRight('students','delete'))
            {
                $control .= '<button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            <li><a href="' . route('adminStudentDelete', ['id' => $student->id]) . '"><i class="md md-delete"></i> Удалить</a></li>
                                        </ul>';
            }

            $status = '';

            if($student->studentProfile->status == Profiles::STATUS_ACTIVE)
            {
                $status = __("Verified");
            }

            if($student->studentProfile->status == Profiles::STATUS_BLOCK)
            {
                $status = __("Blocked");
            }

            if($student->studentProfile->status == Profiles::STATUS_MODERATION)
            {
                $status = __("Not Verified");
            }

            $data[] = [
                0 => $student->id,
                1 => $student->name,
                2 => $student->email,
                3 => $student->studentProfile->mobile,
                4 => $status,
                5 => $control
            ];
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    static function getMatriculantListForAdmin(
        $checkLevel,
        $search,
        $searchParams = [],
        $start = 0,
        $length = 10,
        $orderColumn = 0,
        $orderDir = 'asc'
    )
    {
        $searchParams[12] = $checkLevel;

        if ($searchParams[13] == '1'){
            $levelIds = array_merge (Redis::smembers('list:'. Profiles::CHECK_LEVEL_OR_CABINET), Redis::smembers('list:'. Profiles::CHECK_LEVEL_INSPECTION));
        }else{
            $levelIds = Redis::smembers('list:'. $checkLevel);
        }
        $recordsTotal = count($levelIds);
        $orderColumnName = self::$adminsMatriculantAjaxColumnList[$orderColumn] ?? 'id';

        $query = self::with('studentProfile')
            ->with('mgApplication')
            ->with('bcApplication')
//            ->whereHas('studentProfile')
            ->orderBy($orderColumnName, $orderDir);

        if (!empty($search)) {
            $searchIds = SearchCache::search(self::$adminRedisMatriculantTable, $search);
            $userIds = array_intersect($levelIds, $searchIds);
        } else {
            $userIds = $levelIds;
        }


        foreach ($searchParams as $fieldNum => $val) {

            $filteredIds = SearchCache::search(
                self::$adminRedisMatriculantTable,
                $val,
                self::$adminsMatriculantCacheColumnListFiltered[$fieldNum],
                self::$adminsMatriculantCacheColumnListFiltered[$fieldNum] == 'category');

            $userIds = array_intersect($userIds, $filteredIds);
        }

        $recordsFiltered = count($userIds);

        if( $searchParams[13] == '1'){
            $query = $query->onlyTrashed();
        }

        /*if($checkLevel == 'or_cabinet') {
            $query->whereHas('studentProfile', function($q){
                $q->where('education_status', \App\Profiles::EDUCATION_STATUS_STUDENT);
            });
            $recordsTotal = $recordsFiltered;
        }*/

        $users = $query->whereIn('id', $userIds)
            ->limit($length)
            ->offset($start)
            ->get();

        //dd($users);

        $data = [];

        $editRules = Auth::user()->hasRight('students', 'edit');
        $deleteRules = Auth::user()->hasRight('students', 'delete');
        $setPayInOrcabinetRules = Auth::user()->hasRight('', 'set_pay_in_orcabinet');

        foreach ($users as $user) {
            /** @var self $user */

            $control = '';
            if ($editRules) {
                $control = '<a class="btn btn-default" href="' . route('adminStudentEdit', ['id' => $user->id]) . '"><i class="md md-edit"></i></a>';
            }

            if ($deleteRules) {
                $control .= '<button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            <li><a href="' . route('adminStudentDelete', ['id' => $user->id]) . '"><i class="md md-delete"></i> Удалить</a></li>
                                        </ul>';
            }

            $application = null;
            $degree = '';

            if (isset($user->studentProfile->speciality->code_char) && $user->studentProfile->speciality->code_char == 'b') {
                $application = $user->bcApplication ?? null;
                $degree = 'Бакалавр';
            }

            if (isset($user->studentProfile->speciality->code_char) && $user->studentProfile->speciality->code_char == 'm') {
                $application = $user->mgApplication ?? null;
                $degree = 'Магистр';
            }

            $btnClass = 'btn-default';

            if (!empty($user->studentProfile->docs_status)) {
                if ($user->studentProfile->docs_status == 'reject') {
                    $btnClass = 'btn-danger';
                } elseif ($user->studentProfile->docs_status == 'accept') {
                    $btnClass = 'btn-success';
                } elseif ($user->studentProfile->docs_status == 'change') {
                    $btnClass = 'btn-warning';
                }
            }



            $dataParams[0] = $user->id;

            if($checkLevel == 'or_cabinet') {
                $invis = '';

                if(!$setPayInOrcabinetRules)
                {
                    $invis = 'disabled';
                }
                $dataParams[1] =  '<div style="width:100%; text-align: center"><input value="' . $user->id . '" '. $invis .' name="selectUserPayed" type="checkbox" ' . ($user->studentProfile->buying_allow ? 'checked' : '') . ' onchange="setBuying(this);" /></div>';
            } elseif ($checkLevel == 'inspection') {
                $dataParams[1] = '';
            } else {
                $dataParams[1] = '';
            }

            $data[] = array_merge($dataParams, [
                $user->studentProfile->fio ?? '',
                $user->studentProfile->mobile ?? '',
                $user->studentProfile->speciality->name ?? '',
                date('Y', strtotime($user->created_at)),
                __($user->studentProfile->education_status ?? ''),
                __($application->education ?? ''),
                __($user->studentProfile->education_study_form ?? ''),
                $degree,
                $user->studentProfile->education_lang ?? '',
                 __($user->studentProfile->category ?? ''),
                '<a class="btn ' . $btnClass . '" href="' . route('adminStudentEdit', ['id' => $user->id]) . '">Список документов</a>',
                '<div style="width:100%; text-align: center"><input name="selectUserList" value="' . $user->id . '" type="checkbox" /></div>'
            ]);
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    static function getStudentForAdmin($id) : ?self
    {
        return self::with('studentProfile')
            ->with('bcApplication')
            ->with(['mgApplication' => function($query){
                $query->with('publications');
            }])
            ->with('nobdUser')
            ->with(['disciplines' => function($query){
                $query->withPivot([
                    'test_result',
                    'test_result_points',
                    'test_result_letter',
                    'test1_result',
                    'test1_result_points',
                    'test1_result_letter',
                    'task_result',
                    'task_result_points',
                    'task_result_letter',
                    'migrated',
                    'payed',
                    'payed_credits',
                    'free_credits',
                    'final_result',
                    'final_result_letter',
                    'recommended_semester',
                    'at_semester',
                    'plan_semester'
                ]);
            }])
            ->with(['quizeResultKges' => function($query){
                $query->orderBy('id', 'desc');
            }])
            ->where('id', $id)
            ->whereHas('studentProfile')
            ->first();
    }

    /**
     * @param $promotion
     * @return bool
     */
    public function getPromotionWork($promotion)
    {
        $relation = PromotionUserWork
            ::select([
                'promotion_user_work.id as id',
                'promotion_user.discount as discount',
                'promotion_user.status as status'
            ])
            ->leftJoin('promotion_user', 'promotion_user.id', '=', 'promotion_user_work.promotion_user_id')
            ->where('promotion_user.user_id', $this->id);

        if(is_int($promotion))
        {
            $relation->where('promotion_user.promotion_id', $promotion);
        }
        if(is_string($promotion))
        {
            $relation->leftJoin('promotions', 'promotions.id', '=', 'promotion_user.promotion_id');
            $relation->where('promotions.name', $promotion);
        }

        $relation = $relation->first();

        return $relation ?? false;
    }

    /**
     * @param $roleName
     * @return bool
     */
    public function setRole($roleName)
    {
        if($this->hasRole($roleName))
        {
            return true;
        }

        $role = Role::select(['id'])->where('name', $roleName)->first();

        if(!$role)
        {
            return false;
        }

        return UserRole::insert([
            'user_id'       => $this->id,
            'role_id'       => $role->id,
            'created_at'    => DB::raw('now()'),
            'updated_at'    => DB::raw('now()')
        ]);
    }

    /**
     * @param $roleName
     * @return bool
     */
    public function unsetRole($roleName)
    {
        if(!$this->hasRole($roleName))
        {
            return true;
        }

        return UserRole::
            leftJoin('roles', 'roles.id', '=', 'user_role.role_id')->
            where('roles.name', $roleName)->
            where('user_role.user_id', $this->id)->
            delete();
    }

    /**
     * @return string
     */
    static function getCurrentRole()
    {
        $subdomain = Domain::getSubdomain();

        if( !empty(Auth::user()) && Auth::user()->hasListenerCourseRole() )
        {
            return Role::NAME_LISTENER_COURSE;
        }

        if($subdomain == 's')
        {
            return Role::NAME_TEACHER_MIRAS;
        }

        if($subdomain !== 't')
        {
            return Role::NAME_CLIENT;
        }

        if($subdomain == 't')
        {
            return Role::NAME_TEACHER;
        }

        if($subdomain == 'admin')
        {
            return Role::NAME_ADMIN;
        }

        return false;
    }

    /**
     * @param null $role
     * @return mixed
     */
    public function getCallbackPhone($role = null)
    {
        $phone = $this->phone;

        if(!$role) {
            return $phone;
        }

        if( $role == Role::NAME_CLIENT )
        {
            $phone = $this->studentProfile->mobile ?? $this->phone;
        }

        if( $role == Role::NAME_TEACHER )
        {
            $phone = $this->teacherProfile->mobile ?? $this->phone;
        }

        return $phone;
    }

    /**
     * @return string
     */
    public function getIin()
    {
        $iin = '';

        if( isset($this->studentProfile->iin) )
        {
            $iin = $this->studentProfile->iin;
        }

        if( isset($this->teacherProfile->iin) )
        {
            $iin = $this->teacherProfile->iin;
        }

        return $iin;
    }

    /**
     * @return string
     */
    public function getBdate()
    {
        $bdate = '';

        if( isset($this->studentProfile->bdate) )
        {
            $bdate = $this->studentProfile->bdate;
        }

        if( isset($this->teacherProfile->bdate) )
        {
            $bdate = $this->teacherProfile->bdate;
        }

        return $bdate;
    }

    /**
     * @param $discipline
     * @return mixed
     */
//    public function disciplineCostWithPromotion($discipline)
//    {
//        $disciplineCost = $discipline->getAmount(Auth::user()->credit_price);
//        $promotion = $this->getPromotionWork('working_student');
//
//        if(isset($promotion->discount) && $promotion->status == PromotionUser::STATUS_ACTIVE)
//        {
//            return $disciplineCost - ($promotion->discount * $disciplineCost / 100);
//        }
//
//        return $disciplineCost;
//    }

    /**
     * SUM of all student's disciplines credits
     * @return int
     */
    public function getDisciplineCreditSum() : int
    {
        $studentDiscipline = StudentDiscipline::select(DB::raw('SUM(`disciplines`.`ects`) AS `credits_sum`'))
            ->join('disciplines', 'disciplines.id', '=', 'students_disciplines.discipline_id')
            ->where('students_disciplines.student_id', $this->id)
            ->first();

        return $studentDiscipline->credits_sum ?? 0;
    }

    public function getDisciplineGpaSum() : float
    {
        $studentDiscipline = StudentDiscipline
            ::select(DB::raw('SUM(`final_result_gpa`) AS final_result_gpa_sum'))
            ->where('student_id', $this->id)
            ->first();

        return $studentDiscipline->final_result_gpa_sum ?? 0.0;
    }

    public function updateGpa() : void
    {
        $this->gpaList()->delete();

        $creditSum = StudentDiscipline::getFinishedDisciplinesCreditsSum($this->id);

        if (!$creditSum) {
            return ;
        }

        $disciplineGpaSum = $this->getDisciplineGpaSum();

        $studentGpa = new StudentGpa();
        $studentGpa->user_id = $this->id;
        $studentGpa->value = round($disciplineGpaSum / $creditSum, 2);
        $studentGpa->save();
    }

    /**
     * @param $phone
     * @return bool
     */
    public function sendPhoneConfirmCode($phone)
    {
        if(strlen($phone) == 10) {
            $phone = '+7' . preg_replace("/[^0-9]/", '', $phone);
        }

        $code = new PhoneConfirm();
        $code->user_id = $this->id;
        $code->phone_number = $phone;
        $code->code = rand(1000, 9999);
        $code->save();

        $code->sendSms();

        return true;
    }

    /**
     * @param $code
     * @return bool
     */
    public function checkPhoneConfirmCode($code)
    {
        $phoneConfirm = PhoneConfirm
            ::where('code', $code)
            ->where('user_id', $this->id)
            ->where('confirm', false)
            ->first();

        if(!$phoneConfirm)
        {
            return false;
        }

        $phoneConfirm->confirm = true;
        $phoneConfirm->save();

        return $phoneConfirm;
    }

    /**
     * @return |null
     */
    public function defaultCitizenshipId()
    {
        $country = Country::where('name', 'Kazakhstan')->first();

        if($country)
        {
            return $country->id;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function studentAllQuizSuccess()
    {
        $successTestDisciplines = $this->disciplines()
            ->wherePivot('final_result', '>=', 60)
            ->count();

        return $successTestDisciplines == count($this->disciplines);
    }

    /**
     * @return |null
     */
    public function balance()
    {
        $extStudent = DB
            ::connection('miras_full')
            ->select("SELECT student.id as id
                            FROM student 
                            LEFT JOIN s_user_person ON s_user_person.person_id = student.person_id
                            LEFT JOIN s_users ON s_users.id = s_user_person.user_id
                            WHERE s_users.login = '" . $this->email . "'
                            ");
        $extStudent = new Collection($extStudent);
        $extStudent = $extStudent[0] ?? null;

        if(!isset($extStudent->id))
        {
            return null;
        }

        $balanceRow = DB
            ::connection('miras_full')
            ->select("SELECT (SELECT f.initial_balance+f.payment_sum-f.fine_sum-f.charge_sum-f.refund_sum+f.correction_sum FROM finance_info f WHERE f.student_id=" . $extStudent->id . ") - (SELECT IFNULL(SUM(fw.cost*fw.quantity),0)  FROM finance_writeoff fw WHERE fw.status<>'COMMITTED' AND fw.student_id=" . $extStudent->id . ") as balance");
        $balanceRow = new Collection($balanceRow);

        return $balanceRow[0]->balance ?? null;
    }

    /**
     * @return bool
     */
    public function hasAcademDebt()
    {
        return !(bool)DB::connection('miras_full')
            ->table('student_admission_state_exam')
            ->select(['student.id', 's_users.login'])
            ->leftJoin('student', 'student.id', '=', 'student_admission_state_exam.student_id')
            ->leftJoin('s_user_person', 's_user_person.person_id', '=', 'student.person_id')
            ->leftJoin('s_users', 's_users.id', '=', 's_user_person.user_id')
            ->where('s_users.login', $this->email)
            ->where('admission_state_exam', true)
            ->count();
    }

    public function refreshSearchAdminMatriculants()
    {
        if(!$this->studentProfile)
        {
            return false;
        }

        $student = $this;
        $application = null;
        $degree = '';

        if( !empty($student->studentProfile->speciality) && ($student->studentProfile->speciality->code_char == 'b') )
        {
            $application = $student->bcApplication ?? null;
            $degree = 'Бакалавр';
        }

        if( !empty($student->studentProfile->speciality) && ($student->studentProfile->speciality->code_char == 'm') )
        {
            $application = $student->mgApplication ?? null;
            $degree = 'Магистр';
        }

        $cacheData = [
            'id'                => $student->id,
            'fio'               => $student->studentProfile->fio ?? '',
            'email'             => $student->email ?? '',
            'mobile'            => $student->studentProfile->mobile ?? '',
            'speciality'        => $student->studentProfile->speciality->name ?? '',
            'created_at'        => date('Y', strtotime($student->created_at)),
            'status'            => __($student->studentProfile->education_status),
            'base_education'    => __( !empty($application->education) ? $application->education . '_origin' : 'нет'),
            'education_form'    => $degree,
            'education_degree'  => __(!empty($application->edudegree) ? $application->edudegree . '_origin' : 'нет'),
            'education_lang'    => $student->studentProfile->education_lang ?? '',
            'category'          => $student->studentProfile->category ?? '',
            'check_level'       => $student->studentProfile->check_level ?? '',
            'deleted'           => $student->deleted_at ? 1 : 0
        ];

        Redis::sadd('list:' . $student->studentProfile->check_level ?? '', $student->id);
        return SearchCache::addOrUpdate(self::$adminRedisMatriculantTable, $student->id, $cacheData);
    }

    /**
     * @return bool|mixed
     * @throws \Exception
     */
    public function refreshBalance()
    {
        if(!$this->studentProfile)
        {
            return false;
        }

        $balance = Service1C::getBalance($this->studentProfile->iin);

        if($balance === false)
        {
            return false;
        }

        $this->balance = $balance;
        $this->save();

        return $this->balance;
    }

    public static function getCreditsLimit(int $userId, $currentSemester)
    {
        $path = 'student_discipline_credits_limit:' . $userId . ':' . $currentSemester;
        return Redis::get($path);
    }

    public function getCreatedTimestamp() : int
    {
        if (!empty($this->created_at)) {
            return (new DateTimeImmutable($this->created_at))->getTimestamp();
        }
        return 0;
    }

    public static function updateSearchCache(self $student, string $baseEducation = '', string $degree = '') : void
    {
        $baseEducation = !empty($baseEducation) ? __($baseEducation. '_origin') : '';
        $status = !empty($student->studentProfile->education_status) ? __($student->studentProfile->education_status) : '';
        $education_form = !empty($student->studentProfile->education_study_form) ? __($student->studentProfile->education_study_form) : '';
        $category = !empty($student->studentProfile->category) ? __($student->studentProfile->category) : '';

        SearchCache::addOrUpdate(User::$adminRedisMatriculantTable, $student->id, [
            'id' => $student->id,
            'fio' => $student->studentProfile->fio ?? '',
            'email' => $student->email,
            'mobile' => $student->studentProfile->mobile,
            'speciality' => $student->studentProfile->speciality->name ?? '',
            'created_at' => date('Y', strtotime($student->created_at)),
            'status' => $status,
            'base_education' => $baseEducation,
            'education_form' => $education_form,
            'education_degree' => $degree,
            'education_lang' => $student->studentProfile->education_lang ?? '',
            'category' => $category,
            'check_level' => $student->studentProfile->check_level ?? Profiles::CHECK_LEVEL_INSPECTION,
            'deleted' => $student->deleted_at ? 1 : 0
        ]);
    }

    public static function usersMoveToOR(array $userIds) : void
    {
        Profiles::whereIn('user_id', $userIds)->update(['check_level' => Profiles::CHECK_LEVEL_OR_CABINET]);

        foreach ($userIds as $userId) {
            SearchCache::updateField(self::$adminRedisMatriculantTable, $userId, 'check_level', Profiles::CHECK_LEVEL_OR_CABINET);

            Redis::sMove('list:'. Profiles::CHECK_LEVEL_INSPECTION, 'list:'. Profiles::CHECK_LEVEL_OR_CABINET, $userId);
        }
    }

    public static function usersMoveToInspection(array $userIds) : void
    {
        Profiles::whereIn('user_id', $userIds)->update(['check_level' => Profiles::CHECK_LEVEL_INSPECTION]);

        foreach ($userIds as $userId) {
            SearchCache::updateField(self::$adminRedisMatriculantTable, $userId, 'check_level', Profiles::CHECK_LEVEL_INSPECTION);

            Redis::sMove('list:'. Profiles::CHECK_LEVEL_OR_CABINET, 'list:'. Profiles::CHECK_LEVEL_INSPECTION, $userId);
        }
    }

    public static function updateSimpleSearchCache(self $student) : void
    {
        SearchCache::addOrUpdate(User::$adminRedisTable, $student->id, [
            'id' => $student->id,
            'name' => $student->name ?? '',
            'email' => $student->email ?? '',
            'phone' => $student->phone ?? ''
        ]);
    }

    public function updateGuestSearchCache() : void
    {
        if($this->studentProfile && $this->hasRole('guest'))
        {
            SearchCache::addOrUpdate(User::$adminRedisGuestTable, $this->id, [
                'id' => $this->id,
                'phone' => $this->studentProfile->mobile ?? '',
                'name' => $this->studentProfile->fio ?? '',
                'created_at' => date('d.m.Y', strtotime($this->created_at))
            ]);
        }
        else
        {
            SearchCache::delete(User::$adminRedisGuestTable, $this->id);
        }
    }

    public function getAdmissionDate() : ?int
    {
        if (!empty($this->created_at)) {
            return (new DateTimeImmutable($this->created_at))->format('Y');
        }
        return null;
    }

    public function setIgnoreConfirmMobile()
    {
        session(['ignore_confirm_mobile' => true]);
    }

    public function checkIgnoreConfirmMobile()
    {
        return session('ignore_confirm_mobile', false);
    }

    /**
     * Spent money to buy credits on $semester
     * @param int $semester
     * @return int
     */
    public function spentOnCredits($semester) : int
    {
        $boughtCredits = StudentDiscipline::getBoughtCredits($this->id, $semester);

        return $this->credit_price * $boughtCredits;
    }

    public function hasPayedDisciplines()
    {
        return (bool)DB::table('students_disciplines')
            ->where('student_id', $this->id)
            ->where('payed_credits', '>', 0)
            ->count();

    }

    /**
     * @param int $disciplineId
     * @return Collection
     */
    public function getTeacherDisciplineGroups(int $disciplineId) : Collection
    {
        return $this->teacherGroups()->where('discipline_id', $disciplineId)->get();
    }

    public static function getTeachersForSelect() : Collection
    {
        return self::select(['users.id', 'profile_teachers.fio'])
            ->join('profile_teachers', 'users.id', '=', 'profile_teachers.user_id')
            ->where('users.status', 1)
            ->whereNotNull('profile_teachers.fio')
            ->orderBy('profile_teachers.fio')
            ->get();
    }

    public static function searchUsersForQuizUsersTable(array $filters)
    {
        $query = self::select(['id', 'name'])
            ->with(['roles', 'studentProfile'])
            ->whereHas('studentProfile', function($query) use ($filters) {
                if (!empty($filters['categories'])) {
                    $query->where(function ($query) use ($filters) {
                        foreach ($filters['categories'] as $category) {
                            $query->orWhere('category', $category);
                        }
                    });
                }

                if (!empty($filters['course'])) {
                    $query->where(function ($query) use ($filters) {
                        $query->where('course', $filters['course']);

                        if (
                            !empty($filters['categories']) &&
                            in_array(Profiles::CATEGORY_MATRICULANT, $filters['categories'])
                        ) {
                            $query->orWhereNull('course');
                        }
                    });
                }

                if (!empty($filters['study_form'])) {
                    $query->where('education_study_form', $filters['study_form']);
                }

                if (!empty($filters['group'])) {
                    $studyGroup = StudyGroup::where('name', $filters['group'])->first();

                    if($studyGroup)
                    {
                        $query->where('study_group_id', $studyGroup->id);
                    }
                }

                return $query;
            })
            ->whereHas('roles', function ($query) use ($filters) {
                if (!empty($filters['roles'])) {
                    if (is_array($filters['roles'])) {
                        $query->where(function ($query) use ($filters) {
                            foreach ($filters['roles'] as $role) {
                                $query->orWhere('name', $role);
                            }
                        });
                    } else {
                        $query->where('name', $filters['roles']);
                    }
                }

                return $query;
            });

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        return $query;
    }

    /**
     * @param array $filters
     * @return array
     */
    static function getUserListForPollAdmin($filters) {
        $orderIds = [];

        if (!empty($filters['roles'])) {
            if (is_array($filters['roles'])) {
                foreach ($filters['roles'] as $role) {
                    $filteredIds = SearchCache::search(self::$adminRedisUsersPollTable, $role, 'roles');
                    $orderIds = array_unique(array_merge($orderIds, $filteredIds));
                }
            } else {
                $filteredIds = SearchCache::search(self::$adminRedisUsersPollTable, $filters['roles'], 'roles');
                $orderIds = array_merge($orderIds, $filteredIds);
            }
        }

        if (!empty($filters['categories'])) {
            $categoryIds = [];

            foreach ($filters['categories'] as $category) {
                $filteredIds = SearchCache::search(self::$adminRedisUsersPollTable, $category, 'category');
                $categoryIds = array_unique(array_merge($categoryIds, $filteredIds));
            }

            $orderIds = array_intersect($orderIds, $categoryIds);
        }

        if (!empty($filters['course'])) {
            $courseIds = [];

            foreach ($filters['course'] as $course) {
                $filteredIds = SearchCache::search(self::$adminRedisUsersPollTable, $course, 'course');
                $courseIds = array_unique(array_merge($courseIds, $filteredIds));
            }

            if (
                !empty($filters['categories']) &&
                in_array(Profiles::CATEGORY_MATRICULANT, $filters['categories'])
            ) {
                $filteredIds = SearchCache::search(self::$adminRedisUsersPollTable, '', 'course');
                $courseIds = array_unique(array_merge($filteredIds, $courseIds));
            }

            $orderIds = array_intersect($orderIds, $courseIds);
        }

        if (!empty($filters['study_form'])) {
            $studyFormIds = [];

            foreach ($filters['study_form'] as $study_form) {
                $filteredIds = SearchCache::search(self::$adminRedisUsersPollTable, $study_form, 'study_form');
                $studyFormIds = array_unique(array_merge($studyFormIds, $filteredIds));
            }

            $orderIds = array_intersect($orderIds, $studyFormIds);
        }

        if (!empty($filters['group'])) {
            $groupIds = [];

            foreach ($filters['group'] as $group) {
                $filteredIds = SearchCache::search(self::$adminRedisUsersPollTable, $group, 'group');
                $groupIds = array_unique(array_merge($groupIds, $filteredIds));
            }

            $orderIds = array_intersect($orderIds, $groupIds);
        }

        $users = self::select(['id', 'name'])->whereIn('id', $orderIds);

        if (!empty($filters['name'])) {
            $users->where('name', 'like', '%' . $filters['name'] . '%');
        }

        return $users;
    }

    /**
     * @return float|mixed
     */
    public function balanceByDebt()
    {
        $debtTrust = $this
            ->debtTrusts()
            ->where('contract_current_debt', '>', 0)
            ->where(function($query){
                $query
                    ->whereDate('finish_date', '>', DB::raw('NOW()'))
                    ->orWhereNull('finish_date');
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if ($debtTrust) {
            return $this->balance + $debtTrust->contract_current_debt;
        }

        return $this->balance;
    }

    /**
     * @param $firstDigits
     * @param $lastDigits
     * @param $type
     * @param $exDate
     * @param $issuer
     * @param $issuerBankCountry
     * @param string $token
     */
    public function attachPayCard(
        $firstDigits,
        $lastDigits,
        $type,
        $exDate,
        $issuer,
        $issuerBankCountry,
        $token = ''
    )
    {
        $payCardModel = $this->payCards()
                ->where('first_digits', $firstDigits)
                ->where('last_digits', $lastDigits)
                ->where('type', $type)
                ->where('exp_date', $exDate)
                ->first();

        if(!$payCardModel)
        {
            $payCardModel = new PayCard();
            $payCardModel->user_id = $this->id;
        }

        $payCardModel->fill([
            'first_digits' => $firstDigits,
            'last_digits' => $lastDigits,
            'type' => $type,
            'exp_date' => $exDate,
            'issuer' => $issuer,
            'issuer_bank_country' => $issuerBankCountry,
            'token' => $token
        ]);
        $payCardModel->save();

        return $payCardModel;
    }

    public static function getByIIN(string $iin) : ?self
    {
        $profile = Profiles::where('iin', $iin)->first();

        return $profile->user ?? null;
    }

    /**
     * @return bool
     */
    public function agitatorTestFinalRegistration()
    {

        if( !empty(Auth::user()->studentProfile) )
        {
            $oProfile = Auth::user()->studentProfile;
            if( $oProfile->agitator_registration_step != Profiles::AGITATOR_REGISTRATION_STEP_FINISH )
            {
                return true;
            }
        }

        return false;

    }

    /**
     * @param string $search
     * @param int|null $specialityId
     * @param int|null $year
     * @param string|null $baseEducationFilter
     * @param string|null $studyFormFilter
     * @param string|null $lang
     * @param int $start
     * @param int $length
     * @param int $orderColumn
     * @param string $orderDirection
     * @return array
     */
    static function getListForAdminStudyPlan(
        ?string $search = '',
        ?int $specialityId = null,
        ?int $year = null,
        ?string $baseEducationFilter = null,
        ?string $studyFormFilter = null,
        ?string $lang = null,
        int $start = 0,
        int $length = 10,
        int $orderColumn = 0,
        string $orderDirection = 'asc')
    {
        $orderColumnName = self::$adminAjaxColumnList[$orderColumn] ?? 'id';

        //$recordsTotal = SearchCache::totalCount(self::$adminRedisTable);

        $query = self::select(['id'])
            ->whereHas('studentProfile', function($q){
                $q->where('education_status', Profiles::EDUCATION_STATUS_STUDENT);
            })
            ->orderBy($orderColumnName, $orderDirection);

        $recordsTotal = $query;
        $recordsTotal = $recordsTotal->count();

        // Without filters
        if (
            empty($search) &&
            empty($specialityId) &&
            empty($year) &&
            empty($baseEducationFilter) &&
            empty($studyFormFilter) &&
            empty($lang)
        ) {
            $recordsFiltered = $recordsTotal;
        } else {
            // Speciality Filter
            if (!empty($specialityId)) {
                    $query->whereHas('studentProfile', function ($query1) use ($specialityId) {
                        $query1->where('education_speciality_id', $specialityId);
                    });
            }
            // Year filter
            if (!empty($year)) {
                    $query->whereHas('studentProfile', function ($query1) use ($year) {
                        $query1->whereHas('speciality', function ($query2) use ($year) {
                            $query2->where('year', $year);
                        });
                    });
            }
            // $baseEducationFilter
            if (!empty($baseEducationFilter)) {
                    $query->whereHas('bcApplication', function ($query1) use ($baseEducationFilter) {
                        $query1->where('education', $baseEducationFilter);
                    });
            }
            // $studyFormFilter
            if (!empty($studyFormFilter)) {
                    $query->whereHas('studentProfile', function ($query1) use ($studyFormFilter) {
                        $query1->where('education_study_form', $studyFormFilter);
                    });
            }
            // $typeFilter
            if (!empty($lang)) {
                    $query->whereHas('studentProfile', function ($query1) use ($lang) {
                        $query1->where('education_lang', $lang);
                    });
            }

            // Search string $search
            if (!empty($search)) {
                // Get ids
                $ids = SearchCache::searchFull(self::$adminRedisTable, $search);

                $query->whereIn('id', $ids);

                if (is_numeric($search)) {
                    $query->orWhere('id', (int)$search);
                }
            }

            $recordsFiltered = $query->count();
        }

        // Get result
        $users = $query->with(['studentProfile.speciality'])
            ->offset($start)
            ->take($length)
            ->get();

        $data = [];
        foreach ($users as $user) {
            $baseEducation = !empty($user->base_education) ? __($user->base_education) : '';
            $studyForm = !empty($user->studentProfile->education_study_form) ? __($user->studentProfile->education_study_form) : '';
            $speciality = !empty($user->studentProfile->speciality->name) ?
                $user->studentProfile->speciality->name . ' (' . $user->studentProfile->speciality->year . ')'  :
                '';

            $data[] = [
                $user->id,
                $user->fio ?? '',
                $speciality,
                $user->speciality_admission_year ?? '',
                $baseEducation,
                $studyForm,
                $user->studentProfile->education_lang ?? '',
                $user->last_semester_in_study_plan,
                $user->study_plan_admin_confirmed
            ];
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    public function isBuyingTime(string $semester) : bool
    {
        return $this->isTime($semester, Semester::TYPE_BUYING);
    }

    /**
     * Credit's price with user's discount
     * @param string $semester
     * @return int
     */
    public function getCreditPrice(string $semester) : int
    {
        $specialityPrice = SpecialityPrice::getCreditPrice(
            $this->studentProfile->education_speciality_id,
            $this->studentProfile->education_study_form,
            $this->base_education,
            $this->studentProfile->is_resident
        );

        $discount = DiscountStudent::getCreditPriceDiscount($this->id, $semester);

        return $specialityPrice * (1 - $discount / 100);
    }

    /**
     * @param string $semester
     * @return bool
     * @codeCoverageIgnore
     */
    public function isTest1Time(string $semester) : bool
    {
        return $this->isTime($semester, Semester::TYPE_TEST1);
    }

    /**
     * @param string $semester
     * @return bool
     * @codeCoverageIgnore
     */
    public function isSROTime(string $semester) : bool
    {
        return $this->isTime($semester, Semester::TYPE_SRO);
    }

    private function isTime(string $semester, string $type) : bool
    {
        // TODO Check for user's individual dates

        // Get Speciality dates
        if (!isset($this->semesterDates[$semester]['speciality'][$type])) {
            $this->semesterDates[$semester]['speciality'][$type] = SpecialitySemester::getDatesArray(
                $this->studentProfile->education_speciality_id,
                $this->studentProfile->education_study_form,
                $this->base_education,
                $semester,
                $type
            );
        }

        // Check Speciality dates
        if (!empty($this->semesterDates[$semester]['speciality'][$type])) {
            return Semester::todayBetween(
                $this->semesterDates[$semester]['speciality'][$type]['start_date'],
                $this->semesterDates[$semester]['speciality'][$type]['end_date']
            );
        }

        // Get default dates
        if (!isset($this->semesterDates[$semester]['default'][$type])) {
            $this->semesterDates[$semester]['default'][$type] = Semester::todayInDefaultDates(
                $this->studentProfile->education_study_form,
                $semester,
                $type
            );
        }

        // Check in Default dates
        return $this->semesterDates[$semester]['default'][$type];
    }

    public function activity_logs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public static function setZeroTest1BySpecialitySemester(SpecialitySemester $specialitySemester)
    {
        $semesterString = Semester::semesterInCurrentYear($specialitySemester->semester);

        self
            ::select(['id'])
            ->whereHas('studentProfile', function($query) use ($specialitySemester) {
                $query
                    ->where('education_speciality_id', $specialitySemester->speciality_id)
                    ->where('education_study_form', $specialitySemester->study_form);
            })
            ->where(function ($query) use ($specialitySemester) {
                $query
                    ->whereHas('bcApplication', function($query1) use ($specialitySemester) {
                        $query1->where('education', $specialitySemester->base_education);
                    })
                    ->orWhereHas('mgApplication', function($query2) use ($specialitySemester) {
                        $query2->where('education', $specialitySemester->base_education);
                    });
            })
            ->chunk(100, function ($users) use ($specialitySemester, $semesterString) {
                foreach ($users as $user) {
                    /** @var User $user */

                    $SDs = StudentDiscipline::getWithoutTest1($user->id, $semesterString);

                    foreach ($SDs as $SD) {
                        /** @var StudentDiscipline $SD */

                        if (!$SD->discipline->has_diplomawork && !$SD->discipline->is_practice) {
                            $SD->setTest1ZeroByTime();
                        }
                    }
                }
            });
    }

    public static function getIdsBySpecialitySemesters(Collection $specSemesters) : array
    {
        $userIds = [];

        foreach ($specSemesters as $specialitySemester) {
            self
                ::select(['id'])
                ->whereHas('studentProfile', function($query) use ($specialitySemester) {
                    $query
                        ->where('education_speciality_id', $specialitySemester->speciality_id)
                        ->where('education_study_form', $specialitySemester->study_form);
                })
                ->where(function ($query) use ($specialitySemester) {
                    $query
                        ->whereHas('bcApplication', function($query1) use ($specialitySemester) {
                            $query1->where('education', $specialitySemester->base_education);
                        })
                        ->orWhereHas('mgApplication', function($query2) use ($specialitySemester) {
                            $query2->where('education', $specialitySemester->base_education);
                        });
                })
                ->chunk(100, function ($users) use (&$userIds) {
                    foreach ($users as $user) {
                        $userIds[] = $user->id;
                    }
                });
        }

        return $userIds;
    }

    public static function setZeroTest1BySemester(Semester $semester, array $exceptUserIds)
    {
        $semesterString = Semester::semesterInCurrentYear($semester->number);

        self
            ::select(['id'])
            ->whereHas('studentProfile', function($query) use ($semester) {
                $query->where('education_study_form', $semester->study_form);
            })
            ->chunk(100, function ($users) use ($semesterString, $exceptUserIds) {
                foreach ($users as $user) {
                    /** @var User $user */
                    if (in_array($user->id, $exceptUserIds)) {
                        continue;
                    }

                    $SDs = StudentDiscipline::getWithoutTest1($user->id, $semesterString);

                    foreach ($SDs as $SD) {
                        /** @var StudentDiscipline $SD */

                        if (!$SD->discipline->has_diplomawork && !$SD->discipline->is_practice) {
                            $SD->setTest1ZeroByTime();
                        }
                    }
                }
            });
    }

    /**
     * @param string $semester
     * @return bool
     * @codeCoverageIgnore
     */
    public function isTest1RetakeTime(string $semester) : bool
    {
        return $this->isTime($semester, Semester::TYPE_TEST1_RETAKE);
    }

    /**
     * @param string $semester
     * @return bool
     * @codeCoverageIgnore
     */
    public function isExamTime(string $semester) : bool
    {
        return $this->isTime($semester, Semester::TYPE_EXAM);
    }

    /**
     * @param string $semester
     * @return bool
     * @codeCoverageIgnore
     */
    public function isExamRetakeTime(string $semester) : bool
    {
        return $this->isTime($semester, Semester::TYPE_EXAM_RETAKE);
    }

    /**
     * @return mixed
     */
    public static function getNotificationCount()
    {

        $iNCount = Notification::
        where('user_id',Auth::user()->id)->
        where('read',0)->
        count();

        $aUserNews = UserNews::
        select(['news_id'])->
        where('user_id',Auth::user()->id)->
        get()->
        toArray();

        $iNewsCount = News::
        whereNotIn('id',$aUserNews)->
        count();

        return $iNCount + $iNewsCount;
    }


    public static function setZeroExamBySpecialitySemester(SpecialitySemester $specialitySemester)
    {
        $semesterString = Semester::semesterInCurrentYear($specialitySemester->semester);

        self
            ::select(['id'])
            ->whereHas('studentProfile', function($query) use ($specialitySemester) {
                $query
                    ->where('education_speciality_id', $specialitySemester->speciality_id)
                    ->where('education_study_form', $specialitySemester->study_form);
            })
            ->where(function ($query) use ($specialitySemester) {
                $query
                    ->whereHas('bcApplication', function($query1) use ($specialitySemester) {
                        $query1->where('education', $specialitySemester->base_education);
                    })
                    ->orWhereHas('mgApplication', function($query2) use ($specialitySemester) {
                        $query2->where('education', $specialitySemester->base_education);
                    });
            })
            ->chunk(100, function ($users) use ($specialitySemester, $semesterString) {
                foreach ($users as $user) {
                    /** @var User $user */
                    $SDs = StudentDiscipline::getWithoutExam($user->id, $semesterString);

                    foreach ($SDs as $SD) {
                        /** @var StudentDiscipline $SD */
                        $SD->setExamZeroByTime();

                        if ($SD->test1_result !== null && $SD->test_result !== null && $SD->task_result !== null) {
                            $SD->calculateFinalResult();
                        }
                    }
                }
            });
    }

    public static function setZeroExamBySemester(Semester $semester, array $exceptUserIds)
    {
        $semesterString = Semester::semesterInCurrentYear($semester->number);

        self
            ::select(['id'])
            ->whereHas('studentProfile', function($query) use ($semester) {
                $query->where('education_study_form', $semester->study_form);
            })
            ->chunk(100, function ($users) use ($semesterString, $exceptUserIds) {
                foreach ($users as $user) {
                    /** @var User $user */
                    if (in_array($user->id, $exceptUserIds)) {
                        continue;
                    }

                    $SDs = StudentDiscipline::getWithoutExam($user->id, $semesterString);

                    foreach ($SDs as $SD) {
                        /** @var StudentDiscipline $SD */
                        $SD->setExamZeroByTime();

                        if ($SD->test1_result !== null && $SD->test_result !== null && $SD->task_result !== null) {
                            $SD->calculateFinalResult();
                        }
                    }
                }
            });
    }

    public function isSRORetakeTime(string $semester) : bool
    {
        return $this->isTime($semester, Semester::TYPE_SRO_RETAKE);
    }

    public static function setZeroSROBySpecialitySemester(SpecialitySemester $specialitySemester)
    {
        $semesterString = Semester::semesterInCurrentYear($specialitySemester->semester);

        self
            ::select(['id'])
            ->whereHas('studentProfile', function($query) use ($specialitySemester) {
                $query
                    ->where('education_speciality_id', $specialitySemester->speciality_id)
                    ->where('education_study_form', $specialitySemester->study_form);
            })
            ->where(function ($query) use ($specialitySemester) {
                $query
                    ->whereHas('bcApplication', function($query1) use ($specialitySemester) {
                        $query1->where('education', $specialitySemester->base_education);
                    })
                    ->orWhereHas('mgApplication', function($query2) use ($specialitySemester) {
                        $query2->where('education', $specialitySemester->base_education);
                    });
            })
            ->chunk(100, function ($users) use ($specialitySemester, $semesterString) {
                foreach ($users as $user) {
                    /** @var User $user */
                    $SDs = StudentDiscipline::getWithoutSRO($user->id, $semesterString);

                    foreach ($SDs as $SD) {
                        /** @var StudentDiscipline $SD */
                        $SD->setSROZeroByTime();

                        if ($SD->test1_result !== null && $SD->test_result !== null && $SD->task_result !== null) {
                            $SD->calculateFinalResult();
                        }
                    }
                }
            });
    }

    public static function setZeroSROBySemester(Semester $semester, array $exceptUserIds)
    {
        $semesterString = Semester::semesterInCurrentYear($semester->number);

        self
            ::select(['id'])
            ->whereHas('studentProfile', function($query) use ($semester) {
                $query->where('education_study_form', $semester->study_form);
            })
            ->chunk(100, function ($users) use ($semesterString, $exceptUserIds) {
                foreach ($users as $user) {
                    /** @var User $user */
                    if (in_array($user->id, $exceptUserIds)) {
                        continue;
                    }

                    $SDs = StudentDiscipline::getWithoutSRO($user->id, $semesterString);

                    foreach ($SDs as $SD) {
                        /** @var StudentDiscipline $SD */
                        $SD->setSROZeroByTime();

                        if ($SD->test1_result !== null && $SD->test_result !== null && $SD->task_result !== null) {
                            $SD->calculateFinalResult();
                        }
                    }
                }
            });
    }

    public function semesterDatesFlush() : void
    {
        $this->semesterDates = [];
    }

    public static function getRandomTeacherId() : int
    {
        /** @var self $teacher */
        $teacher = self
            ::select(['id'])
            ->whereHas('teacherProfile')
            ->inRandomOrder()
            ->first();

        return $teacher->id;
    }


    public static function getRandomStudentId() : int
    {
        /** @var self $student */
        $student = self
            ::select(['id'])
            ->whereHas('studentProfile')
            ->inRandomOrder()
            ->first();

        return $student->id;
    }

    /**
     * @return mixed
     */
    public function newStudentFilesByDiscipline($disciplineId)
    {
        return $this->disciplineStudentFiles->where('discipline_id', $disciplineId)->where('new_file', true)->count();

    }

    /**
     * @param string $semester
     * @return bool
     * @codeCoverageIgnore
     */
    public function isSyllabusTime(string $semester) : bool
    {
        return $this->isTime($semester, Semester::TYPE_SYLLABUSES);
    }

    /**
     * @param string $semester
     * @return bool
     * @codeCoverageIgnore
     */
    public function isPayCancelTime(string $semester) : bool
    {
        return $this->isTime($semester, Semester::TYPE_BUY_CANCEL);
    }
}

