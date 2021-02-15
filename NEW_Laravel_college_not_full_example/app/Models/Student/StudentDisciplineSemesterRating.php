<?php

namespace App\Models\Student;

use App\Discipline;
use App\Services\Auth;
use App\SpecialityDiscipline;
use App\StudyGroup;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StudentDisciplineSemesterRating
 * @package App\Models\Student
 *
 * @property int id
 * @property int user_id
 * @property int discipline_id
 * @property int teacher_id
 * @property int study_group_id
 * @property int timetable_schedules_id
 * @property string semester
 * @property string rating
 * @property string type
 * @property int month
 * @property int year
 * @property int day
 * @property Carbon created_at
 */
class StudentDisciplineSemesterRating extends Model
{
    protected $table = 'students_discipline_semester_rating';

    protected $guarded = [];

    public const DEFAULT_RATING = 'default_rating';

    public const TEACHER_DEFAULT_RATTING = 'teacher_default_rating';

    public const CERTIFICATION_RATING = 'certification_rating';

    public const PRE_CERTIFICATION_RATING = 'pre_certification_rating';

    public const EXAM_RATING = 'exam_rating';

    public const SCORE_RATING = 'score_rating';

    public const COURSE_WORK_RATING = 'course_work_rating';

    public const RESULT_RATING = 'result_rating';

    public const TEACHER_TYPES = [
        self::TEACHER_DEFAULT_RATTING => 'Занятие',
        self::CERTIFICATION_RATING => 'Аттестация',
        self::PRE_CERTIFICATION_RATING => 'Пре-аттестация'
    ];

    public const ALL_TYPES = [
        self::DEFAULT_RATING => 'Занятие',
        self::TEACHER_DEFAULT_RATTING => 'Занятие',
        self::CERTIFICATION_RATING => 'Аттестация',
        self::PRE_CERTIFICATION_RATING => 'Пре-аттестация',
        self::EXAM_RATING => 'Экзамен',
        self::SCORE_RATING => 'Диф. Зачет',
        self::COURSE_WORK_RATING => 'Курсовая работа',
        self::RESULT_RATING => 'Итоговая оценка'
    ];

    public const LAST_TYPES = [
        self::EXAM_RATING,
        self::SCORE_RATING,
        self::COURSE_WORK_RATING,
        self::RESULT_RATING
    ];

    public const MONTHS = [
        1 => 'Январь',
        2 => 'Февраль',
        3 => 'Март',
        4 => 'Апрель',
        5 => 'Май',
        6 => 'Июнь',
        7 => 'Июль',
        8 => 'Август',
        9 => 'Сентябрь',
        10 => 'Октябрь',
        11 => 'Ноябрь',
        12 => 'Декабрь'
    ];

    /**
     * @param $request
     */
    public static function createOrUpdate($request)
    {
        self::where('discipline_id', $request->disciplineId)
            ->where('study_group_id', $request->groupId)
            ->where('teacher_id', Auth::id())
            ->where('semester', $request->semester)
            ->delete();

        foreach ($request->days as $type => $days){
            foreach ($days as $day){

                foreach ($day['students'] as $studentId => $rating){
                    $studentRating = new self;
                    $studentRating->user_id = $studentId;
                    $studentRating->discipline_id = $request->disciplineId;
                    $studentRating->study_group_id = $request->groupId;
                    $studentRating->teacher_id = Auth::id();
                    $studentRating->semester = $request->semester;
                    $studentRating->type = $type;
                    $studentRating->month = $request->month;
                    $studentRating->year = $request->year;
                    $studentRating->day = $day['day'] ?? null;
                    $studentRating->rating = $rating;
                    $studentRating->save();
                }
            }
        }
    }

    /**
     * @param $daysCount
     * @param $date
     * @return array
     */
    public static function getDefaultRatingDays($daysCount, $date)
    {
        $defaultRatingDays  = [];
        for($i = 1; $i <= $daysCount; $i++){
            $carbon = Carbon::create($date->year, $date->month, $i);

            if ($carbon->dayOfWeek !== 0 and $carbon->dayOfWeek !== 6) {
                $defaultRatingDays[$carbon->day]['day'] = $carbon->day;
                $defaultRatingDays[$carbon->day]['students'] = null;
            }
        }
        return $defaultRatingDays;
    }

    /**
     * @param $type
     * @param $daysCount
     * @param $date
     * @param $semesterString
     * @param $disciplineId
     * @param $groupId
     * @param $month
     * @param $userId
     * @return array
     */
    public static function getRatingDaysByType($type, $daysCount, $date, $semesterString, $disciplineId, $groupId, $month , $userId)
    {
        $dayStudentsRating = self::where('semester', $semesterString)
            ->where('teacher_id', $userId)
            ->where('type', $type)
            ->where('discipline_id', $disciplineId)
            ->where('study_group_id', $groupId)
            ->where('month', $month)
            ->get();

        $ratingDays = [];
        if ($type === self::DEFAULT_RATING){
            $ratingDays = self::getDefaultRatingDays($daysCount, $date);
        }
        foreach ($dayStudentsRating as $rating){
            $ratingDays[$rating->day]['day'] = $rating->day;
            $ratingDays[$rating->day]['students'][$rating->user_id] = $rating->rating;
        }
        return array_values($ratingDays);
    }

