<?php

namespace App;

use App\Services\SearchCache;
use App\Services\Translit;
use App\Chatter\Models\Category;
use App\Chatter\Models\Discussion;
use App\Chatter\Models\Post;
use Carbon\Carbon;
use DevDojo\Chatter\Helpers\ChatterHelper;
use Illuminate\Database\Eloquent\Model;
use App\Services\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use OwenIt\Auditing\Contracts\Auditable;
use App\{Models\Discipline\DisciplineSemester,
    Models\Speciality\SpecialityDisciplineDependence,
    Models\Student\StudentGroupTeacher,
    Models\StudentDisciplineDayLimit,
    Models\StudentDisciplineFile,
    SpecialityDiscipline};

/**
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property string name
 * @property string dependence
 * @property string dependence2
 * @property string dependence3
 * @property string dependence4
 * @property string dependence5
 * @property int ects
 * @property bool tests_lang_invert
 * @property bool verbal_sro
 * @property string control_form
 * @property bool is_practice
 * @property bool has_diplomawork
 * @property string practise_1sem_control_start
 * @property string practise_1sem_control_end
 * @property string practise_2sem_control_start
 * @property string practise_2sem_control_end
 * @property int language_level
 * @property StudentGroupTeacher studyGroupTeachers
 * @property User teachers
 * @property SpecialityDiscipline specialityDisciplines
 *
 * @property-read bool has_syllabuses
 *
 * @property-read Syllabus syllabuses
 */
