<?php

namespace App\Teacher;

use App\Services\Auth;
use App\{Discipline, ProfileDoc, StudyGroup, User, UserEducationDocument};
use Illuminate\Database\Eloquent\Model;
use App\Role;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProfileTeacher
 * @package App\Teacher
 * @property int id
 * @property string fio
 */
class ProfileTeacher extends Model
{
    use SoftDeletes;

    const DOCTYPE_PASS  = 'pass';
    const DOCTYPE_ID    = 'id';

    const SEX_MALE = 'male';
    const SEX_FEMALE = 'female';

    const STATUS_MODERATION = 'moderation';
    const STATUS_ACTIVE = 'active';
    const STATUS_BLOCK = 'block';

    const ALIEN_STATUS_RESIDENT = 'resident';
    const ALIEN_STATUS_ALIEN = 'alien';

    const FAMILY_STATUS_SINGLE = 'single';
    const FAMILY_STATUS_MARITAL = 'marital';

    const REGISTRATION_STEP_USER_PROFILE_ID = 'teacherMirasUserProfileID';                          // шаг 1 - подгрузка УЛ
    const REGISTRATION_STEP_USER_PROFILE_ID_POST = 'teacherMirasUserProfileID';
    const REGISTRATION_STEP_USER_PROFILE_ID_MANUAL = 'teacherMirasUserProfileIDManual';             // шаг 1 - ручной воод данных УЛ
    const REGISTRATION_STEP_USER_PROFILE_ID_MANUAL_POST = 'teacherMirasUserProfileIDManualPost';
    const REGISTRATION_STEP_PROFILE_EDIT = 'teacherMirasProfileEdit';
    const REGISTRATION_STEP_FAMILY_STATUS = 'teacherMirasFamilyStatus';                             // шаг 2 - семейное положение
    const REGISTRATION_STEP_FAMILY_STATUS_POST = 'teacherMirasFamilyStatusPost';
    const REGISTRATION_STEP_ADD_ADRESS = 'teacherMirasAddAdress';                                   // шаг 3 - ввод адреса
    const REGISTRATION_STEP_ADD_ADRESS_POST = 'teacherMirasAddAdressPost';
    const REGISTRATION_STEP_ENTER_MOBILE_PHONE = 'teacherMirasEnterMobilePhone';                    // шаг 4 - ввод телефона
    const REGISTRATION_STEP_ENTER_MOBILE_PHONE_SENDCODE = 'teacherMirasEnterMobilePhoneSendcode';
    const REGISTRATION_STEP_ENTER_MOBILE_PHONE_POST = 'teacherMirasEnterMobilePhonePost';
    const REGISTRATION_STEP_ENTER_RESUME = 'teacherMirasEnterResume';                               // шаг 5 - резюме
    const REGISTRATION_STEP_ENTER_RESUME_POST = 'teacherMirasEnterResumePost';
    const REGISTRATION_STEP_ENTER_EDUCATION = 'teacherMirasEnterEducation';                         // шаг 6 - ввод образования
    const REGISTRATION_STEP_ENTER_EDUCATION_POST = 'teacherMirasEnterEducationPost';
    const REGISTRATION_STEP_SENIORITY = 'teacherMirasSeniority';                                    // шаг 7 - трудовой стаж
    const REGISTRATION_STEP_SENIORITY_POST = 'teacherMirasSeniorityPost';
    const REGISTRATION_STEP_FINISH = 'teacherMirasFinish';

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 0;

    /**
     * @var string
     */
    protected $table = 'profile_teachers';

    /**
     * @var array
     */
    protected $fillable = [
        'photo',
        'iin',
        'fio',
        'bdate',
        'doctype',
        'docnumber',
        'issuing',
        'issuedate',
        'expire_date',
        'sex',
        'mobile',
        'education_document',
        'facebook',
        'insta',
        'nationality_id',
        'citizenship_id',
        'alien',
        'family_status',
        'docseries',
        'home_phone',
        'resume_link',
        'country_id',
        'region_id',
        'city_id',
        'street',
        'building_number',
        'apartment_number',
        'home_country_id',
        'home_region_id',
        'home_city_id',
        'home_street',
        'home_building_number',
        'home_apartment_number',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'bdate',
        'issuedate'
    ];

