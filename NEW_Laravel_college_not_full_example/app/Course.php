<?php

namespace App;

use App\Language;
use App\Services\Auth;
use App\Services\Avatar;
use App\Teacher\ProfileTeacher;
use Illuminate\Database\Eloquent\{Model,SoftDeletes};

class Course extends Model
{

    const STATUS_MODERATION = 'moderation';
    const STATUS_ACTIVE     = 'active';

    const STATUS_FORM_HOLDING_FULLTIME = 'fulltime';
    const STATUS_FORM_HOLDING_ONLINE   = 'online';
    const STATUS_FORM_HOLDING_DISTANT  = 'distant';

    const IS_CERTIFICATE_YES = 'yes';
    const IS_CERTIFICATE_NO  = 'no';

    protected $table = 'courses';

    protected $fillable = [
        'title',
        'photo',
        'video_link',
        'tags',
        'status',
        'title_card',
        'author_resume_file',
        'author_resume_link',
        'user_id',

        'description',
        'certificate_file_name',
        'hours',
        'form_holding',
        'training_group',
        'scheme_courses_file',
        'scheme_courses_link',
        'trial_course_file',
        'trial_course_link',
        'is_certificate',
        'cost',
        'inner_photo',
        'schedule',
        'language',
        'title_kz',
        'title_en',
        'title_card_kz',
        'title_card_en',
        'description_kz',
        'description_en'
    ];

    /**
     * @param $value
     * @return mixed
     */
    public function getField($value)
    {
        $value = Language::getFieldName($value,app()->getLocale());
        return $this->attributes[$value];
    }

    /**
     * @param $value
     */
    public function setPhotoAttribute($value)
    {
        if($value) {
            $this->attributes['photo_file_name'] = 'course_' . time() . '.' . $value->getClientOriginalExtension();
        }
    }

    /**
     * @param $value
     */
    public function setAuthorResumeFileAttribute($value)
    {
        if($value) {
            $this->attributes['author_resume_file'] = 'author_resume' . time() . '.' . $value->getClientOriginalExtension();
        }
    }

    /**
     * @param $value
     */
    public function setCertificateFileAttribute($value)
    {
        if($value) {
            $this->attributes['certificate_file_name'] = 'cert_' . time() . '.' . $value->getClientOriginalExtension();
        }
    }

    /**
     * @param $value
     */
    public function setSchemeCoursesFileAttribute($value)
    {
        if($value) {
            $this->attributes['scheme_courses_file'] = 'scheme_courses' . time() . '.' . $value->getClientOriginalExtension();
        }
    }

    /**
     * @param $value
     */
    public function setTrialCourseFileAttribute($value)
    {
        if($value) {
            $this->attributes['trial_course_file'] = 'trial_course' . time() . '.' . $value->getClientOriginalExtension();
        }
    }

    /**
     * @param $value
     */
    public function setInnerPhotoAttribute($value)
    {
        if($value) {
            $this->attributes['inner_photo'] = 'inner_photo' . time() . '.' . $value->getClientOriginalExtension();
        }
    }

    /**
     * @param $value
     */
    public function setVideoLinkAttribute($value)
    {
        if($value) {
            $this->attributes['video_link'] = self::getNormalizeLink($value);
        }
    }

    /**
     * @param $value
     */
    public function setAuthorResumeLinkAttribute($value)
    {
        if($value) {
            $this->attributes['author_resume_link'] = self::getNormalizeLink($value);
        }
    }

    /**
     * @param $value
     */
    public function setSchemeCoursesLinkAttribute($value)
    {
        if($value) {
            $this->attributes['scheme_courses_link'] = self::getNormalizeLink($value);
        }
    }

