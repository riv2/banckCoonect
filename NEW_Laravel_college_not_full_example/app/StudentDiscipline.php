<?php

namespace App;

use OwenIt\Auditing\Contracts\Auditable;
use App\{Models\StudentDisciplineDay, Models\StudentDisciplineDayLimit, QuizResult, SyllabusTask, SyllabusTaskResult};
use App\Services\{
    Auth,
    StudentRating
};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * @property int id
 * @property int student_id
 * @property int discipline_id
 * @property int week1_result
 * @property int week2_result
 * @property int week3_result
 * @property int week4_result
 * @property int week5_result
 * @property int week6_result
 * @property int week7_result
 * @property int|null test1_result
 * @property int test1_result_points
 * @property string test1_result_letter
 * @property Carbon test1_date
 * @property bool test1_result_trial User has trial attempt
 * @property bool test1_blur
 * @property bool test1_zeroed_by_time
 * @property bool test1_qr_checked
 * @property int test1_max_points
 * @property int week9_result
 * @property int week10_result
 * @property int week11_result
 * @property int week12_result
 * @property int week13_result
 * @property int week14_result
 * @property int week15_result
 * @property int week16_result
 * @property int week17_result
 * @property int week18_result
 * @property int week19_result
 * @property int week20_result
 * @property int|null test_result
 * @property int|null test_result_points
 * @property string|null test_result_letter
 * @property Carbon test_date
 * @property bool test_manual
 * @property bool test_result_trial
 * @property bool test_blur
 * @property bool exam_zeroed_by_time
 * @property bool test_qr_checked
 * @property int test_max_points
 * @property int final_result
 * @property float final_result_points
 * @property float final_result_gpa
 * @property string final_result_letter
 * @property Carbon final_date
 * @property bool final_manual
 * @property string analogue
 * @property string notes
 * @property bool pay_processing
 * @property bool payed
 * @property int payed_credits
 * @property int free_credits
 * @property bool remote_access
 * @property bool corona_distant
 * @property int iteration
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property int syllabus_updated
 * @property int migrated Перезачтена
 * @property int recommended_semester
 * @property string plan_semester
 * @property Carbon plan_semester_date
 * @property int plan_semester_user_id
 * @property bool plan_admin_confirm
 * @property Carbon plan_admin_confirm_date
 * @property int plan_admin_confirm_user_id
 * @property bool plan_student_confirm
 * @property Carbon plan_student_confirm_date
 * @property int at_semester
 * @property int submodule_id
 * @property int is_elective
 * @property int task_result
 * @property int task_result_points
 * @property string|null task_result_letter
 * @property Carbon task_date
 * @property bool task_manual
 * @property bool task_blur
 * @property bool sro_zeroed_by_time
 *
 * @property Discipline discipline
 * @property User user
 * @property QuizResult[] quizeResults
 *
 *
 * @property-read int test1_attempts_count
 * @property-read bool test1_appeal_available
 * @property-read bool test1_available
 * @property-read bool sro_available
 * @property-read bool exam_available
 * @property-read int exam_attempts_count
 * @property-read bool exam_appeal_available
 * @property-read string|null migrated_type
 * @property-read bool is_inherited Унаследнованная дисциплина (перезачтенная или пришла из старой системы)
 * @property-read array dependencies
 * @property-read array plan_confirmed
 *
 * @property string color
 * @property mixed credits
 * @property bool chooseAvailable
 * @property bool buyAvailable
 * @property bool remoteAccessBuyAvailable
 * @property bool payButtonEnabled
 * @property bool payButtonShow
 * @property bool test1ButtonEnabled
 * @property bool test1ButtonShow
 * @property bool SROButtonShow
 * @property bool SROButtonEnabled
 * @property bool test1Available
 * @property bool examButtonEnabled
 * @property bool examButtonShow
 * @property bool test1AppealButtonShow
 * @property bool examAppealButtonShow
 * @property bool syllabusButtonShow
 * @property bool payCancelButtonShow
 */
