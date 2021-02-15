<?php

namespace App;

use App\Models\Speciality\SpecialityDisciplineSemester;
use OwenIt\Auditing\Contracts\Auditable;
use App\Services\{Auth,LanguageService,SearchCache};
use Illuminate\Database\Eloquent\{Collection,Model};
use Illuminate\Support\Facades\{DB,Log};


/**
 * Class Speciality
 * @package App
 *
 * @property int id
 * @property int trend_id
 * @property string $code_char
 * @property string $code
 * @property string $year
 * @property string name
 * @property string name_en
 * @property string name_kz
 * @property string url
 *
 * @property-read bool is_bachelor
 * @property-read bool is_master
 *
 * @property $submodules
 * @property $profiles
 * @property Discipline[] $disciplines
 */
class Speciality extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    const CODE_CHAR_BACHELOR = 'b';
    const CODE_CHAR_MASTER = 'm';
    const URL_DESIGN = 'design_5b';

    protected $table = 'specialities';

    public $fillable = [
        'code_char',
        'code_number',
        'code',
        'year',
        'name',
        'name_en',
        'name_kz',
        'url',
        'trend_id',
        'passing_ent_total',
        'check_ent',
        'check_entrance_test',
        'description',
        'description_kz',
        'description_en',
        'goals',
        'goals_kz',
        'goals_en',
        'qualification_id',
    ];

    private static $adminAjaxColumnList = [
        'id',
        'id',
        'name',
        'id',
        'year'
    ];

    public static $adminRedisTable = 'admin_specialities';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function trend()
    {
        return $this->hasOne(Trend::class, 'id', 'trend_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function qualification()
    {
        return $this->hasOne(TrendQualification::class, 'id', 'qualification_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function modules()
    {
        return $this->belongsToMany(Module::class);
    }

    public function getEntranceTestAttribute()
    {
        return $this->entranceTests->first();
    }

    public function profiles()
    {
        return $this->hasMany(Profiles::class, 'education_speciality_id', 'id');
    }

    public function trajectories()
    {
        return $this->belongsToMany(Trajectory::class);
    }

    public function sector()
    {
        return $this->hasMany('App\SectorSpeciality', 'speciality_id', 'id');
    }

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

    /**
     * @return string
     */
    public function getFullCodeAttribute()
    {
        return strtoupper($this->code_char) . $this->code;
    }

    public function getIsBachelorAttribute() : bool
    {
        return $this->code_char == self::CODE_CHAR_BACHELOR;
    }

    public function getIsMasterAttribute() : bool
    {
        return $this->code_char == self::CODE_CHAR_MASTER;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'speciality_subject',
            'speciality_id',
            'subject_id')
            ->withPivot('ent');
    }

    public function disciplines()
    {
        return $this->belongsToMany(
            Discipline::class,
            'speciality_discipline',
            'speciality_id',
            'discipline_id')
            ->withPivot(['id', 'language_type', 'exam', 'semester', 'has_coursework', 'discipline_cicle', 'mt_tk', 'pressmark', 'control_form', 'verbal_sro', 'sro_hours', 'laboratory_hours', 'practical_hours', 'lecture_hours', 'cloned'])
            ->withTimestamps();
    }

    public function submodules()
    {
        return $this->belongsToMany(
            Submodule::class,
            'speciality_submodule',
            'speciality_id',
            'submodule_id')
            ->withPivot(['language_type', 'pressmark', 'semester', 'discipline_cicle', 'mt_tk', 'control_form', 'verbal_sro', 'sro_hours', 'laboratory_hours', 'practical_hours', 'lecture_hours', 'cloned'])
            ->withTimestamps();
    }

    public function entranceTests()
    {
        return $this->belongsToMany(
            EntranceTest::class,
            'speciality_entrance_test',
            'speciality_id',
            'entrance_test_id')
            ->withTimestamps();
    }

    public function specialityDisciplines()
    {
        return $this->hasMany(SpecialityDiscipline::class, 'speciality_id', 'id');
    }

    /**
     * @param $id
     * @return bool
     */
    public function idInSubjects($id)
    {
        foreach ($this->subjects as $item) {
            if ($item->id == $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function idInDisciplines($id)
    {
        foreach ($this->disciplines as $item) {
            if ($item->id == $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function idInModules($id)
    {
        foreach ($this->modules as $item) {
            if ($item->id == $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function idInSubmodules($id)
    {
        foreach ($this->submodules as $item) {
            if ($item->id == $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $id
     * @return string
     */
    public function getSubjectEntById($id)
    {
        foreach ($this->subjects as $item) {
            if ($item->id == $id) {
                return $item->pivot->ent;
            }
        }

        return '';
    }

    /**
     * @param $subjects
     * @return bool
     */
    public function attachSubjects($subjects)
    {
        $taboIdList = [];
        foreach ($subjects as $item) {
            $taboIdList[] = $item['id'];

            if ($this->idInSubjects($item['id'])) {
                SpecialitySubject
                    ::where('speciality_id', $this->id)
                    ->where('subject_id', $item['id'])
                    ->update([
                        'ent' => $item['ent']
                    ]);
            } else {
                SpecialitySubject
                    ::insert([
                        'speciality_id' => $this->id,
                        'subject_id' => $item['id'],
                        'ent' => $item['ent'] ?? 0,
                        'created_at' => DB::raw('now()'),
                        'updated_at' => DB::raw('now()'),
                    ]);
            }
        }

        SpecialitySubject
            ::where('speciality_id', $this->id)
            ->whereNotIn('subject_id', $taboIdList)
            ->delete();

        return true;
    }

    /**
     * @param $disciplineId
     * @param bool $default
     * @return bool
     */
    public function getDisciplineExam(int $disciplineId, $default = false)
    {
        foreach ($this->disciplines as $discipline) {
            if ($discipline->id == $disciplineId) {
                return $discipline->pivot->exam;
            }
        }

        return $default;
    }

    /**
     * @param $disciplineId
     * @param bool $default
     * @return bool
     */
    public function getDisciplineExam2(int $disciplineId, $default = false)
    {
        if (isset($this->disciplines[$disciplineId])) {
            return $this->disciplines[$disciplineId]->pivot->exam;
        }

        return $default;
    }

    /**
     * @param $disciplineId
     * @param int $default
     * @return int
     */
    public function getDisciplinePressmark($disciplineId, $default = '')
    {
        foreach ($this->disciplines as $discipline) {
            if ($discipline->id == $disciplineId) {
                return $discipline->pivot->pressmark;
            }
        }

        return $default;
    }

    /**
     * @param $disciplineId
     * @param int $default
     * @return int
     */
    public function getDisciplinePressmark2($disciplineId, $default = '')
    {
        if (isset($this->disciplines[$disciplineId])) {
            return $this->disciplines[$disciplineId]->pivot->pressmark;
        }

        return $default;
    }

    public function getDisciplineSemester($disciplineId, $default = 1)
    {
        foreach ($this->disciplines as $discipline) {
            if ($discipline->id == $disciplineId) {
                return $discipline->pivot->semester;
            }
        }

        return $default;
    }

    public function getDisciplineSemester2($disciplineId, $default = 1)
    {
        if (isset($this->disciplines[$disciplineId])) {
            return $this->disciplines[$disciplineId]->pivot->semester;
        }

        return $default;
    }

    /**
     * @param $disciplineId
     * @param bool $default
     * @return bool
     */
    public function getDisciplineHasCoursework($disciplineId, $default = false)
    {
        foreach ($this->disciplines as $discipline) {
            if ($discipline->id == $disciplineId) {
                return $discipline->pivot->has_coursework;
            }
        }

        return $default;
    }

    /**
     * @param $disciplineId
     * @param bool $default
     * @return bool
     */
    public function getDisciplineHasCoursework2(int $disciplineId, $default = false)
    {
        if (isset($this->disciplines[$disciplineId])) {
            return $this->disciplines[$disciplineId]->pivot->has_coursework;
        }

        return $default;
    }

    /**
     * @param $disciplineId
     * @param bool $default
     * @return bool
     */
    public function getDisciplineLangType($disciplineId, $default = 'native')
    {
        foreach ($this->disciplines as $discipline) {
            if ($discipline->id == $disciplineId) {
                return $discipline->pivot->language_type;
            }
        }

        return $default;
    }

    /**
     * @param $disciplineId
     * @param bool $default
     * @return bool
     */
    public function getDisciplineLangType2($disciplineId, $default = 'native')
    {
        if (isset($this->disciplines[$disciplineId])) {
            return $this->disciplines[$disciplineId]->pivot->language_type;
        }

        return $default;
    }

    /**
     * @param $disciplineId
     * @param bool $default
     * @return bool
     */
    public function getDisciplineDisciplineCicle($disciplineId, $default = 'ООД')
    {
        foreach ($this->disciplines as $discipline) {
            if ($discipline->id == $disciplineId) {
                return $discipline->pivot->discipline_cicle;
            }
        }

        return $default;
    }

    /**
     * @param $disciplineId
     * @param bool $default
     * @return bool
     */
    public function getDisciplineDisciplineCicle2($disciplineId, $default = 'ООД')
    {
        if (isset($this->disciplines[$disciplineId])) {
            return $this->disciplines[$disciplineId]->pivot->discipline_cicle;
        }

        return $default;
    }

    /**
     * @param $disciplineId
     * @param bool $default
     * @return bool
     */
    public function getDisciplineMtTk($disciplineId, $default = 'ОК')
    {
        foreach ($this->disciplines as $discipline) {
            if ($discipline->id == $disciplineId) {
                return $discipline->pivot->mt_tk;
            }
        }

        return $default;
    }

  /**
   * @param $disciplineId
   * @param bool $default
   * @return bool
   */
    public function getDisciplineControlForm($disciplineId, $default = 'test')
    {
        foreach ($this->disciplines as $discipline) {
            if ($discipline->id == $disciplineId) {
                return $discipline->pivot->control_form;
            }
        }

        return $default;
    }

    /**
     * @param $disciplineId
     * @param bool $default
     * @return bool
     */
    public function getDisciplineMtTk2($disciplineId, $default = 'ОК')
    {
        if (isset($this->disciplines[$disciplineId])) {
            return $this->disciplines[$disciplineId]->pivot->mt_tk;
        }

        return $default;
    }

    /**
     * @return QuizQuestion[]|bool|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getKgeQuestionList($language)
    {
        $scheme = [];
        $questionsOnCredit = 0;

        if ($this->code_char == self::CODE_CHAR_BACHELOR) {
            $scheme = [
                [
                    'limit' => 3,
                    'answerCount' => 1
                ],
                [
                    'limit' => 2,
                    'answerCount' => 2
                ],
                [
                    'limit' => 1,
                    'answerCount' => 3
                ]
            ];
            $questionsOnCredit = 6;
        } elseif ($this->code_char == self::CODE_CHAR_MASTER) {
            $scheme = [
                [
                    'limit' => 5,
                    'answerCount' => 1
                ],
                [
                    'limit' => 3,
                    'answerCount' => 2
                ],
                [
                    'limit' => 2,
                    'answerCount' => 3
                ]
            ];
            $questionsOnCredit = 10;
        }

        $disciplineList = Discipline
            ::select([
                'disciplines.*',
                'speciality_discipline.language_type as language_type'
            ])
            ->leftJoin('speciality_discipline', 'speciality_discipline.discipline_id', '=', 'disciplines.id')
            ->where('speciality_discipline.speciality_id', $this->id)
            ->where('speciality_discipline.exam', true)
            ->get();

        if (count($disciplineList) == 0) {
            return false;
        }


        $selectedQuestionIdsAll = [];

        foreach ($disciplineList as $discipline) {
            $selectedQuestionIds = [];

            if ($scheme) {
                $syllabusLang = LanguageService::getByType(
                    $discipline->language_type,
                    $language);

                foreach ($scheme as $item) {
                    $questionIds = $discipline->getQuestionIdListByCorrectCount($item['answerCount'], $syllabusLang);
                    $countLimit = $discipline->credits * $item['limit'];
                    shuffle($questionIds);
                    $selectedQuestionIds = array_merge($selectedQuestionIds, array_slice($questionIds, 0, $countLimit));
                }

                $allcountDiscipline = $discipline->credits * $questionsOnCredit;

                if (count($selectedQuestionIds) < $allcountDiscipline) {
                    $questionIds = $discipline->getQuestionIdListByCorrectCount(1, $syllabusLang);
                    shuffle($questionIds);
                    $partCount = $allcountDiscipline - count($selectedQuestionIds);
                    ($discipline->id);
                    (array_slice($questionIds, 0, $partCount));

                    $selectedQuestionIds = array_merge($selectedQuestionIds, array_slice($questionIds, 0, $partCount));
                }
                $selectedQuestionIdsAll = array_merge($selectedQuestionIdsAll, $selectedQuestionIds);
            }
        }

        $allQuestionsModels = QuizQuestion
            ::with([
                'answers' => function ($query) {
                    $query->inRandomOrder();
                }
            ])
            ->whereIn('id', $selectedQuestionIdsAll)
            ->get();

        return $allQuestionsModels;
    }

    /**
     * @return bool
     */
    public function updateStudentDisciplines()
    {
        foreach ($this->profiles as $profile) {
            $disciplineIdList = [];

            // Check
            foreach ($this->disciplines as $discipline) {
                $disciplineIdList[] = $discipline->id;

                $exists = StudentDiscipline::existsByUserAndDiscipline($profile->user_id, $discipline->id);

                // Add new
                if (!$exists) {
                    $studentDiscipline = new StudentDiscipline();
                    $studentDiscipline->discipline_id = $discipline->id;
                    $studentDiscipline->student_id = $profile->user_id;
                    $studentDiscipline->save();
                }
            }

            // Delete deleted from speciality
            StudentDiscipline
                ::where('student_id', $profile->user_id)
                ->where('is_elective', 0)
                ->whereNull('submodule_id')
                ->whereNotIn('discipline_id', $disciplineIdList)
                ->delete();
        }

        return true;
    }

    /**
     * @param string|null $search
     * @param string|null $fullCodeFilter
     * @param string|null $directionFilter
     * @param string|null $yearFilter
     * @param int $start
     * @param int $length
     * @param int $orderColumn
     * @param string $orderDirection
     * @return array
     */
    static function getListForAdmin(?string $search = '', ?string $fullCodeFilter = null, ?string $directionFilter = null, ?string $yearFilter = null, int $start = 0, int $length = 10, int $orderColumn = 0, string $orderDirection = 'asc')
    {
        $orderColumnName = self::$adminAjaxColumnList[$orderColumn] ?? 'id';

        $recordsTotal = SearchCache::totalCount(self::$adminRedisTable);

        $query = self::select(['id', 'trend_id', 'name', 'year'])->with('trend')->orderBy($orderColumnName, $orderDirection);

        // Without filters
        if ( empty($search) && empty($fullCodeFilter) && empty($yearFilter) ) {
            $recordsFiltered = $recordsTotal;
        } else {
            // Full Code filter
            if (!empty($fullCodeFilter)) {
                $query->whereRaw("CONCAT(code_number, code_char, code) = ?", $fullCodeFilter);
            }

            // Direction filter
            if( !empty($directionFilter) ) {
                Log::info('$directionFilter: ' . var_export($directionFilter,true));

                $query->whereRaw("UPPER('name') like '%'" . strtoupper($directionFilter) . "'%'");
                if(is_numeric($directionFilter))
                {
                    $query->orWhere('id', (int)$directionFilter);
                }
            }

            // Year filter
            if (!empty($yearFilter)) {
                $query->where('year', $yearFilter);
            }

            // Search string $search
            if (!empty($search)) {
                // Get ids
                $idList = SearchCache::searchFull(self::$adminRedisTable, $search, 'name');
                $query->whereIn('id', $idList);

                if (is_numeric($search)) {
                    $query->orWhere('id', (int)$search);
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
        foreach ($filterResult as $user) {
            $data[] = [
                $user->id ?? '',
                $user->trend->training_code ?? '',
                $user->name ?? '',
                $user->trend->training_code  ?? ''. ' - ' . (isset($user->trend) ? $user->trend->name : ''),
                $user->year,
                '',
                '<div style="width:100%; text-align: center"><input name="selectSpecialityList" value="' . $user->id . '" type="checkbox" /></div>'
            ];
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    /**
     * Get array of unique full codes
     */
    public static function getUniqueFullCodes() : array
    {
        $codes = Trend::select(['training_code'])->orderBy('training_code')->get();

        $fullCodes = [];
        foreach ($codes as $code) {
            $fullCodes[] = $code->training_code;
        }

        return array_unique($fullCodes);
    }

    /**
     * Get array of unique years
     */
    public static function getUniqueYears() : array
    {
        return self::select(['year'])
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
    }

    public static function getIdsByTrendAndYear(int $trendId, int $year) : array
    {
        return self::select('id')
            ->where([
                ['trend_id', $trendId],
                ['year', $year]
            ])
            ->pluck('id')
            ->toArray();
    }

    public static function getById(int $specialityId)
    {
        return self::where('id', $specialityId)->first();
    }

    public function getSubmoduleField(int $submoduleId, string $field, $default)
    {
        foreach ($this->submodules as $submodule) {
            if ($submodule->id == $submoduleId) {
                return $submodule->pivot->$field;
            }
        }

        return $default;
    }

    public function getSubmoduleSemester(int $submoduleId, $default = 1)
    {
        return $this->getSubmoduleField($submoduleId, 'semester', $default);
    }

    /**
     * @param int $submoduleId
     * @param string $default
     * @return bool
     */
    public function getSubmoduleDisciplineCycle(int $submoduleId, $default = 'ООД')
    {
        return $this->getSubmoduleField($submoduleId, 'discipline_cicle', $default);
    }


    /**
     * @param int $submoduleId
     * @param string $default
     * @return bool
     */
    public function getSubmoduleMtTk(int $submoduleId, $default = 'ОК')
    {
        return $this->getSubmoduleField($submoduleId, 'mt_tk', $default);
    }

    /**
     * @param int $submoduleId
     * @param string $default
     * @return bool
     */
    public function getSubmoduleControlForm(int $submoduleId, $default = 'test')
    {
        return $this->getSubmoduleField($submoduleId, 'control_form', $default);
    }

    /**
     * @param int $submoduleId
     * @param string $default
     * @return bool
     */
    public function getSubmoduleLangType(int $submoduleId, $default = 'native')
    {
        return $this->getSubmoduleField($submoduleId, 'language_type', $default);
    }

    public function getSubmodulePressmark(int $submoduleId, $default = '')
    {
        return $this->getSubmoduleField($submoduleId, 'pressmark', $default);
    }

    public static function getForEdit(int $id) : self
    {
        $speciality = self::with('subjects')
            ->with(['disciplines' => function ($query) {
                $query->with('modules');
            }])
            ->with('modules')
            ->where('id', $id)
            ->first();
        $disciplines = [];
        foreach ($speciality->disciplines as $discipline) {
            $disciplines[] = $discipline;
            
          //  dumpLog((array)$discipline);
            
//          $disciplines[$discipline->id] = $discipline; //Can be dublicates disciplineId.
        }

        $speciality->disciplines = $disciplines;
        
        return $speciality;
    }

    public static function isShaped(int $speciality_id)
    {
        $code = self::select('code', 'code_number')
                    ->where('id' ,$speciality_id)
                    ->where('code_char', self::CODE_CHAR_MASTER)
                    ->first();

        if($code->code_number <= 6) {
            return false;
        }
                       
        return $code->code%2 != 1 ? true: false;
    }

    /**
     * Update Student and submodules links
     * @return bool
     */
    public function updateStudentSubmodules()
    {
        foreach ($this->profiles as $profile) {
            $submoduleIds = [];

            // Check
            foreach ($this->submodules as $submodule) {
                $submoduleIds[] = $submodule->id;

                $exists = StudentSubmodule::existsByUserAndSubmodule($profile->user_id, $submodule->id);
                $hasBoughtDisciplines = StudentDiscipline::hasSubmoduleDisciplines($profile->user_id, $submodule->id);

                // Add new
                if (!$exists && !$hasBoughtDisciplines) {
                    $newLink = new StudentSubmodule();
                    $newLink->submodule_id = $submodule->id;
                    $newLink->student_id = $profile->user_id;
                    $newLink->save();
                }
            }

            // Delete outdated
            StudentSubmodule::where('student_id', $profile->user_id)
                ->whereNotIn('submodule_id', $submoduleIds)
                ->delete();
        }

        return true;
    }

    /**
     * List for elective select
     * @param string $codeChar
     * @param int $currentSpecialityId
     * @param int $year
     * @return mixed
     */
    public static function getElective(string $codeChar, int $currentSpecialityId, int $year) : Collection
    {
        return self::where('code_char', $codeChar)
            ->where('year', $year)
            ->where('id', '!=', $currentSpecialityId)
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getArrayForSelect() : array
    {
        $specialities = self::select(['id', 'name', 'year'])
            ->orderBy('name')
            ->orderBy('year')
            ->get()
            ->toArray();

        $array = [];
        foreach ($specialities as $speciality) {
            $array[$speciality['id']] = "$speciality[name] ($speciality[year])";
        }

        return $array;
    }

    public static function getRandomId(?int $year = null) : ?int
    {
        $rand = self::select('id');

        if ($year) {
            $rand->where('year', $year);
        }

        $speciality = $rand->inRandomOrder()->first();

        return $speciality->id ?? null;
    }

    public function checkDisciplineSemester($disciplineId, $semester)
    {
        $specialityDiscipline = $this->specialityDisciplines()
            ->where('discipline_id', $disciplineId)
            ->first();

        $semester  = $specialityDiscipline->semesters()
            ->where('semester', $semester)
            ->first();
        return isset($semester);
    }
}
