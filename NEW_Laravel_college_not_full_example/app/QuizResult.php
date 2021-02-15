<?php

namespace App;

use App\Services\SearchCache;
use App\Services\StudentRating;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property int id
 * @property string type
 * @property string lang
 * @property int user_id
 * @property int discipline_id
 * @property int student_discipline_id
 * @property string hash
 * @property int value
 * @property float|null points
 * @property string|null letter
 * @property bool blur
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property-read string type_text
 *
 * @property Discipline discipline
 * @property User user
 * @property StudentDiscipline studentDiscipline
 * @property QuizResultAnswer[] $answers
 */
class QuizResult extends Model
{
    const TYPE_TEST1 = 'test';
    const TYPE_EXAM = 'exam';

    protected $table = 'quize_result';

    protected $casts = [
        'blur' => 'boolean',
    ];

    private static $adminAjaxColumnList = [
        'id',
        'full_code',
        'name',
        'year'
    ];

    public static $adminRedisTable = 'admin_quiz_results';

    public function discipline()
    {
        return $this->hasOne(Discipline::class, 'id', 'discipline_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function studentDiscipline()
    {
        return $this->hasOne(StudentDiscipline::class, 'id', 'student_discipline_id');
    }

    public function answers()
    {
        return $this->hasMany(QuizResultAnswer::class, 'result_id', 'id')->orderBy('id');
    }

    public function getTypeTextAttribute() : string
    {
        return !empty($this->type) ? __('quiz_type_' . $this->type) : '';
    }

    /**
     * Array for selects
     * @return array
     */
    public static function getTypesArray() : array
    {
        return [
            self::TYPE_TEST1 => __('quiz_type_' . self::TYPE_TEST1),
            self::TYPE_EXAM => __('quiz_type_' . self::TYPE_EXAM)
        ];
    }

    /**
     * @param $valuePercent
     * @param $creditSum
     * @return bool
     */
//    public function setValue(int $valuePercent)
//    {
//        $this->value = $valuePercent;
//        $this->points = StudentRating::getPoints($valuePercent);
//        $this->letter = StudentRating::getLetter($valuePercent);
//
//        $this->user->updateGpa();
//
//        return $this->save();
//    }

    /**
     * @param $answerList
     */
    public function setResultAnswers($answerList)
    {
        foreach ($answerList as $k => $item) {
            $answerList[$k]['result_id'] = $this->id;
            $answerList[$k]['created_at'] = DB::raw('NOW()');
        }

        QuizResultAnswer::insert($answerList);
    }

    public static function addTest1(
        int $userId,
        string $lang,
        int $disciplineId,
        int $studentDisciplineId,
        string $hash,
        int $percentVal,
        bool $blur
    ): ?self
    {
        return self::add(
            self::TYPE_TEST1,
            $userId,
            $lang,
            $disciplineId,
            $studentDisciplineId,
            $hash,
            $percentVal,
            $blur
        );
    }

    public static function addExam(
        int $userId,
        string $lang,
        int $disciplineId,
        int $studentDisciplineId,
        string $hash,
        int $percentVal,
        bool $blur
    ): ?self
    {
        return self::add(
            self::TYPE_EXAM,
            $userId,
            $lang,
            $disciplineId,
            $studentDisciplineId,
            $hash,
            $percentVal,
            $blur
        );
    }

    private static function add(
        string $type,
        int $userId,
        string $lang,
        int $disciplineId,
        int $studentDisciplineId,
        string $hash,
        int $value,
        bool $blur
    ) : ?self
    {
        // Fix duplicate
        if (self::hasSame($studentDisciplineId, $value, $type)) {
            return null;
        }

        $points = ($type == QuizResult::TYPE_TEST1) ? StudentDiscipline::getTest1ResultPoints($value) : StudentDiscipline::getExamResultPoints($value);

        $quizResult = new self;
        $quizResult->type = $type;
        $quizResult->lang = $lang;
        $quizResult->user_id = $userId;
        $quizResult->discipline_id = $disciplineId;
        $quizResult->student_discipline_id = $studentDisciplineId;
        $quizResult->hash = $hash;
        $quizResult->value = $value;
        $quizResult->points = $points;
        $quizResult->letter = StudentRating::getLetter($value);
        $quizResult->blur = $blur;

        if (!$quizResult->save()) {
            throw new \Exception('Cannot save quizResult');
        }

        return $quizResult;
    }

    /**
     * @param int $studentDisciplineId
     * @return int
     * @codeCoverageIgnore
     */
    public static function test1AttemptsCount(int $studentDisciplineId) : int
    {
        return self
            ::where('type', self::TYPE_TEST1)
            ->where('student_discipline_id', $studentDisciplineId)
            ->count();
    }

    public static function getBestTest1(int $studentDisciplineId) : ?self
    {
        return self
            ::where('student_discipline_id', $studentDisciplineId)
            ->where('type', self::TYPE_TEST1)
            ->orderBy('value', 'desc')
            ->first();
    }

    public static function getLastTest1(int $studentDisciplineId): ?self
    {
        return self::where('student_discipline_id', $studentDisciplineId)
            ->where('type', self::TYPE_TEST1)
            ->orderBy('id', 'desc')
            ->first();
    }

    public static function examAttemptsCount(int $studentDisciplineId) : int
    {
        return self::where('type', self::TYPE_EXAM)
            ->where('student_discipline_id', $studentDisciplineId)
            ->count();
    }

    public static function getBestExam($studentDisciplineId) : ?self
    {
        return self::where('student_discipline_id', $studentDisciplineId)
            ->where('type', self::TYPE_EXAM)
            ->orderBy('value', 'desc')
            ->first();
    }

    public static function getLastExam(int $studentDisciplineId) : ?self
    {
        return self::where('student_discipline_id', $studentDisciplineId)
            ->where('type', self::TYPE_EXAM)
            ->orderBy('id', 'desc')
            ->first();
    }

    private static function hasSame(int $studentDisciplineId, int $result, string $type): bool
    {
        $ago5sec = Carbon::now()->subSeconds(5);

        return self::where('student_discipline_id', $studentDisciplineId)
            ->where('value', $result)
            ->where('type', $type)
            ->where('created_at', '>=', $ago5sec)
            ->exists();
    }

    /**
     * @param string|null $search
     * @param string|null $specialityId
     * @param string|null $year
     * @param string|null $baseEducationFilter
     * @param string|null $studyFormFilter
     * @param string|null $typeFilter
     * @param int $start
     * @param int $length
     * @param int $orderColumn
     * @param string $orderDirection
     * @return array
     */
    static function getListForAdmin(
        ?string $search = '',
        ?int $specialityId = null,
        ?int $year = null,
        ?string $baseEducationFilter = null,
        ?string $studyFormFilter = null,
        ?string $typeFilter = null,
        int $start = 0,
        int $length = 10,
        int $orderColumn = 0,
        string $orderDirection = 'asc'
    ) {
        $orderColumnName = self::$adminAjaxColumnList[$orderColumn] ?? 'id';

        $recordsTotal = SearchCache::totalCount(self::$adminRedisTable);

        $query = self::select(['id', 'type', 'user_id', 'discipline_id', 'student_discipline_id', 'value', 'points', 'letter', 'blur', 'created_at'])
            ->orderBy($orderColumnName, $orderDirection);

        // Without filters
        if (
            empty($search) &&
            empty($specialityId) &&
            empty($year) &&
            empty($baseEducationFilter) &&
            empty($studyFormFilter) &&
            empty($typeFilter)
        ) {
            $recordsFiltered = $recordsTotal;
        } else {
            // Speciality Filter
            if (!empty($specialityId)) {
                $query->whereHas(
                    'user',
                    function ($query) use ($specialityId) {
                        $query->whereHas(
                            'studentProfile',
                            function ($query1) use ($specialityId) {
                                $query1->where('education_speciality_id', $specialityId);
                            }
                        );
                    }
                );
            }
            // Year filter
            if (!empty($year)) {
                $query->whereHas(
                    'user',
                    function ($query) use ($year) {
                        $query->whereHas(
                            'studentProfile',
                            function ($query1) use ($year) {
                                $query1->whereHas(
                                    'speciality',
                                    function ($query2) use ($year) {
                                        $query2->where('year', $year);
                                    }
                                );
                            }
                        );
                    }
                );
            }
            // $baseEducationFilter
            if (!empty($baseEducationFilter)) {
                $query->whereHas(
                    'user',
                    function ($query) use ($baseEducationFilter) {
                        $query->whereHas(
                            'bcApplication',
                            function ($query1) use ($baseEducationFilter) {
                                $query1->where('education', $baseEducationFilter);
                            }
                        );
                    }
                );
            }
            // $studyFormFilter
            if (!empty($studyFormFilter)) {
                $query->whereHas(
                    'user',
                    function ($query) use ($studyFormFilter) {
                        $query->whereHas(
                            'studentProfile',
                            function ($query1) use ($studyFormFilter) {
                                $query1->where('education_study_form', $studyFormFilter);
                            }
                        );
                    }
                );
            }
            // $typeFilter
            if (!empty($typeFilter)) {
                $query->where('type', $typeFilter);
            }

            // Search string $search
            if (!empty($search)) {
                // Get ids
                $ids = SearchCache::searchFull(self::$adminRedisTable, $search, 'fio');

                $query->whereIn('id', $ids);

                if (is_numeric($search)) {
                    $query->orWhere('id', (int)$search);
                }
            }

            $recordsFiltered = $query->count();
        }

        // Get result
        $quizResults = $query->with(['user', 'discipline'])
            ->offset($start)
            ->take($length)
            ->get();

        $data = [];
        foreach ($quizResults as $quizResult) {
            $baseEducation = !empty($quizResult->user->base_education) ? __($quizResult->user->base_education) : '';
            $studyForm = !empty($quizResult->user->studentProfile->education_study_form) ? __($quizResult->user->studentProfile->education_study_form) : '';
            $speciality = !empty($quizResult->user->studentProfile->speciality->name) ?
                $quizResult->user->studentProfile->speciality->name . ' (' . $quizResult->user->studentProfile->speciality->year . ')' :
                '';

            $data[] = [
                $quizResult->id,
                $quizResult->user->fio ?? '',
                $speciality,
                $quizResult->user->speciality_admission_year ?? '',
                $baseEducation,
                $studyForm,
                $quizResult->discipline->name ?? '',
                $quizResult->type_text,
                !empty($quizResult->created_at) ? $quizResult->created_at->format('d.m.Y H:i') : '',
                $quizResult->value . ",($quizResult->letter)",
                '',
            ];
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    public static function generateHash() : string
    {
        return str_random(10);
    }

    public static function existsByHash(int $studentDisciplineId, string $type, string $hash) : bool
    {
        self::checkType($type);

        return self::where('student_discipline_id', $studentDisciplineId)
            ->where('type', $type)
            ->where('hash', $hash)
            ->exists();
    }

    public function addValue(int $value) : void
    {
        $newValue = $this->value + $value;
        $newValue = ($newValue > 100) ? 100 : $newValue;

        $this->value = $newValue;
        $this->letter = StudentRating::getLetter($newValue);

        if ($this->type == self::TYPE_TEST1) {
            $this->points = round(StudentDiscipline::TEST1_MAX_POINTS * ($newValue / 100));
        } elseif ($this->type == self::TYPE_EXAM) {
            $this->points = round(StudentDiscipline::EXAM_MAX_POINTS * ($newValue / 100));
        }

        $this->save();
    }

    public static function hoursFromLastResult(int $studentDisciplineId, string $type) : ?int
    {
        $result = self::select('created_at')
            ->where('student_discipline_id', $studentDisciplineId)
            ->where('type', $type)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($result)) {
            return null;
        }

        return $result->created_at->diffInHours(Carbon::now());
    }

    private static function checkType(string $type)
    {
        if (!in_array($type, [self::TYPE_TEST1, self::TYPE_EXAM])) {
            throw new \Exception('Wrong QuizResult::TYPE_* value - ' . $type);
        }
    }

    /**
     * Save questions and answers
     * @param int $appealId
     */
    public function addSnapshot(int $appealId): void
    {
        $snapshot = [];

        foreach ($this->answers as $answer) {
            if (empty($snapshot[$answer->question_id])) {
                $snapshot[$answer->question_id] = [
                    'question_id' => $answer->question_id,
                    'question' => $answer->question->question_text_only,
                    'multiple_answers' => $answer->question->has_multi_answer,
                    'answers' => $answer->question->getAnswersForSnapshot(),
                    'user_answers' => []
                ];
            }

            $snapshot[$answer->question_id]['user_answers'][] = $answer->answer_id;
        }

        $appealAnswer = new AppealQuizAnswer;
        $appealAnswer->appeal_id = $appealId;
        $appealAnswer->result_id = $this->id;
        $appealAnswer->snapshot = $snapshot;

        $appealAnswer->save();
    }
}