class StudentDiscipline extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'students_disciplines';

    //protected $fillable = ['id', 'expire_m', 'expire_y', 'owner_name', 'owner_last', 'card_name', 'user_id'];

    const MAX_CREDITS_AT_SEMESTER = 33;
    const MAX_CREDITS_AT_SEMESTER3 = 20;
    const MAX_CHOOSE_CREDITS = 100;
    const MAX_CREDITS_AT_SEMESTER_FOR_REFERENCE = 12;

    const TEST1_MAX_POINTS = 20;
    const TEST1_FREE_ATTEMPTS = 2;
    const TEST1_CORONA_FREE_ATTEMPTS = 5;

    const SRO_MAX_POINTS = 40;

    const EXAM_MAX_POINTS = 40;
    const EXAM_FREE_ATTEMPTS = 2;
    const EXAM_CORONA_FREE_ATTEMPTS = 5;

    const CONTROL_TYPE_TEST1 = 'test1';
    const CONTROL_TYPE_SRO = 'sro';
    const CONTROL_TYPE_EXAM = 'exam';

    const MIGRATED_TYPE_FREE = 'free';
    const MIGRATED_TYPE_NOT_FREE = 'not_free';

    protected $casts = [
        'test1_result_trial' => 'boolean',
        'test1_blur' => 'boolean',
        'test1_zeroed_by_time' => 'boolean',
        'test1_qr_checked' => 'boolean',
        'test_manual' => 'boolean',
        'test_result_trial' => 'boolean',
        'test_blur' => 'boolean',
        'exam_zeroed_by_time' => 'boolean',
        'test_qr_checked' => 'boolean',
        'final_manual' => 'boolean',
        'remote_access' => 'boolean',
        'payed' => 'boolean',
        'is_elective' => 'boolean',
        'plan_admin_confirm' => 'boolean',
        'plan_student_confirm' => 'boolean',
        'task_manual' => 'boolean',
        'task_blur' => 'boolean',
        'sro_zeroed_by_time' => 'boolean',
    ];

    protected $dates = ['created_at', 'updated_at', 'test1_date', 'test_date', 'final_date', 'plan_semester_date', 'plan_admin_confirm_date', 'plan_student_confirm_date'];

    public $examDisabledFor = [16233, 18016, 16088, 11184, 11417, 10961, 11064, 15854, 11259, 15833, 10769];

    public $test1ButtonShow;
    public $SROButtonShow;
    public $examButtonShow;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function discipline()
    {
        return $this->hasOne(Discipline::class, 'id', 'discipline_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'student_id');
    }

    public function quizeResults()
    {
        return $this->hasMany(QuizResult::class, 'student_discipline_id', 'id');
    }

    public function studentProfile()
    {
        return $this->hasOne(Profiles::class, 'id', 'student_id');
    }

    /**
     * @return int
     * @codeCoverageIgnore
     */
    public function getTest1AttemptsCountAttribute() : int
    {
        return QuizResult::test1attemptscount($this->id);
    }

    public function getExamAttemptsCountAttribute() : int
    {
        return QuizResult::examAttemptsCount($this->id);
    }

    public function getExamAvailableAttribute() : bool
    {
        if (in_array($this->student_id, User::RECOVERY_EXCEPTION_USER_LIST) && !$this->payed) {
            return false;
        }

        if (in_array($this->student_id, $this->examDisabledFor)) {
            return false;
        }

        if (
            $this->user->studentProfile->course > 1 &&
            $this->user->studentProfile->education_study_form == Profiles::EDUCATION_STUDY_FORM_FULLTIME &&
            $this->user->bcApplication
        ) {
            return $this->test1_result !== null && $this->task_result !== null;
        }

        if ($this->user->distance_learning) {
            return $this->payed;
        }

        return $this->payed && $this->test1_result !== null && $this->task_result !== null;
    }

    public function getMigratedTypeAttribute() : ?string
    {
        if (!$this->migrated) {
            return null;
        }

        if ($this->payed && !$this->payed_credits) {
            return self::MIGRATED_TYPE_FREE;
        } else {
            return self::MIGRATED_TYPE_NOT_FREE;
        }
    }

    public function getIsInheritedAttribute() : bool
    {
        return $this->final_result !== null && $this->at_semester === null;
    }

    public function getTest1AppealAvailableAttribute() : bool
    {
        if ($this->test1_result === null) {
            return false;
        }

        $hours = QuizResult::hoursFromLastResult($this->id, QuizResult::TYPE_TEST1);

        return $hours !== null && $hours < 24;
    }

    public function getExamAppealAvailableAttribute() : bool
    {
        if ($this->test_result === null) {
            return false;
        }

        $hours = QuizResult::hoursFromLastResult($this->id, QuizResult::TYPE_EXAM);

        return $hours !== null && $hours < 24;
    }

    public function getDependenciesAttribute() : array
    {
        $dependencies = $this->discipline->unresolvedDependencies($this->student_id);

        $dependenceDisciplines = [];
        foreach ($dependencies as $year => $dependencyGroup){
            foreach ($dependencyGroup as $key => $dependencies){
                foreach ($dependencies as $dkey => $disciplineId){
                    $dependenceDisciplines[$year][$key][$dkey] = [
                        'id' => $disciplineId,
                        'name' => Discipline::getLocaleNameById($disciplineId)
                    ];
                }
            }
        }
        return $dependenceDisciplines;
    }

    public function getPlanConfirmedAttribute() : bool
    {
        return $this->plan_student_confirm && $this->plan_admin_confirm;
    }

    public function getTest1AvailableAttribute() : bool
    {
        return $this->payed || $this->payed_credits > 0;
    }

    public function getSroAvailableAttribute() : bool
    {
        return $this->payed || $this->payed_credits > 1;
    }

    public static function getMigratedType(bool $migrated, bool $payed, ?int $payed_credits) : ?string
    {
        if (!$migrated) {
            return null;
        }

        if ($payed && !$payed_credits) {
            return self::MIGRATED_TYPE_FREE;
        } else {
            return self::MIGRATED_TYPE_NOT_FREE;
        }
    }

    /**
     * @param $creditprice
     * @return mixed
     */
    public function getAmount($creditprice)
    {
        return $this->discipline->credits * $creditprice;
    }

    /**
     * @param null $payedCredits
     * @return bool
     */
    public function setPayed($payedCredits = null)
    {
        /*if (!$this->payed_credits) {
            $path = 'student_discipline_credits_limit:' . $this->student_id . ':' . Auth::user()->studentProfile->currentSemester();
            $creditsLimit = Redis::get($path);
            Redis::set($path, $creditsLimit + $this->discipline->ects);
        }*/

        $this->payed_credits += $payedCredits;

        if ($this->payed_credits >= $this->discipline->ects - $this->free_credits) {
            $this->payed = true;
        }

        $this->at_semester = Auth::user()->studentProfile->currentSemester();
        $this->updated_at = date('Y-m-d H:i:s', time());

        return $this->save();
    }

    /**
     * @param $studentId
     * @return self|null
     */
    public static function getPayedCreditSumAtCurrentSemester($studentId)
    {
        $currentSemester = Auth::user()->studentProfile->currentSemester();

        return self::where('student_id', $studentId)
            ->where('at_semester', $currentSemester)
            ->sum('payed_credits');
    }

    /**
     * @param $studentId
     * @param $studentDisciplinesId
     * @return self|null
     */
    static function getDisciplineForPay(int $studentId, int $studentDisciplinesId) : ?self
    {
        return self::with('discipline')
            ->where('student_id', $studentId)
            ->where('students_disciplines.id', $studentDisciplinesId)
            ->first();
    }

    /**
     * @param $valuePercent
     * @param $creditSum
     * @return bool
     */
    public function setTestResult($valuePercent)
    {
        $this->test_result = $valuePercent;
        $this->final_result = $valuePercent;
        //$this->test_result_points = StudentRating::getPoints($valuePercent);
        //$this->test_result_gpi = StudentRating::getDisciplineGpi($valuePercent, $this->discipline->ects);
        $this->test_result_letter = StudentRating::getLetter($valuePercent);
        $this->final_result_letter = $this->test_result_letter;

        //$this->user->updateGpi();

        return $this->save();
    }

    /**
     * @param int $result
     * @param bool $manual
     * @return void
     */
    public function setFinalResult(int $result, bool $manual = false) : void
    {
        $this->final_result = $result;
        $this->final_result_points = StudentRating::getFinalResultPoints($result);
        $this->final_result_gpa = StudentRating::getDisciplineGpa($result, $this->discipline->ects);
        $this->final_result_letter = StudentRating::getLetter($result);
        $this->final_date = Carbon::now();
        $this->final_manual = $manual;
        $this->save();

        $this->user->updateGpa();
    }

    public function hasOpenDisciplinesCount()
    {
        $result = self::whereDoesntHave('quizeResults')
            ->where('discipline_id', $this->discipline_id)
            ->where('student_id', $this->student_id)
            ->where('id', '!=', $this->id)
            ->count();

        return $result;
    }

    public static function hasStudentsDiscipline($disciplineId)
    {
        return (bool)self::where('discipline_id', $disciplineId)->count();
    }

    public static function getStudentsIdsByDisciplinesIds(array $disciplineIds) : array
    {
        return self::select('student_id')
            ->distinct()
            ->whereIn('discipline_id', $disciplineIds)
            ->pluck('student_id')
            ->toArray();
    }

    /**
     * @param int $userId
     * @param int $specialityId
     * @param bool $isElective
     * @return Collection
     */
    public static function getListForStudyPage(int $userId, bool $isElective = false) : Collection
    {
        return self::select([
            'students_disciplines.*',
            'chatter_categories.slug AS forum_url'
        ])
            ->with(['discipline' => function ($query) {
                $query->with('syllabuses');
            }])

            ->leftJoin('chatter_category_discipline', 'chatter_category_discipline.discipline_id', '=', 'students_disciplines.discipline_id')
            ->leftJoin('chatter_categories', 'chatter_category_discipline.chatter_category_id', '=', 'chatter_categories.id')

            ->where('students_disciplines.student_id', $userId)
            ->where('students_disciplines.is_elective', $isElective)
            ->where('students_disciplines.archive', 0)

            ->orderBy('students_disciplines.recommended_semester')
            ->get();
    }

//    public static function getListForStudyPage_old(int $userId, $null)
//    {
//        $a = self::select([
//            'students_disciplines.*',
//            'disciplines.ects AS credits',
//            'disciplines.id AS disciplienID',
//            'chatter_categories.slug AS forum_url',
//            'students_disciplines.migrated'
//        ])
//            ->with(['discipline' => function ($query) {
//                $query->with('syllabuses');
//            }])
//            ->leftJoin('disciplines', 'disciplines.id', '=', 'students_disciplines.discipline_id')
//            ->leftJoin('chatter_category_discipline', 'chatter_category_discipline.discipline_id', '=', 'disciplines.id')
//            ->leftJoin('chatter_categories', 'chatter_category_discipline.chatter_category_id', '=', 'chatter_categories.id')
//            ->where('students_disciplines.student_id', $userId)
//            ->get();
//
//        return $a;
//    }

    /**
     * Set color for Study page
     */
    public function setColor() : void
    {
        if ($this->final_result !== null) {
            $this->color = ($this->final_result >= 50) ? 'success' : 'danger';
        }
        // Fully paid
        elseif ($this->payed == 1) {
            $this->color = 'warning';
        } else {
            $this->color = 'default';
        }
    }

    /**
     * Show links Pay and Partial Pay
     * @param User $user
     */
    public function setBuyAvailable(User $user) : void
    {
        // Migrated not free
        if ($this->migrated_type == self::MIGRATED_TYPE_NOT_FREE && !$this->payed) {
            $this->buyAvailable = true;
            return;
        }

        $this->buyAvailable =
            !$this->payed &&
            $this->plan_admin_confirm &&
            $this->plan_student_confirm &&
            !empty($this->plan_semester) &&
            $user->isBuyingTime($this->plan_semester);
    }

    /**
     * Get link ID
     * @param $studentId
     * @param int $disciplineId
     * @return int|null
     */
    public static function getId(int $studentId, int $disciplineId) : ?int
    {
        $link = self::select('id')
            ->where('student_id', $studentId)
            ->where('discipline_id', $disciplineId)
            ->first();

        return $link->id ?? null;
    }

    public static function add(int $userId, int $disciplineId, int $submoduleId, int $specialityId)
    {
        $link = new self;
        $link->student_id = $userId;
        $link->discipline_id = $disciplineId;
        $link->submodule_id = $submoduleId;
        $link->recommended_semester = SpecialitySubmodule::getSemester($specialityId, $submoduleId);
        $link->save();

        return $link;
    }

    /**
     * Show info and buttons
     */
    public function setChooseAvailable() : void
    {
        $this->chooseAvailable = true ;
//            $this->final_result !== null ||
//            $this->migrated_type == self::MIGRATED_TYPE_NOT_FREE ||
//            $this->payed ||
//            $this->payed_credits > 0 ||
//            $this->plan_semester !== null;
    }

    /**
     * Sorting submodules and disciplines by semester
     * @param Collection $submodules
     * @param Collection|self[] $disciplines
     * @return array
     */
    public static function combineAndSortSubmodulesAndDisciplines(Collection $submodules, Collection $disciplines): array
    {
        $old = [];
        $planned = [];
        $subs = [];
        $future = [];

        foreach ($submodules as $submodule) {
            $subs[] = $submodule;
        }

        foreach ($disciplines as $SD) {
            if (!$SD->plan_semester && ($SD->payed || $SD->payed_credits)) {
                $old[] = $SD;
            } elseif ($SD->plan_semester) {
                $planned[] = $SD;
            }
            else {
                $future[] = $SD;
            }
        }

        // Old
        usort($old, function ($a, $b) {
            if ($a->at_semester == $b->at_semester) {
                return 0;
            }

            return ($a->at_semester < $b->at_semester) ? -1 : 1;
        });

        // Planned
        usort($planned, function ($a, $b) {
            if ($a->plan_semester == $b->plan_semester) {
                return 0;
            }

            return ($a->plan_semester < $b->plan_semester) ? -1 : 1;
        });

        usort($future, function ($a, $b) {
            if ($a->at_semester == $b->at_semester) {
                return 0;
            }

            return ($a->at_semester < $b->at_semester) ? -1 : 1;
        });

        return array_merge($subs, $old, $planned, $future);
    }

    /**
     * Check choose and buying available
     * @param User $user
     * @param array|Collection $SDs
     * @param int $semester Speciality semester
     * @param string $currentSemesterString
     * @return array
     */
    public static function checkSubmodulesAndDisciplinesForAvailable(
        User $user,
        Iterable $SDs,
        string $currentSemesterString
    )
    {
        $creditsSum = 0;

        // Already bought credits
        $boughtDisciplinesCredits = self::getBoughtDisciplinesCredits($user->id, $user->studentProfile->currentSemester());

        // Appeals
        $appealsSDIdsTest1 = Appeal::getSDIDsTest1($user->id);
        $appealsSDIdsExam = Appeal::getSDIDsExam($user->id);

        $cancelPayDisciplineIds = DisciplinePayCancel::getDisciplineArray($user->id);

        foreach ($SDs as $SD) {
            // Discipline
            if (class_basename($SD) == 'StudentDiscipline') {
                /** @var StudentDiscipline $SD */

                $SD->setColor();

                // Show info and buttons
                $SD->setChooseAvailable();

                if ($SD->chooseAvailable) {
                    // Show links 'Pay' and 'Partial Pay'
                    $SD->setBuyAvailable($user);

                    // Show link 'Buy remote access'
                    $SD->setRemoteAccessBuyAvailable($user->free_remote_access, $currentSemesterString);

                    // Show pay button
                    $SD->setPayButtonShow();

                    // Pay button enabled
                    $SD->setPayButtonEnabled();

                    // Show Test1 button
                    $SD->setTest1ButtonShow($user);

                    // Test1 button enabled
                    $SD->setTest1ButtonEnabled();

                    // Test1 Appeal
                    $SD->setTest1AppealShow($appealsSDIdsTest1);

                    // Show SRO button
                    $SD->setSROButtonShow($user);

                    // SRO button enabled
                    $SD->setSROButtonEnabled();

                    // Show Exam button
                    $SD->setExamButtonShow($user);

                    // Exam button enabled
                    $SD->setExamButtonEnabled();

                    // Exam Appeal
                    $SD->setExamAppealShow($appealsSDIdsExam);

                    // Show Syllabus button
                    $SD->setSyllabusButtonShow($user);

                    // Show PayCancel button
                    $SD->setPayCancelButtonShow($user, $cancelPayDisciplineIds);
                }
            }
            // Submodule
            elseif (class_basename($SD) == 'StudentSubmodule') {
                /** @var StudentSubmodule $SD */
                $SD->setChooseAvailable($creditsSum, $user->studentProfile->currentSemester(), $user->studentProfile->category);

                $SD->setBuyAvailable($SD, $boughtDisciplinesCredits, $user->semester_credits_limit);
            } else {
                abort(500, 'Wrong discipline class');
            }
        }

        return $SDs;
    }

    /**
     * Sum of disciplines ects
     * @param int $userId
     * @param int $semester
     * @return int
     */
    public static function getBoughtDisciplinesCredits(int $userId, int $semester) : int
    {
        $credits = 0;

        $boughtDisciplines = self::select(['id', 'discipline_id'])
            ->where('student_id', $userId)
            ->where('at_semester', $semester)
            ->where('payed_credits', '>', 0)
            ->with('discipline:id,is_practice,ects')
            ->get();

        foreach ($boughtDisciplines as $studentDiscipline) {
            if (!$studentDiscipline->discipline->is_practice) {
                $credits += $studentDiscipline->discipline->ects;
            }
        }

        return $credits;
    }

    /**
     * Sum of bought credits on semester
     * @param int $userId
     * @param int $semester
     * @return int
     */
    public static function getBoughtCredits($userId, $semester) : int
    {
        return self::where('student_id', $userId)
            ->where('at_semester', $semester)
            ->where('payed_credits', '>', 0)
            ->sum('payed_credits');
    }

    public static function getDataForStudyPage(int $userId, bool $isElective) : Collection
    {
        // Get user's disciplines
        $SDs = self::getListForStudyPage($userId, $isElective);

        if ($SDs->isNotEmpty()) {
            foreach ($SDs as $SD) {
                /** @var self $SD */

                // For new students
                if (Auth::user()->speciality_admission_year >= 2019) {
                    $depDisciplines = $SD->discipline->unresolvedDependencies($userId);
                    $depDisciplines = $depDisciplines[Carbon::now()->year] ?? null;

                    $SD->discipline->depWithoutResult = $depDisciplines;
                } else {
                    $SD->discipline->depWithoutResult = [];
                }

                if (empty($SD->recommended_semester) && !empty($SD->submodule_semester)) {
                    $SD->recommended_semester = $SD->submodule_semester;
                }
            }

            // For new students
            if (Auth::user()->speciality_admission_year >= 2019) {
                self::setDependenciesNames($SDs);
            }
        }

        return $SDs;
    }

    private static function setDependenciesNames(Collection $studentDisciplines)
    {
        $disciplineIds = [];
        foreach ($studentDisciplines as $studentDiscipline) {
            if (!empty($studentDiscipline->discipline->depWithoutResult)) {
                $disciplineIds[] = $studentDiscipline->discipline->depWithoutResult;
            }
        }
        $disciplineIds = array_unique(array_flatten($disciplineIds));

        $names = Discipline::getDependencyArray($disciplineIds);

        foreach ($studentDisciplines as $studentDiscipline) {
            if (!empty($studentDiscipline->discipline->depWithoutResult)) {
                foreach ($studentDiscipline->discipline->depWithoutResult as $gkey => $group) {
                    $disciplineNames = [];
                    foreach ($group as $dkey => $disciplineId) {
                        if(is_numeric($disciplineId) && !empty($names[$disciplineId]) ){
                            $disciplineNames[$disciplineId] = $names[$disciplineId];
                        }
                    }
                    $studentDiscipline->discipline->depWithoutResult[$gkey] = $disciplineNames;
                }
            }
        }
    }

    /**
     * Add disciplines when elective speciality choose
     * @param int $userId
     * @param int $electiveSpecialityId
     */
    public static function addElectiveDisciplines(int $userId, int $electiveSpecialityId) : void
    {
        $currentDisciplinesIds = self::getDisciplineIds($userId);
        $electiveDisciplineIds = SpecialityDiscipline::getDisciplineIdsExcludingIds($electiveSpecialityId, $currentDisciplinesIds);

        foreach ($electiveDisciplineIds as $electiveDisciplineId) {
            $studentDiscipline = new self;
            $studentDiscipline->discipline_id = $electiveDisciplineId;
            $studentDiscipline->student_id = $userId;
            $studentDiscipline->is_elective = 1;
            $studentDiscipline->save();
        }
    }

    public static function getDisciplineIds(int $userId) : array
    {
        return self::select(['discipline_id'])->where('student_id', $userId)->pluck('discipline_id')->toArray();
    }

    /**
     * @param int $studentId
     * @param int $disciplineId
     * @return self|null
     */
    public static function getOne(int $studentId, int $disciplineId) : ?self
    {
        return self::where('student_id', $studentId)
            ->where('discipline_id', $disciplineId)
            ->first();
    }

    public static function getOneOrderByPayedCredits(int $studentId, int $disciplineId) : ?self
    {
        return self::where('student_id', $studentId)
            ->where('discipline_id', $disciplineId)
            ->orderBy('payed_credits', 'desc')
            ->first();
    }

    public static function getCreditsCountForBuy(?int $price, ?int $payedCredits, ?int $freeCredits) : int
    {
        if ($payedCredits >= $price - $freeCredits) {
            return 0;
        }

        return $price - $payedCredits - $freeCredits;
    }

    public static function existsByUserAndDiscipline(int $userId, int $disciplineId) : bool
    {
        return self::where('discipline_id', $disciplineId)
        ->where('student_id', $userId)
        ->exists();
    }

    /**
     * Has one SD with final result from array $disciplineIds
     * @param int $userId
     * @param array $disciplineIds
     * @return bool
     */
    public static function hasOneWithResult(int $userId, array $disciplineIds) : bool
    {
        $count = self::where('student_id', $userId)
            ->whereNotNull('final_result')
            ->whereIn('discipline_id', $disciplineIds)
            ->count();

        return $count > 0;
    }

    public static function allowTest1InClassroom(int $userId, int $disciplineId) : void
    {
        self::where('student_id', $userId)
            ->where('discipline_id', $disciplineId)
            ->update(['test1_qr_checked' => true]);
    }

    public static function allowExamInClassroom(int $userId, int $disciplineId) : void
    {
        self::where('student_id', $userId)
            ->where('discipline_id', $disciplineId)
            ->update(['test_qr_checked' => true]);
    }

    /**
     * Set best result
     * @return bool
     */
    public function setTest1Result() : bool
    {
        $result = QuizResult::getBestTest1($this->id);

        $this->test1_result = $result->value;
        $this->test1_result_points = $result->points;
        $this->test1_result_letter = $result->letter;
        $this->test1_date = $result->created_at->timestamp;
        $this->test1_blur = $result->blur;
        $this->test1_zeroed_by_time = false;
        $this->test1_result_trial = false;
        $this->test1_qr_checked = false;

        return $this->save();
    }

    /**
     * Set best result
     * @return bool
     */
    public function setExamResult()
    {
        $result = QuizResult::getBestExam($this->id);

        $this->test_result = $result->value ?? 0;
        $this->test_result_points = $result->points ?? 0;
        $this->test_result_letter = $result->letter ?? '';
        $this->test_date = $result->created_at->timestamp ?? date('Y-m-d H:i:s');
        $this->test_manual = false;
        $this->test_result_trial = false;
        $this->test_blur = $result->blur ?? 0;
        $this->exam_zeroed_by_time = false;
        $this->test_qr_checked = false;

        return $this->save();
    }

    public static function setPayProcessing(int $id, bool $value)
    {
        self::where('id', $id)->update(['pay_processing' => $value]);
    }

    /**
     * There are submodule's disciplines
     * @param $userId
     * @param $submoduleId
     * @return bool
     */
    public static function hasSubmoduleDisciplines(int $userId, int $submoduleId) : bool
    {
        return self::where('student_id', $userId)
            ->where('submodule_id', $submoduleId)
            ->exists();
    }

    public function getLanguageType($specialityId) : string
    {
        // Not from submodule
        if (empty($this->submodule_id)) {
            return SpecialityDiscipline::getLanguageType($specialityId, $this->discipline_id);
        }
        // From submodule
        else {
            return SpecialitySubmodule::getLanguageType($specialityId, $this->submodule_id);
        }
    }

    public static function getForPay(int $userId, int $disciplineId)
    {
        $studentsDiscipline = new self;
        $studentsDiscipline->discipline_id = $disciplineId;
        $studentsDiscipline->student_id = $userId;
        $studentsDiscipline->payed = false;
        $studentsDiscipline->payed_credits = 0;
        $studentsDiscipline->free_credits = 0;

        return $studentsDiscipline;
    }

    public function getCreditsForFullBuy() : int
    {
        $price = $this->discipline->ects ?? 0;
        $payedCredits = $this->payed_credits ?? 0;
        $freeCredits = $this->free_credits ?? 0;

        if ($payedCredits >= $price - $freeCredits) {
            return 0;
        }

        return $price - $payedCredits - $freeCredits;
    }

    public function getCreditsForPartialBuy(int $credits) : int
    {
        $maxCredits = $this->getCreditsForFullBuy();

        if ($credits > $maxCredits) {
            return $maxCredits;
        }

        return $credits;
    }

    public function checkValidQuizPay() : bool
    {
        $docPayCount = PayDocumentStudentDiscipline
            ::leftJoin('pay_documents', 'pay_documents.id', '=', 'pay_documents_student_disciplines.pay_document_id')
            ->where('pay_documents.type', PayDocument::TYPE_RETAKE_TEST)
            ->where('pay_documents_student_disciplines.student_discipline_id', $this->id)
            ->count();

        return count($this->quizeResults) <= 1 ? true : (count($this->quizeResults) - 1) <= $docPayCount;
    }

    public function hasTest1Attempt() : bool
    {
        if ($this->test1_attempts_count < self::TEST1_FREE_ATTEMPTS) {
            return true;
        }

        return $this->test1_result_trial ?? false;
    }

    public function isTest1PaidAttempt() : bool
    {
        return $this->test1_attempts_count >= StudentDiscipline::TEST1_FREE_ATTEMPTS;
    }

    public function isExamPaidAttempt() : bool
    {
        return $this->exam_attempts_count >= StudentDiscipline::EXAM_FREE_ATTEMPTS;
    }

    public function hasExamAttempt() : bool
    {
        if ($this->exam_attempts_count < self::EXAM_FREE_ATTEMPTS) {
            return true;
        }

        return $this->test_result_trial;
    }

    /**
     * Access to remote access buy button on Study page
     * @param bool $freeRemoteAccess
     * @param string $currentSemester
     * @return void
     */
    public function setRemoteAccessBuyAvailable(bool $freeRemoteAccess, string $currentSemester) : void
    {
        Semester::checkStringSemester($currentSemester);

        $this->remoteAccessBuyAvailable =
            !$this->discipline->is_practice &&
            $this->payed_credits > 0 &&
            !$this->remote_access &&
            !$freeRemoteAccess &&
            $this->isPlannedToSemester($currentSemester) &&
            !in_array($this->student_id, $this->examDisabledFor);
    }

    /**
     * Show pay button on Study page
     * @return void
     */
    public function setPayButtonShow() : void
    {
        $this->payButtonShow = $this->buyAvailable || $this->remoteAccessBuyAvailable;
    }

    /**
     * Pay button enabled on Study page
     * @return void
     */
    public function setPayButtonEnabled()
    {
        $this->payButtonEnabled = !$this->pay_processing;
    }

    /**
     * Show Test1 button on Study page
     * @param User $user
     * @return void
     */
    public function setTest1ButtonShow(User $user) : void
    {
        $this->test1ButtonShow =
            in_array($user->studentProfile->education_study_form, [
                Profiles::EDUCATION_STUDY_FORM_FULLTIME,
                Profiles::EDUCATION_STUDY_FORM_ONLINE,
                Profiles::EDUCATION_STUDY_FORM_EVENING,
                Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL,
            ]) &&
            !$this->discipline->is_practice &&
            !$this->discipline->has_diplomawork &&
            $this->plan_semester !== null &&
            (
                $user->isTest1Time($this->plan_semester) ||
                $user->isTest1RetakeTime($this->plan_semester)
            );
    }

    /**
     * Test1 button enabled on Study page
     * @return void
     */
    public function setTest1ButtonEnabled() : void
    {
        $this->test1ButtonEnabled = $this->test1_available;
    }

    /**
     * Test1 button enabled on Study page
     * @return void
     */
    public function setSROButtonEnabled() : void
    {
        $this->SROButtonEnabled = $this->sro_available;
    }

    public static function setDisciplineToStudents(Collection $studentProfiles, int $disciplineId) : void
    {
        foreach ($studentProfiles as $studentProfile) {
            $studentProfile->studentDiscipline = self::getOne($studentProfile->user_id, $disciplineId);
        }
    }

    public static function setManualResultAccess(Collection $studentProfiles, int $teacherId) : void
    {
        foreach ($studentProfiles as $studentProfile) {
            /** @var Profiles $studentProfile */

            if (empty($studentProfile->studentDiscipline)) {
                continue;
            }

            $QRsCount = StudentCheckin::countByTeacher($studentProfile->user_id, $teacherId);
            $studentProfile->studentDiscipline->QRsCount = $QRsCount;

            // Practice
            if ($studentProfile->studentDiscipline->discipline->is_practice) {
                // SRO
                $studentProfile->studentDiscipline->SROAccess = false;

                // Exam
                $minExamQRCount = $studentProfile->studentDiscipline->minExamQRCount = 1;
                $isPracticeTime = $studentProfile->studentDiscipline->discipline->isPracticeTime($studentProfile->education_study_form, $studentProfile->speciality->year);
                $studentProfile->studentDiscipline->manualExamAvailable = $isPracticeTime;

                $studentProfile->studentDiscipline->manualExamAccess = (
                    $isPracticeTime &&
//                    $QRsCount >= $minExamQRCount &&
                    $studentProfile->studentDiscipline->payed_credits > 0
                );
            } else {
                // SRO
                $minSROQRCount = $studentProfile->studentDiscipline->minSROQRCount = 1;
                $isSROTime = $studentProfile->isSROTime();
                $isSROVerbal = $studentProfile->studentDiscipline->discipline->verbal_sro;

                $studentProfile->studentDiscipline->manualSROAvailable = $isSROVerbal && $isSROTime;

                $studentProfile->studentDiscipline->manualSROAccess = (
                    $isSROVerbal &&
                    $isSROTime &&
                    //$QRsCount >= $minSROQRCount &&
                    $studentProfile->studentDiscipline->payed_credits > 1
                );

                // Exam
                $minExamQRCount = $studentProfile->studentDiscipline->minExamQRCount = 1;
                $isExamTime = $studentProfile->isExamTime();
                $isTraditionalForm = $studentProfile->studentDiscipline->discipline->control_form == Discipline::CONTROL_FORM_TRADITIONAL;
                $studentProfile->studentDiscipline->manualExamAvailable = $isTraditionalForm && $isExamTime;

                $studentProfile->studentDiscipline->manualExamAccess = (
                    $isTraditionalForm &&
                    $isExamTime &&
                    $QRsCount >= $minExamQRCount &&
                    $studentProfile->studentDiscipline->payed_credits > 0
                );
            }
        }
    }

    public static function setRatingByDays(Collection $studentProfiles, int $disciplineId) : void
    {
        foreach ($studentProfiles as $studentProfile) {

            $dayRatingList = StudentDisciplineDay
                ::where('user_id', $studentProfile->user_id)
                ->where('discipline_id', $disciplineId)
                ->where('semester', Semester::current($studentProfile->education_study_form))
                ->get();

            $ratingList = [];

            foreach ($dayRatingList as $dayRating)
            {
                $ratingList[$dayRating->day_num] = $dayRating->rating;
            }

            $studentProfile->dayRatingList = $ratingList;
        }
    }

    public function calculateFinalResult() : void
    {
        // Distance learning
        if ($this->user->distance_learning) {
            $test1Result = $this->randomResult($this->test_result, 5);
            $taskResult = $this->randomResult($this->test_result, 3);

            // Test 1
            $this->test1_result = $test1Result;
            $this->test1_result_points = self::getTest1ResultPoints($test1Result);
            $this->test1_result_letter = StudentRating::getLetter($test1Result);

            // SRO
            $this->task_result = $taskResult;
            $this->task_result_points = self::getSROResultPoints($taskResult);
            $this->task_result_letter = StudentRating::getLetter($taskResult);
            $this->task_manual = false;

            $this->save();
        }

        $dailyRating = $this->calculateDailyRating();
        $result = $this->test1_result_points + $this->task_result_points + $this->test_result_points + $dailyRating;

        $this->setFinalResult($result);
    }

    /**
     * SUM of all finished disciplines credits
     * @param int $userId
     * @return int
     */
    public static function getFinishedDisciplinesCreditsSum(int $userId) : int
    {
        $studentDiscipline = self
            ::select(DB::raw('SUM(`disciplines`.`ects`) AS `credits_sum`'))
            ->join('disciplines', 'disciplines.id', '=', 'students_disciplines.discipline_id')
            ->where('students_disciplines.student_id', $userId)
            ->whereNotNull('students_disciplines.final_result')
            ->first();

        return $studentDiscipline->credits_sum ?? 0;
    }

    public function setExamResultManual(int $value) : bool
    {
        $this->test_result = $value;
        $this->test_result_points = self::getExamResultPoints($value);
        $this->test_result_letter = StudentRating::getLetter($value);
        $this->test_date = Carbon::now();
        $this->test_manual = true;
        $this->test_blur = 0;

        return $this->save();
    }

    public function setSROResultManual(int $value) : bool
    {
        $this->task_result = $value;
        $this->task_result_points = self::getSROResultPoints($value);
        $this->task_result_letter = StudentRating::getLetter($value);
        $this->task_date = Carbon::now();
        $this->task_manual = true;
        $this->task_blur = 0;

        return $this->save();
    }

    /**
     * Временное решение для комиссии
     * @deprecated
     * @param int $value
     * @return bool
     */
//    public function setTest1ResultManual(int $value) : bool
//    {
//        $this->test1_result = $value;
//        $this->test1_result_points = self::getTest1ResultPoints($value);
//        $this->test1_result_letter = StudentRating::getLetter($value);
//        $this->test1_date = Carbon::now();
////        $this->test1_manual = true;
//        $this->test1_blur = 0;
//
//        return $this->save();
//    }

    public function setFinalResultManual(int $result) : void
    {
        $this->setFinalResult($result, true);
    }

    public static function resultClean(int $result) : int
    {
        if ($result < 0) {
            return 0;
        } elseif ($result > 100) {
            return 100;
        } else {
            return (int)$result;
        }
    }

    public static function getTest1ResultPoints(int $result) : int
    {
        return round(StudentDiscipline::TEST1_MAX_POINTS * ($result / 100));
    }

    public static function getSROResultPoints(int $result) : int
    {
        return round(StudentDiscipline::SRO_MAX_POINTS * ($result / 100));
    }

    public static function getExamResultPoints(int $result) : int
    {
        return round(StudentDiscipline::EXAM_MAX_POINTS * ($result / 100));
    }

    /**
     * Show SRO button on Study page
     * @param User $user
     * @return void
     */
    public function setSROButtonShow(User $user) : void
    {
        $this->SROButtonShow = (
//            in_array($user->studentProfile->education_study_form, [
//                Profiles::EDUCATION_STUDY_FORM_FULLTIME,
//                Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL,
//                Profiles::EDUCATION_STUDY_FORM_ONLINE,
//                Profiles::EDUCATION_STUDY_FORM_EVENING
//            ]) &&
            !$this->discipline->is_practice &&
            !$this->discipline->has_diplomawork &&
            $this->plan_semester !== null &&
            (
                $user->isSROTime($this->plan_semester) ||
                $user->isSRORetakeTime($this->plan_semester)
            )
        );
    }

    /**
     * @param $iUserId
     * @param $iDisciplineId
     */
    public static function setSROResult($iUserId,$iDisciplineId)
    {

        if( !empty($iUserId) && !empty($iDisciplineId) )
        {

            // получаем список результатов для текущего юзера по текущей дисциплине
            // берем max баллы по каждому заданию
            $oSyllabusTaskResult = DB::select("select st.id as 'syllabus_task_id',
                (
                  select id
                  from syllabus_task_result
                  where task_id = st.id
                  and user_id = ".$iUserId."
                  order by points desc
                  limit 1
                ) as 'result_id'
                from syllabus_task as st
                where st.discipline_id = ".$iDisciplineId);


            //Log::info('$oSyllabusTaskResult' . var_export($oSyllabusTaskResult,true));

            $iPoints  = 0;
            $iPercent = 0;
            // достаем результаты студика по дисциплине
            if( !empty($oSyllabusTaskResult) && ( count($oSyllabusTaskResult) > 0 ) )
            {
                foreach( $oSyllabusTaskResult as $itemSTR )
                {
                    if( !empty($itemSTR->result_id) )
                    {
                        // получаем тек баллы студика по заданию
                        $iCurPoint = SyllabusTaskResult::recalculationData( $itemSTR->result_id, $itemSTR->syllabus_task_id );
                        if( !empty($iCurPoint) && ( ($iPoints + $iCurPoint) <= 20) ){ $iPoints += $iCurPoint; }
                    }
                }
            }

            //Log::info('$iPoints: ' . var_export($iPoints,true));

            $oStudentsDisciplines = self::
            where('discipline_id',$iDisciplineId)->
            where('student_id',$iUserId)->
            first();


            if( !empty($oStudentsDisciplines) && ($iPoints > 0) && ($iPoints < 21) )
            {

                // get percent
                $iPercent = intval( ($iPoints * 100) / 20 );

                //Log::info('$iPercent: ' . var_export($iPercent,true));

                // проверяем текущий результат чтобы не записать худший
                //if( ($iPoints > $oStudentsDisciplines->task_result_points) || ($oStudentsDisciplines->task_result_points > 20) )

                $oStudentsDisciplines->task_result = $iPercent;
                $oStudentsDisciplines->task_result_points = $iPoints;
                $oStudentsDisciplines->task_result_letter = StudentRating::getLetter($iPercent);
                $oStudentsDisciplines->task_date = date('Y-m-d H:i:s');
                $oStudentsDisciplines->task_manual = false;
                $oStudentsDisciplines->task_blur = 0;
                $oStudentsDisciplines->save();

                // вызываем общий пересчет
                if( ($oStudentsDisciplines->test1_result !== null) && ($oStudentsDisciplines->test_result !== null) &&
                    ($oStudentsDisciplines->final_result !== null)
                )
                {
                    $oStudentsDisciplines->calculateFinalResult();
                }
            }

        }
    }

    public static function getByStudentIdsAndDisciplineId(array $studentIds, int $disciplineId) : Collection
    {
        return self
            ::where('discipline_id', $disciplineId)
            ->whereIn('student_id', $studentIds)
            ->whereNotNull('final_result')
            ->get();
    }

    public static function getResultsCounts(Collection $studentDisciplines) : array
    {
        $counts = [
            'a' => 0,
            'b' => 0,
            'cd' => 0,
            'f' => 0
        ];

        foreach ($studentDisciplines as $studentDiscipline) {
            /** @var $studentDiscipline self */
            if (in_array($studentDiscipline->final_result_letter, ['A', 'A-'])) {
                $counts['a']++;
            } elseif (in_array($studentDiscipline->final_result_letter, ['B+', 'B', 'B-'])) {
                $counts['b']++;
            } elseif (in_array($studentDiscipline->final_result_letter, ['C+', 'C', 'C-', 'D+', 'D-'])) {
                $counts['cd']++;
            } elseif (in_array($studentDiscipline->final_result_letter, ['FX', 'F'])) {
                $counts['f']++;
            }
        }

        return $counts;
    }

    public static function getArrayForExamSheet(Collection $studentDisciplines, $migrated = null, int $i = 0) : array
    {
        $result = [];

        foreach ($studentDisciplines as $SD) {
            /** @var self $SD */

            if ($migrated !== null and $SD->migrated != $migrated) {
                continue;
            }

            $i++;

            if ($migrated !== null && $SD->migrated) {
                $data = ['migrated_user_n' => $i];
            } else {
                $data = ['user_n' => $i];
            }

            $result[] = array_merge($data, [
                'user_fio' => $SD->user->studentProfile->fio,
                'user_spec_code' => $SD->user->studentProfile->speciality->code,
                'user_id' => $SD->student_id,
                'user_t1_char' => $SD->test1_result_letter,
                'user_t1_points' => ($SD->test1_result !== null ? StudentRating::getFinalResultPoints($SD->test1_result) : ''),
                'user_exam_char' => $SD->test_result_letter,
                'user_exam_points' => ($SD->test_result !== null ? StudentRating::getFinalResultPoints($SD->test_result) : ''),
                'user_final_char' => $SD->final_result_letter,
                'user_final_pionts' => $SD->final_result_points
            ]);
        }

        return $result;
    }

    private function randomResult(int $examResult, int $delta) : int
    {
        $result = rand($examResult - $delta, $examResult + $delta);

        $result = ($result < 0) ? 0 : $result;
        $result = ($result > 100) ? 100 : $result;

        return $result;
    }

    public function setTest1Trial() : void
    {
        $this->test1_result_trial = true;
        $this->test1_qr_checked = false;
        $this->save();
    }

    public function setExamTrial() : void
    {
        $this->test_result_trial = true;
        $this->test_qr_checked = false;
        $this->save();
    }

    public function setRemoteAccess() : void
    {
        $this->remote_access = true;
        $this->save();
    }

    public function setTest1AppealShow(array $SDIds) : void
    {
        $this->test1AppealButtonShow = $this->test1_appeal_available || in_array($this->id, $SDIds);
    }

    public function setExamAppealShow(array $SDIds) : void
    {
        $this->examAppealButtonShow = $this->exam_appeal_available || in_array($this->id, $SDIds);
    }

    public function getTestLanguage(Profiles $studentProfile) : string
    {
        return ($this->discipline->tests_lang_invert) ? $studentProfile->second_language : $studentProfile->native_language;
    }

    public static function makePlan(int $userId, string $semester, int $adminId) : void
    {
        Semester::checkStringSemester($semester);

        $user = User::where('id', $userId)->first();

        if (empty($user)) {
            return;
        }

        [$year, $semesterNumber] = self::explodeSemester($semester);

        // 1st or 2nd semester in year
        if (in_array($semesterNumber, [1, 2])) {
            $specialitySemester = Semester::byStringSemester($user->speciality_admission_year, $semester);

            if (empty($specialitySemester)) {
                return;
            }

            $credits = self::plannedDisciplinesCredits($userId, $semester);
            $creditsLimit = $user->semester_credits_limit;

            // 1. Set recommended to plan
            $recommendedSDs = self::getRecommended($userId, $specialitySemester);
            $credits = self::addToPlan($recommendedSDs, $semester, $credits, $creditsLimit, $adminId);

            // Limit reached
            if ($credits >= $creditsLimit) {
                return;
            }

            // 2. Set missed to plan
            $missedSDs = self::getMissed($userId, $specialitySemester);
            $credits = self::addToPlan($missedSDs, $semester, $credits, $creditsLimit, $adminId);

            // Limit reached
            if ($credits >= $creditsLimit) {
                return;
            }

            // 3. Set next recommended to plan
            $nextSDs = self::getNextRecommended($userId, $specialitySemester);
            self::addToPlan($nextSDs, $semester, $credits, $creditsLimit, $adminId);
        }
        // 3rd semester
        elseif ($semesterNumber == 3) {
            $secondSemesterString = "$year.2";

            $specialitySemester = Semester::byStringSemester($user->speciality_admission_year, $secondSemesterString);

            if (empty($specialitySemester)) {
                return;
            }

            $credits = self::plannedDisciplinesCredits($userId, $semester);
            $creditsLimit = self::MAX_CREDITS_AT_SEMESTER3;

            // 1. Set missed before second semester to plan
            $missedSDs = self::getMissed($userId, $secondSemesterString);
            $credits = self::addToPlan($missedSDs, $semester, $credits, $creditsLimit, $adminId);

            // Limit reached
            if ($credits >= $creditsLimit) {
                return;
            }

            // 2. Set recommended to second semester to plan
            $recommendedSDs = self::getRecommended($userId, $secondSemesterString);
            $credits = self::addToPlan($recommendedSDs, $semester, $credits, $creditsLimit, $adminId);

            // Limit reached
            if ($credits >= $creditsLimit) {
                return;
            }

            // 3. Set next recommended to plan
            $nextSDs = self::getNextRecommended($userId, $secondSemesterString);
            self::addToPlan($nextSDs, $semester, $credits, $creditsLimit, $adminId);
        }
    }

    /**
     * Not bought and recommended
     * @param int $userId
     * @param int $semesterNumber
     * @return Collection|self[]
     */
    public static function getRecommended(int $userId, int $semesterNumber) : Collection
    {
        return self::where('student_id', $userId)
            ->where('recommended_semester', $semesterNumber)
            ->whereNull('at_semester')
            ->whereNull('final_result')
            ->whereNull('plan_semester')
            ->get();
    }

    /**
     * Not bought and missed
     * @param int $userId
     * @param int $semesterNumber
     * @return Collection|self[]
     */
    public static function getMissed(int $userId, int $semesterNumber) : Collection
    {
        return self::where('student_id', $userId)
            ->whereNotNull('recommended_semester')
            ->where('recommended_semester', '<', $semesterNumber)
            ->whereNull('at_semester')
            ->whereNull('final_result')
            ->whereNull('plan_semester')
            ->orderBy('recommended_semester')
            ->get();
    }

    /**
     * Not bought and next recommended
     * @param int $userId
     * @param int $semesterNumber
     * @return Collection|self[]
     */
    public static function getNextRecommended(int $userId, int $semesterNumber) : Collection
    {
        return self::where('student_id', $userId)
            ->where('recommended_semester', '>', $semesterNumber)
            ->whereNull('at_semester')
            ->whereNull('final_result')
            ->whereNull('plan_semester')
            ->orderBy('recommended_semester')
            ->get();
    }

    public static function plannedDisciplinesCredits(int $userId, string $semester) : int
    {
        $SDs = self::select(['id', 'discipline_id'])
            ->where('student_id', $userId)
            ->where('plan_semester', $semester)
            ->with('discipline')
            ->get();

        $credits = 0;
        foreach ($SDs as $SD) {
            $credits += $SD->discipline->ects;
        }

        return $credits;
    }

    public function setPlanSemester(string $semester, int $adminId) : void
    {
        Semester::checkStringSemester($semester);

        if ($this->plan_semester !== null) {
            throw new \Exception('Can\'t rewrite SD plan_semester. SDID=' . $this->id .', old='. $this->plan_semester . ', new=' . $semester);
        }

        $this->plan_semester = $semester;
        $this->plan_semester_date = Carbon::now();
        $this->plan_semester_user_id = $adminId;
        $this->plan_admin_confirm = false;
        $this->plan_student_confirm = false;

        if (!$this->save()) {
            throw new \Exception('Can\'t set SD plan_semester. SDID=' . $this->id .', old='. $this->plan_semester . ', new=' . $semester);
        }
    }

    public function changePlanSemester(string $semester, int $adminId) : void
    {
        Semester::checkStringSemester($semester);

        $this->plan_semester = $semester;
        $this->plan_semester_date = Carbon::now();
        $this->plan_semester_user_id = $adminId;
        $this->plan_admin_confirm = true;
        $this->plan_student_confirm = true;

        if (!$this->save()) {
            throw new \Exception('Can\'t change SD plan_semester. SDID=' . $this->id .', old='. $this->plan_semester . ', new=' . $semester);
        }
    }

    private static function addToPlan(Collection $SDs, string $semester, int $credits, int $creditsLimit, int $adminId) : int
    {
        foreach ($SDs as $studentDiscipline) {
            /** @var self $studentDiscipline */

            // Has unresolved dependencies
            if (!empty($studentDiscipline->dependencies)) {
                continue;
            }

            if ($credits + $studentDiscipline->discipline->ects > $creditsLimit) {
                break;
            }

            // Add to plan
            $studentDiscipline->setPlanSemester($semester, $adminId);

            StudyPlanLog::autoAddToPlan($studentDiscipline, $semester, $adminId);

            $credits += $studentDiscipline->discipline->ects;
        }

        return $credits;
    }

    public static function getForPlanEdit(int $userId) : Collection
    {
        return self::where('student_id', $userId)
            ->where('is_elective', 0)
            ->orderBy('recommended_semester')
            ->with('discipline')
            ->get();
    }

    public static function creditsByPlanSemesters(int $userId) : array
    {
        $SDs = self::select(['id', 'plan_semester', 'discipline_id'])
            ->where('student_id', $userId)
            ->whereNotNull('plan_semester')
            ->with('discipline')
            ->get();

        $result = [];
        foreach ($SDs as $SD) {
            if (!isset($result[$SD->plan_semester])) {
                [$year, $semesterNumber] = explode('.', $SD->plan_semester);
                $result[$SD->plan_semester] = [
                    'year' => $year,
                    'semester' => $semesterNumber,
                    'credits' => 0
                ];
            }

            $result[$SD->plan_semester]['credits'] += $SD->discipline->ects;
        }

        return $result;
    }

    public function clearPlanSemester(int $whoDidId) : bool
    {
        if (empty($this->plan_semester)) {
            return false;
        }

        $planSemester = $this->plan_semester;

        $this->plan_semester = null;
        $this->plan_semester_date = null;
        $this->plan_semester_user_id = null;
        $this->plan_admin_confirm = null;
        $this->plan_admin_confirm_date = null;
        $this->plan_admin_confirm_user_id = null;
        $this->plan_student_confirm = null;
        $this->plan_student_confirm_date = null;

        if (!$this->save()) {
            return false;
        }

        return StudyPlanLog::deleteFromPlan($this, $planSemester, $whoDidId);
    }

    /**
     * @param int $userId
     * @param string $semester
     * @return self[]|Collection
     * @throws \Exception
     */
    public static function getBySemester(int $userId, string $semester) : Collection
    {
        Semester::checkStringSemester($semester);

        return self::where('student_id', $userId)
            ->where('plan_semester', $semester)
            ->get();
    }

    public function adminConfirmPlanSemester(string $semester, int $adminId) : void
    {
        $this->plan_admin_confirm = true;
        $this->plan_admin_confirm_date = Carbon::now();
        $this->plan_admin_confirm_user_id = $adminId;
        $this->save();

        StudyPlanLog::adminConfirm($this, $semester, $adminId);
    }

    /**
     * @param int $userId
     * @return Collection|self[]
     */
    public static function getNotConfirmed(int $userId) : Collection
    {
        return self::where('student_id', $userId)
            ->where('plan_admin_confirm', true)
            ->where('plan_student_confirm', false)
            ->orderBy('plan_semester')
            ->with('discipline')
            ->get();
    }

    public function studentConfirmPlanSemester() : void
    {
        $this->plan_student_confirm = true;
        $this->plan_student_confirm_date = Carbon::now();
        $this->save();

        StudyPlanLog::studentConfirm($this, $this->plan_semester, $this->student_id);
    }

    public function isPlannedToSemester(string $semester) : bool
    {
        Semester::checkStringSemester($semester);

        return $this->plan_semester == $semester && $this->plan_confirmed;
    }

    public static function explodeSemester(string $semester) : array
    {
        Semester::checkStringSemester($semester);

        [$years, $semesterNumber] = explode('.', $semester);

        [$year] = explode('-', $years);

        return [$year, $semesterNumber];
    }

    /**
     * На время эпидемии
     * @return bool
     */
    public function isTest1PaidAttemptCorona() : bool
    {
        return $this->test1_attempts_count >= StudentDiscipline::TEST1_CORONA_FREE_ATTEMPTS;
    }

    /**
     * На время эпидемии
     * @return bool
     */
    public function isExamPaidAttemptCorona() : bool
    {
        return $this->exam_attempts_count >= StudentDiscipline::EXAM_CORONA_FREE_ATTEMPTS;
    }

    /**
     * @return bool
     * @codeCoverageIgnore
     */
    public function hasTest1FreeAttemptCorona() : bool
    {
        return $this->test1_attempts_count < self::TEST1_CORONA_FREE_ATTEMPTS;
    }

    public function setTest1ZeroByTime() : bool
    {
        $this->test1_result = 0;
        $this->test1_result_points = self::getTest1ResultPoints(0);
        $this->test1_result_letter = StudentRating::getLetter(0);
        $this->test1_date = Carbon::now();
        $this->test1_zeroed_by_time = true;

        return $this->save();
    }

    /**
     * @param int $userId
     * @param string $semester
     * @return Collection
     * @codeCoverageIgnore
     */
    public static function getWithoutTest1(int $userId, string $semester) : Collection
    {
        return self
            ::where('student_id', $userId)
            ->whereNull('test1_result')
            ->where('plan_semester', $semester)
            ->where('plan_admin_confirm', true)
            ->where('plan_student_confirm', true)
            ->get();
    }

    public function hasTest1FreeAttempt() : bool
    {
        return $this->test1_attempts_count < self::TEST1_FREE_ATTEMPTS;
    }

    /**
     * Show Exam button on Study page
     * @param User $user
     * @return void
     */
    public function setExamButtonShow(User $user) : void
    {
        $this->examButtonShow =
            !$this->discipline->is_practice &&
            !$this->discipline->has_diplomawork &&
            $this->plan_semester !== null &&
            (
                $user->isExamTime($this->plan_semester) ||
                $user->isExamRetakeTime($this->plan_semester)
            );
    }

    /**
     * Exam button enabled on Study page
     * @return void
     */
    public function setExamButtonEnabled() : void
    {
        $this->examButtonEnabled = $this->exam_available;
    }

    public function hasExamFreeAttemptCorona() : bool
    {
        return $this->exam_attempts_count < self::EXAM_CORONA_FREE_ATTEMPTS;
    }

    public function hasExamFreeAttempt() : bool
    {
        return $this->exam_attempts_count < self::EXAM_FREE_ATTEMPTS;
    }

    public static function getWithoutExam(int $userId, string $semester) : Collection
    {
        return self
            ::where('student_id', $userId)
            ->whereNull('test_result')
            ->where('plan_semester', $semester)
            ->where('plan_admin_confirm', true)
            ->where('plan_student_confirm', true)
            ->get();
    }

    public function setExamZeroByTime() : bool
    {
        $this->test_result = 0;
        $this->test_result_points = self::getTest1ResultPoints(0);
        $this->test_result_letter = StudentRating::getLetter(0);
        $this->test_date = Carbon::now();
        $this->exam_zeroed_by_time = true;

        return $this->save();
    }

    public static function getWithoutSRO(int $userId, string $semester) : Collection
    {
        return self
            ::where('student_id', $userId)
            ->whereNull('task_result')
            ->where('plan_semester', $semester)
            ->where('plan_admin_confirm', true)
            ->where('plan_student_confirm', true)
            ->get();
    }

    public function setSROZeroByTime() : bool
    {
        $this->task_result = 0;
        $this->task_result_points = self::getTest1ResultPoints(0);
        $this->task_result_letter = StudentRating::getLetter(0);
        $this->task_date = Carbon::now();
        $this->sro_zeroed_by_time = true;

        return $this->save();
    }

    public static function getRandomId() : ?int
    {
        $SD = self
            ::select('id')
            ->inRandomOrder()
            ->first();

        return $SD->id ?? null;
    }

    public function calculateDailyRating()
    {
        $ratingList = StudentDisciplineDay
            ::where('user_id', $this->student_id)
            ->where('discipline_id', $this->discipline_id)
            ->get();

        $limitList = StudentDisciplineDayLimit
            ::where('study_group_id', $this->user->studentProfile->study_group_id)
            ->where('discipline_id', $this->discipline_id)
            ->get();

        $limitsByDay = [];
        foreach ($limitList as $limit)
        {
            $limitsByDay[$limit->day_num] = $limit->rating_limit;
        }

        $finalRating = 0;

        foreach ($ratingList as $rating)
        {
            $ratingNum = $rating->rating;
            $limit = $limitsByDay[$rating->day_num];

            $finalRating = $finalRating + ($ratingNum * $limit / 100);
        }

        return round($finalRating);
    }

    /**
     * Show SRO button on Study page
     * @param User $user
     * @return void
     */
    public function setSyllabusButtonShow(User $user) : void
    {
        $this->syllabusButtonShow =
//            ($this->payed || $this->payed_credits) &&
            $this->plan_semester !== null &&
            $user->isSyllabusTime($this->plan_semester) &&
            $this->discipline->has_syllabuses &&
            !$user->keycloak;
    }

    /**
     * Show SRO button on Study page
     * @param User $user
     * @param array $cancelPayDisciplineIds
     * @return void
     */
    public function setPayCancelButtonShow(User $user, array $cancelPayDisciplineIds) : void
    {
        $this->payCancelButtonShow =
            $this->payed_credits &&
            !in_array($this->discipline_id, $cancelPayDisciplineIds) &&
            $this->plan_semester !== null &&
            $user->isPayCancelTime($this->plan_semester);
    }
}