    /**
     * @param $value
     */
    public function setPhotoAttribute($value)
    {
        if($value) {
            $this->attributes['photo'] = time() . '.' . $value->getClientOriginalExtension();
        }
    }

    /**
     * @param $value
     */
    public function setFacebookAttribute($value)
    {
        $this->user->facebook = $value;
    }

    /**
     * @param $value
     */
    public function setInstaAttribute($value)
    {
        $this->user->insta = $value;
    }

    /**
     * @param $value
     */
    public function setEducationDocumentAttribute($value)
    {
        if(empty($value['level']))
        {
            return;
        }

        if($value['level'] == 'none')
        {
            UserEducationDocument::where('user_id', $this->user->id)->delete();
            return;
        }

        $educationDocument = $this->user->educationDocumentFirst();

        if(!$educationDocument)
        {
            $educationDocument = new UserEducationDocument();
            $educationDocument->user_id = $this->user->id;
        }

        $educationDocument->fill($value);
        $educationDocument->save();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function studyGroups()
    {
        return $this->belongsToMany(StudyGroup::class, 'study_group_teacher', 'user_id', 'study_group_id', 'user_id');
    }

    /**
     * @param $smartIdResult
     * @return bool
     */
    public function combineWithSmartId($smartIdResult)
    {
        $this->attributes['photo']  = !empty($smartIdResult->photo) ? $smartIdResult->photo : $this->attributes['photo'];
        $this->iin                  = !empty($smartIdResult->inn) ? $smartIdResult->inn : $this->iin;
        $this->fio                  = !empty(trim($smartIdResult->fio)) ? $smartIdResult->fio : $this->fio;
        $this->bdate                = !empty($smartIdResult->birth_date) ? strtotime($smartIdResult->birth_date) : $this->bdate;
        $this->docnumber            = !empty($smartIdResult->number_mrz) ? $smartIdResult->number_mrz : $this->docnumber;
        $this->issuing              = !empty($smartIdResult->issue_authority) ? $smartIdResult->issue_authority : $this->issuing;
        $this->issue_date           = !empty($smartIdResult->issue_date) ? $smartIdResult->issue_date : $this->issue_date;

        return true;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    static function getTeacherListForAdmin()
    {
        $role = Role::where('name', Role::NAME_TEACHER)->first();

        return
            User::select('users.*')
                ->leftJoin('user_role', 'user_role.user_id', '=', 'users.id')
                ->with('teacherProfile')
                ->where('user_role.role_id', $role->id)
                ->get();
    }

    /**
     * @param $id
     * @return ProfileTeacher|null
     */
    static function getTeacherForAdmin($id)
    {
        $userTeacher = null;
        /* todo to future create teacher from admin panel
        if($id == 'add')
        {
            $profileTeacher = new self();
        }*/
        if(is_numeric($id))
        {
            $role = Role::where('name', Role::NAME_TEACHER)->first();

            $userTeacher =
                User::select('users.*')
                    ->leftJoin('user_role', 'user_role.user_id', '=', 'users.id')
                    ->with('teacherProfile')
                    ->with('teacherStudyGroups')
                    ->where('user_role.role_id', $role->id)
                    ->where('users.id', $id)
                    ->first();
        }

        return $userTeacher;
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $this->user->save();

        return parent::save($options);
    }

    /**
     * @return mixed
     */
    public function getTeacherMirasResumeAttribute()
    {
        return $this->profileDocs->where('doc_type', ProfileDoc::TYPE_TEACHER_MIRAS_RESUME)->last();
    }

}