class Discipline extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    // recalculation status
    const RECALCULATION_STATUS_OK = 'ok';
    const RECALCULATION_STATUS_ERROR = 'error';

    // является практикой
    const IS_PRACTICE_ACTIVE = 1;
    const IS_PRACTICE_INACTIVE = 0;

    const CONTROL_FORM_TRADITIONAL = 'traditional';

    // так некотрые поля скрыты - добавляем дефаултные значение.
    const DISCIPLINE_CREDITS_DEFAULT = 0;
    const DISCIPLINE_LANGUAGE_LVL_DEFAULT = 0;

    protected $casts = [
        'tests_lang_invert' => 'boolean',
        'is_practice' => 'boolean',
        'has_diplomawork' => 'boolean',
        'verbal_sro' => 'boolean'
    ];

    public static $languageLevels = [
        1 => 'A1',
        2 => 'A2',
        3 => 'B1',
        4 => 'B2',
        5 => 'C1',
        6 => 'C2',
        7 => 'A1, часть1',
        8 => 'A1, часть2',
        9 => 'A2, часть1',
        10 => 'A2, часть2',
        11 => 'B1, часть1',
        12 => 'B1, часть2',
        13 => 'B2, часть1',
        14 => 'B2, часть2',
        15 => 'C1, часть1',
        16 => 'C1, часть2',
        17 => 'C2, часть1',
        18 => 'C2, часть2'
    ];

    public static $cycles = [
        'ООД',
        'ОГД',
        'СЭД',
        'ОПД',
        'СД',
        'ДД',
        'ДО',
        'ДООО',
        'ПО',
        'ПП',
        'ДП',
        'ИА',
        'Ф',
        'БМ',
        'ПМ',
        'МОО'
    ];

    public static $mtTks = [
        'ОК',
        'ВК',
        'КВ',
        'ФК',
        'НЕТ',
        'ФД',
        'ИРМ'
    ];

    public static $languageTypes = [
        'native' => 'Родной',
        'second' => 'Второй',
        'other' => 'Другой'
    ];

    protected $table = 'disciplines';

    protected $fillable = [
        'ex_id',
        'sector_id',
        'name',
        'name_kz',
        'name_en',
        'num_ru',
        'num_kz',
        'num_en',
        'module_number',
        'credits',
        'ects',
        'description',
        'description_kz',
        'description_en',
        'tests_lang_invert',
        'lecture_hours',
        'practical_hours',
        'laboratory_hours',
        'sro_hours',
        'verbal_sro',
        'srop_hours',
        'control_form',
        'semester',
        'kz',
        'ru',
        'en',
        'recalculation_status',
        'is_practice',
        'has_diplomawork',
        'practise_1sem_control_start',
        'practise_1sem_control_end',
        'practise_2sem_control_start',
        'practise_2sem_control_end',
        'language_level'
    ];

    private static $adminAjaxColumnList = [
        'id',
        'name',
        'credits',
        'recalculation_status',
    ];

    public static $adminRedisTable = 'admin_disciplines';

    public $depWithoutResult = [];

    /**
     * @return mixed
     */
    public function getNameAttribute()
    {
        if (!isset($this->attributes['name'])) {
            return '';
        }

        $educationLang = Auth::user()->studentProfile->education_lang ?? null;

        $result = $this->attributes['name'];

        if ($educationLang == 'en') {
            $result = $this->attributes['name_en'];
        }

        if ($educationLang == 'kz') {
            $result = $this->attributes['name_kz'];
        }

        return $result;
    }

    public function getHasSyllabusesAttribute() : bool
    {
        return Syllabus::where('discipline_id', $this->id)->exists();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function students()
    {
        return $this->belongsToMany(
            User::class, 'students_disciplines', 'discipline_id', 'student_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function forumCategories()
    {
        return $this->belongsToMany(
            Category::class, 'chatter_category_discipline', 'discipline_id', 'chatter_category_id');
    }

    public function forumCategory()
    {
        return $this->forumCategories->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function syllabuses()
    {
        return $this->hasMany(Syllabus::class);
    }

    public function syllabusTasks()
    {
        return $this->hasMany(SyllabusTask::class);
    }

    public function sector()
    {
        return $this->belongsTo(EmployeesDepartment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function modules()
    {
        return $this->belongsToMany(Module::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(StudentPracticeFiles::class);
    }

    public function disciplineFiles()
    {
        return $this->hasMany(StudentDisciplineFile::class);
    }

    public function studentDisciplineDayLimits()
    {
        return $this->hasMany(StudentDisciplineDayLimit::class);
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $saveStatus = parent::save($options);

        /*Attach forum category*/
        $forumCategory = $this->forumCategories->first();

        if(empty($forumCategory)) {
            $forumCategory = new Category();
        }

        $forumCategory->name = $this->name;
        $forumCategory->slug = Translit::get($this->name);
        $forumCategory->color = '#'.ChatterHelper::stringToColorCode(Translit::get($this->name));
        $forumCategory->save();

        $this->forumCategories()->sync($forumCategory->id);

        return $saveStatus;

    }

    /**
     * @return bool
     */
    public function updateQuestionIndex()
    {
        /* path = question_by_discipline:<discipline>:<correctCount>:<lang>*/
        $path = 'question_by_discipline:' . $this->id . ':';
        $disciplineId = $this->id;
        $questionList = QuizQuestion
            ::select(['id'])
            ->with(['answers' => function ($query) {
                $query->select(['id', 'question_id', 'correct']);
            }])
            ->with('syllabuses')
            ->whereHas('syllabuses', function ($query) use ($disciplineId) {
                $query->where('discipline_id', $disciplineId);
            })
            ->get();

        if ($questionList) {
            $hasClear = [];

            foreach ($questionList as $question) {
                if (isset($question->syllabus)) {
                    $correctCount = $question->getCorrectAnswersCount();
                    $fullPath = $path . $correctCount . ':' . $question->syllabus->language;

                    if (!in_array($fullPath, $hasClear)) {
                        Redis::del($fullPath);
                        $hasClear[] = $fullPath;
                    }

                    Redis::sadd($fullPath, $question->id);
                }
            }
        }

        return true;
    }

    /**
     * @param $correctAnswersCount
     * @return mixed
     */
    public function getQuestionIdListByCorrectCount($correctAnswersCount, $lang)
    {
        /* path = question_by_discipline:<discipline>:<correctCount>:<lang>*/
        $path = 'question_by_discipline:' . $this->id . ':' . $correctAnswersCount . ':' . $lang;

        return Redis::smembers($path);
    }

    /**
     * @param $specialityId
     * @return bool
     */
    public function InSpecialityModules($speciality)
    {
        $modulesIds = [];

        foreach ($this->modules as $module) {
            foreach ($speciality->modules as $spModule) {
                if ($spModule->id == $module->id) {
                    return true;
                }
            }
            $modulesIds[] = $module->id;
        }

        return false;
    }

    /**
     * @param string $search
     * @param int $start
     * @param int $length
     * @param int $orderColumn
     * @param string $orderDir
     * @param array $userAdminDiscipline
     * @return array
     */
    static function getDisciplineListForAdmin(
        ?string $search = '',
        int $start = 0,
        int $length = 10,
        int $orderColumn = 0,
        string $orderDir = 'asc',
        $userAdminDiscipline = [])
    {
        $recordsTotal = SearchCache::totalCount(self::$adminRedisTable);

        $orderColumnName = self::$adminAjaxColumnList[$orderColumn] ?? 'id';

        $query = self::orderBy($orderColumnName, $orderDir);

        if ($userAdminDiscipline) {
            $query->whereIn('id', $userAdminDiscipline);
        }

        if ($search) {
            $idList = SearchCache::search(self::$adminRedisTable, $search);
            $query->whereIn('id', $idList);

            if (is_numeric($search)) {
                $query->orWhere('id', (int)$search);
            }

            $recordsFiltered = count($idList);
        } else {
            $recordsFiltered = $recordsTotal;
        }

        $filterResult = $query
            ->offset($start)
            ->take($length)
            ->get();

        $data = [];
        foreach ($filterResult as $discipline) {
            $data[] = [
                $discipline->id,
                $discipline->name,
                $discipline->ects,
                $discipline->recalculation_status,
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
     * @param int $userId
     * @return array
     */
    public function unresolvedDependencies(int $userId) : array
    {
        $dependencyGroups = [];

        $student = Profiles::where('user_id', $userId)
            ->first();
        $specialityDisciplineDependencies = SpecialityDisciplineDependence::where('discipline_id', $this->id)
            ->where('speciality_id', $student->speciality->id);
        foreach ($specialityDisciplineDependencies->get() as $dependency){
            $dependeceDisciplinesIds = [];
            foreach ($dependency->dependenceDisciplines as $dependenceDiscipline){
                $dependeceDisciplinesIds[] = $dependenceDiscipline->id;
            }
            if (!empty($dependeceDisciplinesIds)){
                $dependencyGroups[$dependency->year][] = $dependeceDisciplinesIds;
            }
        }
        return $dependencyGroups;
    }

    public static function getSpecialityExamDisciplines($specialityId)
    {
        return self
            ::select([
                'disciplines.*'
            ])
            ->leftJoin('speciality_discipline', 'disciplines.id', '=', 'speciality_discipline.discipline_id')
            //->leftJoin('students_disciplines', 'students_disciplines.discipline_id', '=', 'disciplines.id')
            //->where('students_disciplines.student_id', Auth::user()->id)
            ->where('speciality_discipline.exam', true)
            ->where('speciality_discipline.speciality_id', $specialityId)
            ->get();
    }

    public static function getLanguageLevel(int $disciplineId) : int
    {
        return self::select('language_level')->where('id', $disciplineId)->first()->language_level;
    }

    public static function getDependencyArray(array $disciplineIds)
    {
        $disciplines = self::select(['id', 'name', 'name_kz', 'name_en'])->whereIn('id', $disciplineIds)->get()->toArray();

        $result = [];
        foreach ($disciplines as $discipline) {
            $result[$discipline['id']] = $discipline;
        }

        return $result;
    }

    /**
     * @param int $disciplineId
     * @return Discipline|null
     */
    public static function getById(int $disciplineId) : ?self
    {
        return self::where('id', $disciplineId)->first();
    }

    /**
     * @param string $syllabusLang
     * @param int $credits
     * @param bool $test1 For test 1
     * @return QuizQuestion[]
     */
    public function getQuizQuestions(string $syllabusLang, int $credits, bool $test1 = false) : array
    {
        $syllabuses = $this
            ->syllabuses()
            ->with([
                'quizeQuestions' => function ($query) {
                    $query->with(['answers' => function ($query1) {
                        $query1->inRandomOrder();
                    }]);
                }
            ])
            ->where('language', $syllabusLang);

        // Test 1
        if ($test1) {
            $syllabuses->where('for_test1', 1);
        }

        $syllabusesArray = $syllabuses->get();

        if ($syllabusesArray->isEmpty()) {
            return [];
        }

        // Test 1
        if ($test1) {
            $maxCount = 10;
        }
        // Exam
        else {
            $maxCount = $credits * 4;
            $maxCount = $maxCount < 20 ? 20 : $maxCount;
        }

        $questions = [];
        for ($i = 0; $i < $maxCount; $i++) {
            foreach ($syllabusesArray as $syllabus) {
                /** @var Syllabus $syllabus */

                if (count($questions) == $maxCount) {
                    break;
                } elseif (isset($syllabus->quizeQuestions[$i])) {
                    $questions[] = $syllabus->quizeQuestions[$i];
                }
            }
        }

        if (!empty($questions) && count($questions) < $maxCount) {
            // Fill by random questions
            do {
                $questions[] = $questions[array_rand($questions)];
            } while(count($questions) < $maxCount);
        }

        return $questions;
    }

    public static function getLocaleNameById(int $id) : string
    {
        $discipline = self::select(['name', 'name_kz', 'name_en'])->where('id', $id)->first();

        $field = Language::getFieldName('name', app()->getLocale());

        return $discipline->$field;
    }

    /**
     * @return array
     */
    public function themeLangs()
    {
        $syllabusList = Syllabus
            ::select(['language'])
            ->where('discipline_id', $this->id)
            ->groupBy('language')
            ->get();

        $result = [];

        foreach ($syllabusList as $syllabus)
        {
            $result[] = $syllabus->language;
        }

        return $result;
    }

    public function isPracticeTime(string $studyForm, int $specialityYear) : bool
    {
        $semester = Semester::inStudyYear($studyForm);

        if (empty($semester)) {
            return false;
        }

        if ($semester == 1) {
            $startDayMonth = $this->practise_1sem_control_start;
            $endDayMonth = $this->practise_1sem_control_end;
        } elseif ($semester == 2) {
            $startDayMonth = $this->practise_2sem_control_start;
            $endDayMonth = $this->practise_2sem_control_end;
        } else {
            return false;
        }

        if (empty($startDayMonth) || empty($endDayMonth)) {
            return false;
        }

        [$day, $month] = explode('.', $startDayMonth);
        $start = Carbon::now();
        $start->day = (int)$day;
        $start->month = (int)$month;

        [$day, $month] = explode('.', $endDayMonth);
        $end = Carbon::now();
        $end->day = (int)$day;
        $end->month = (int)$month;
        if ($end->month < $start->month) {
            $end->addYear(1);
        }

        $now = Carbon::now();

        return $now->greaterThanOrEqualTo($start) && $now->lessThanOrEqualTo($end);
    }


    /**
     * наличие курсовой работы
     * @return bool
     */
    public function hasCoursework()
    {

        $bResponse = false;
        if( !empty(Auth::user()->studentProfile->education_speciality_id) )
        {
            $oSpecialityDiscipline = SpecialityDiscipline::
            where('speciality_id',Auth::user()->studentProfile->education_speciality_id)->
            where('discipline_id',$this->id)->
            first();
            $bResponse = ( !empty($oSpecialityDiscipline) && !empty($oSpecialityDiscipline->has_coursework) ) ? true : false;
        }
        return $bResponse;
    }

    public function hasCourseworkByUserId($id)
    {

        $bResponse = false;
        $userSpecialityId = User::find($id)->studentProfile->education_speciality_id;
        if( !empty($userSpecialityId) )
        {
            $oSpecialityDiscipline = SpecialityDiscipline::
            where('speciality_id', $userSpecialityId)->
            where('discipline_id',$this->id)->
            first();
            $bResponse = ( !empty($oSpecialityDiscipline) && !empty($oSpecialityDiscipline->has_coursework) ) ? true : false;
        }
        return $bResponse;
    }

    public function hasExamByUserId($id)
    {

        $hasExam = false;
        $userSpecialityId = User::find($id)->studentProfile->education_speciality_id;
        if(empty($userSpecialityId) )
        {
            return false;
        }

        $oSpecialityDiscipline = SpecialityDiscipline::
        where('speciality_id', $userSpecialityId)->
        where('discipline_id',$this->id)->
        first();
        $hasExam = ( !empty($oSpecialityDiscipline) && !empty($oSpecialityDiscipline->exam) ) ? true : false;

        return $hasExam;
    }

    public static function getArrayForSelect() : array
    {
        $disciplines = self::select(['id', 'name', 'ects'])->orderBy('name')->get();

        $array = [];
        foreach ($disciplines as $discipline) {
            $array[$discipline->id] = "$discipline->name ($discipline->ects ects) id $discipline->id";
        }

        return $array;
    }

    public function documents()
    {
        return $this->hasMany(DisciplinePracticeDocument::class);
    }

    public static function getRandomPracticeId() : ?int
    {
        $practice = self
            ::select('id')
            ->where('is_practice', true)
            ->inRandomOrder()
            ->first();

        return $practice->id ?? null;
    }

    public static function getRandomDiplomaWorkId() : ?int
    {
        $practice = self
            ::select('id')
            ->where('has_diplomawork', true)
            ->inRandomOrder()
            ->first();

        return $practice->id ?? null;
    }

    public static function getRandomNotPracticeNotDiplomaWorkId() : ?int
    {
        $discipline = self
            ::select('id')
            ->where('is_practice', false)
            ->where('has_diplomawork', false)
            ->inRandomOrder()
            ->first();

        return $discipline->id ?? null;
    }

    public static function getRandomId() : ?int
    {
        $discipline = self
            ::select('id')
            ->inRandomOrder()
            ->first();

        return $discipline->id ?? null;
    }

    public static function getRandomIdNCredit(int $credits) : ?int
    {
        $discipline = self
            ::select('id')
            ->where('ects', $credits)
            ->inRandomOrder()
            ->first();

        return $discipline->id ?? null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function disciplineSemesters()
    {
        return $this->hasMany(DisciplineSemester::class, 'discipline_id', 'id');
    }

    /**
     * @param string $studyForm
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDisciplineSemestersByStudyForm($studyForm = Profiles::EDUCATION_STUDY_FORM_FULLTIME)
    {
        return $this->disciplineSemesters()
            ->where('study_form', $studyForm)
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function specialityDisciplines()
    {
        return $this->hasMany(SpecialityDiscipline::class, 'discipline_id', 'id');
    }

    /**
     * @return array
     */
    public function getStudyGroups()
    {
        $resultGroupList = [];

        $groupList = Profiles
            ::select(['study_groups.id as id', 'study_groups.name as name'])
            ->leftJoin('study_groups', 'study_groups.id', '=', 'profiles.study_group_id')
            ->leftJoin('students_disciplines', 'students_disciplines.student_id', '=', 'profiles.user_id')
            ->where('students_disciplines.discipline_id', $this->id)
            ->groupBy(['study_groups.id', 'study_groups.name'])
            ->get();

        $excList = [];

        foreach ($groupList as $group)
        {
            if ($group->id !== null){
                $excList[] = $group->id;
                $resultGroupList[] = [
                    'id' => $group->id,
                    'name' => $group->name
                ];
            }
        }

        $groupList2 = StudentGroupsSemesters
            ::select(['study_groups.id as id', 'study_groups.name as name'])
            ->leftJoin('study_groups', 'study_groups.id', '=', 'student_groups_semesters.study_group_id')
            ->leftJoin('students_disciplines', 'students_disciplines.student_id', '=', 'student_groups_semesters.user_id')
            ->where('students_disciplines.discipline_id', $this->id)
            ->where('semester', '2019-20.1')
            ->whereNotIn('student_groups_semesters.study_group_id', $excList)
            ->get();

        foreach ($groupList2 as $group)
        {
            if ($group->id !== null){
                $resultGroupList[] = [
                    'id' => $group->id,
                    'name' => $group->name
                ];
            }
        }
        return $resultGroupList;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teachers()
    {
        return $this->belongsToMany(
            User::class,
            'admin_user_discipline',
            'discipline_id',
            'user_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studyGroupTeachers()
    {
        return $this->hasMany(StudentGroupTeacher::class, 'discipline_id', 'id');
    }

    /**
     * @param $query
     * @param string $semester
     * @return mixed
     */
    public function scopeHasSpecialitySemester($query, string $semester)
    {
        return $query->whereHas('specialityDisciplines', function ($query) use ($semester){
            $query->whereHas('semesters', function ($query) use ($semester) {
                $query->where('semester', $semester);
            });
        });
    }

    /**
     * @return array
     */
    public function getStudyGroupsForAssignTeachers()
    {
        $teacherGroupsIds = [];
        $groupsTeachers = [];

        foreach ($this->studyGroupTeachers as $groupTeacher){
            $group = [];
            foreach ($groupTeacher->studyGroups as $studyGroup){
                $teacherGroupsIds[] = $studyGroup->id;
                $group[] = [
                    'id' => $studyGroup->id,
                    'name' => $studyGroup->name,
                ];
            }
            $groupsTeachers[] = [
                'teacher' => $groupTeacher->teacher_id,
                'groups' => $group,
                'checked' => false
            ];
        }
        $groups = collect($this->getStudyGroups())->whereNotIn('id', $teacherGroupsIds);

        foreach ($groups as $group){
            $groupsTeachers[] = [
                'teacher' => null,
                'groups' => [$group],
                'checked' => false
            ];
        }

        return $groupsTeachers;
    }

    public function hasScore($semester)
    {
        $disciplineSemester = $this->disciplineSemesters()
            ->where('semester', $semester)
            ->where('control_form', 'score')
            ->first();

        return isset($disciplineSemester);
    }
}