    /**
     * @param $request
     * @param $semester
     * @param $userId
     * @return array|mixed
     */
    public static function getRatingDays($request, $semester, $userId)
    {
        $data = collect();
        $date = Carbon::create($request->year, $request->month);

        if ($request->has('month') and $request->has('year')){
            $monthDays = [];

            foreach (self::ALL_TYPES as $type => $lang){
                if (!in_array($type, self::LAST_TYPES, false)){
                    $monthDays[$type] =  self::getRatingDaysByType(
                        $type,
                        $semester->start_date->daysInMonth,
                        $date,
                        $request->semester,
                        $request->disciplineId,
                        $request->groupId,
                        $request->month,
                        $userId
                    );
                }
            }
            foreach ($monthDays as $type => $days){
                foreach ($days as $day){
                    $data->push([
                        'day' => $day['day'],
                        'students' => $day['students'],
                        'type' => $type,
                        'editable' => true,
                    ]);
                }
            }
        }
        $days = [];
        foreach ($data->sortBy('day') as $day){
            $days[] = $day;
        }
        if ($semester->end_date->month === $request->month){
            $lastMonthSemesterDay = self::getLastMonthSemesterDay($request->groupId, $request->disciplineId, $request->semester);

            foreach ($lastMonthSemesterDay as $day){
                $days[] = $day;
            }
        }
        return $days;
    }

    /**
     * @param $groupId
     * @param $disciplineId
     * @param $semester
     * @return array
     */
    public static function getLastMonthSemesterDay($groupId, $disciplineId, $semester): array
    {
        $lastStudentsRatings = self::where('discipline_id', $disciplineId)
            ->whereIn('type', self::LAST_TYPES)
            ->where('study_group_id', $groupId)
            ->where('semester', $semester)
            ->get();

        $studentRatings = [];
        foreach (self::LAST_TYPES as $type) {
            if ($type !== self::RESULT_RATING){
                $students = [];
                foreach ($lastStudentsRatings as $rating) {
                    if ($rating->type === $type){
                        $students[$rating->user_id] = $rating->rating;
                    }
                }
                $studentRatings[] = [
                    'day' => null,
                    'students' => $students,
                    'editable' => self::checkEditableForType($type, $groupId, $disciplineId, $semester),
                    'type' => $type
                ];
            }
        }
        $students = [];
        foreach ($lastStudentsRatings->where('type', self::RESULT_RATING) as $rating) {
            $students[$rating->user_id] = self::getResultRating($rating->user_id, $disciplineId, $semester);
        }
        $studentRatings[] = [
            'day' => null,
            'students' => $students,
            'editable' => self::checkEditableForType($type, $groupId, $disciplineId, $semester),
            'type' => $type
        ];
        return $studentRatings;
    }

    /**
     * @param $type
     * @param $groupId
     * @param $disciplineId
     * @param $semester
     * @return bool
     */
    public static function checkEditableForType($type, $groupId, $disciplineId, $semester)
    {
        $studyGroup = StudyGroup::find($groupId);

        $discipline = Discipline::find($disciplineId);
        if ($type === self::COURSE_WORK_RATING){
            $specialityDiscipline = SpecialityDiscipline::where('discipline_id', $disciplineId)
                ->where('speciality_id', $studyGroup->specialityId)
                ->whereHas('semesters', function ($query) use ($semester){
                    $query->where('semester', $semester);
                })
                ->first();

            return $specialityDiscipline->has_coursework === 1;
        }
        if ($type === self::EXAM_RATING){
            $specialityDiscipline = SpecialityDiscipline::where('discipline_id', $disciplineId)
                ->where('speciality_id', $studyGroup->specialityId)
                ->first();

            $lastSemester = $specialityDiscipline
                ->semesters()
                ->orderBy('semester', 'desc')
                ->first();

            if (!$discipline->hasScore($semester) or $lastSemester->semester == $semester){
                return $studyGroup->isExamTime($semester);
            } else {
                return false;
            }
        }
        if ($type === self::SCORE_RATING){
            return $discipline->hasScore($semester);
        }
    }

    /**
     * @param $userId
     * @param $disciplineId
     * @param $semester
     * @return float|int
     */
    public static function getResultRating($userId, $disciplineId, $semester)
    {
        $result = 0;
        $studentsRatings = self::select('rating')
            ->where('user_id', $userId)
            ->where('discipline_id', $disciplineId)
            ->where('semester', $semester)
            ->where('type', self::CERTIFICATION_RATING)
            ->get();
        foreach ($studentsRatings as $rating){
            $result += (int)$rating->rating;
        }
        if (isset($studentsRatings)){
            if ($studentsRatings->count() > 0){
                $result = (int)$result / $studentsRatings->count() ;
            }
        }
        $studentsRatingExam = self::select('rating')
            ->where('user_id', $userId)
            ->where('discipline_id', $disciplineId)
            ->where('semester', $semester)
            ->where('type', self::EXAM_RATING)
            ->first();

        return (int)($result + (int)$studentsRatingExam->rating) / 2;
    }
}