    /**
     * @param $value
     */
    public function setTrialCourseLinkAttribute($value)
    {
        if($value) {
            $this->attributes['trial_course_link'] = self::getNormalizeLink($value);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lectures()
    {
        return $this->hasMany(Lecture::class, 'course_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function disciplines()
    {
        return $this->belongsToMany(
            Discipline::class, 'course_discipline', 'course_id', 'discipline_id');
    }

    /**
     * @return bool
     */
    public function canCreateLecture()
    {
        return true;
        /*todo Uncomment when can set rating for lecture
        if(
            $this->status != self::STATUS_ACTIVE &&
            $this->user->teacherProfile->status != ProfileTeacher::STATUS_ACTIVE
        )
        {
            return false;
        }

        $lecturesWithRating = $this->lectures()->


            whereHas('ratingList')->
            count();

        if(count($this->lectures) == 0 || $lecturesWithRating == count($this->lectures))
        {
            return true;
        }

        return false;*/
    }

    /**
     * @param $value
     */
    public function setDiscipline($value)
    {
        $courseDiscipline = CourseDiscipline::where('course_id', $this->id)->first();

        if(!$courseDiscipline)
        {
            $courseDiscipline = new CourseDiscipline();
        }

        $courseDiscipline->course_id = $this->id;
        $courseDiscipline->discipline_id = $value;
        $courseDiscipline->save();
    }

    /**
     * @return int
     */
    public function getDisciplineIdAttribute()
    {
        $courseDiscipline = CourseDiscipline::where('course_id', $this->id)->first();
        return isset($courseDiscipline->discipline_id) ? $courseDiscipline->discipline_id : 0;
    }

    /**
     * @return mixed|string
     */
    public function getTitle()
    {
        if($this->title)
        {
            return $this->title;
        }

        if(isset($this->discipline_title) && $this->discipline_title)
        {
            return $this->discipline_title;
        }

        $discipline = Discipline::
            leftJoin('course_discipline', 'course_discipline.discipline_id', '=', 'disciplines.id')->
            where('course_discipline.course_id', $this->id)->
            first();

        if($discipline)
        {
            return $discipline->name;
        }

        return '';
    }

    /**
     * @return mixed
     */
    static function getListForAdmin($orderField = 'id', $orderType = 'asc', $lang = \App\Profiles::EDUCATION_LANG_RU)
    {
        return self::select([
            'courses.id as id',
            'title',
            'courses.title_card',
            'photo_file_name',
            'status'
        ])->
            /*
        with(['user' => function($query){
            $query->with('teacherProfile');
            $query->with('educationDocumentList');
        }])->
            */
        //leftJoin('course_discipline', 'course_discipline.course_id', '=', 'courses.id')->
        //leftJoin('disciplines', 'course_discipline.discipline_id', '=', 'disciplines.id')->
        //whereHas('user')->
        whereNull('deleted_at')->
        orderBy($orderField, $orderType)->
        get();

    }

    /**
     * @return mixed
     */
    static function getListForShopOther()
    {
        return self::with(['user' => function ($query) {
            $query->with('teacherProfile');
        }])->
        whereDoesntHave('disciplines')->
        whereHas('user', function ($query) {
            $query->whereHas('teacherProfile', function ($query1) {
                $query1->where('status', ProfileTeacher::STATUS_ACTIVE);
            });
        })->
        where('status', Course::STATUS_ACTIVE)->
        get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    static function getListForShopDisciplines()
    {
        return self::
        select([
            'courses.id as id',
            'courses.user_id as user_id',
            'courses.title_card as description',
            'disciplines.name as discipline_title'
        ])->
        with(['user' => function($query){
            $query->with('teacherProfile');
        }])->
        leftJoin('course_discipline', 'course_discipline.course_id', '=', 'courses.id')->
        leftJoin('disciplines', 'course_discipline.discipline_id', '=', 'disciplines.id')->
        whereHas('disciplines', function($query){
            $query->whereHas('students', function($query1){
                $query1->where('student_id', Auth::user()->id);
                $query1->where('payed', true);
            });
        })->
        whereHas('user', function($query){
            $query->whereHas('teacherProfile', function($query1){
                $query1->where('status', ProfileTeacher::STATUS_ACTIVE);
            });
        })->
        where('status', Course::STATUS_ACTIVE)->
        get();
    }

    /**
     * @return mixed
     */
    static function getForShopDetails($id)
    {
        return self::
        with(['user' => function($query){
            $query->with('teacherProfile');
        }])->
        with(['lectures' => function($query){
            $query->with(['students' => function($query1){
                $query1->where('users.id', Auth::user()->id);
            }]);
            $query->with('offlineStudents');
        }])->
        whereHas('user', function($query){
            $query->whereHas('teacherProfile', function($query1){
                $query1->where('status', ProfileTeacher::STATUS_ACTIVE);
            });
        })->
        where(function ($query){
            $query->
            whereDoesntHave('disciplines')->
            orWhereHas('disciplines', function($query1){
                $query1->whereHas('students', function($query2){
                    $query2->where('student_id', Auth::user()->id);
                    $query2->where('payed', true);
                });
            });
        })->

        where('id', $id)->
        where('status', Course::STATUS_ACTIVE)->
        first();
    }

    /**
     * @param $photoFile
     * @param $certificateFile
     * @return bool
     */
    public function saveFiles(
        $photoFile=null,
        $authorResumeFile=null,
        $certificateFile=null,
        $schemeCoursesFile=null,
        $trialCourseFile=null,
        $innerPhoto=null
        )
    {
        if($photoFile)
        {
            Avatar::make($photoFile)->saveToCourse( $this->photo_file_name );
        }

        if ($authorResumeFile)
        {
            $authorResumeFile->move(public_path('images/uploads/courses'), $this->author_resume_file);
        }

        if ($certificateFile)
        {
            $certificateFile->move(public_path('images/uploads/certificates'), $this->certificate_file);
        }

        if ($schemeCoursesFile)
        {
            $schemeCoursesFile->move(public_path('images/uploads/courses'), $this->scheme_courses_file);
        }

        if ($trialCourseFile)
        {
            $trialCourseFile->move(public_path('images/uploads/courses'), $this->trial_course_file);
        }

        if ($innerPhoto)
        {
            $innerPhoto->move(public_path('images/uploads/courses'), $this->inner_photo);
        }

        return true;
    }

    /**
     * @return bool|null
     */
    public function delete()
    {
        CourseDiscipline::where('course_id', $this->id);

        $photoFileName = public_path('images/uploads/courses/' . $this->photo_file_name);
        $certificateFileName = public_path('images/uploads/certificates/' . $this->certificate_file_name);
        $authorResumeFile = public_path('images/uploads/courses/' . $this->author_resume_file);
        $schemeCoursesFile = public_path('images/uploads/courses/' . $this->scheme_courses_file);
        $trialCourseFile = public_path('images/uploads/courses/' . $this->trial_course_file);
        $innerPhoto = public_path('images/uploads/courses/' . $this->inner_photo);

        if(file_exists($photoFileName) && is_file($photoFileName))
        {
           \File::delete($photoFileName);
        }

        if(file_exists($certificateFileName) && is_file($certificateFileName))
        {
            \File::delete($certificateFileName);
        }

        if(file_exists($authorResumeFile) && is_file($authorResumeFile))
        {
            \File::delete($authorResumeFile);
        }

        if(file_exists($schemeCoursesFile) && is_file($schemeCoursesFile))
        {
            \File::delete($schemeCoursesFile);
        }

        if(file_exists($trialCourseFile) && is_file($trialCourseFile))
        {
            \File::delete($trialCourseFile);
        }

        if(file_exists($innerPhoto) && is_file($innerPhoto))
        {
            \File::delete($innerPhoto);
        }

        return parent::delete();
    }

    /**
     * @param $value
     * @return string
     */
    public static function getNormalizeLink($value)
    {
        return 'http://' . str_replace(['http://','https://'],'',$value);
    }

}
