<?php

namespace App;

use App\Models\Student\StudentDisciplineSemesterRating;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{DB,Log};

/**
 * Class Semester
 * @package App
 * @property int id
 * @property string study_form
 * @property int number
 * @property string type
 * @property Carbon start_date
 * @property Carbon end_date
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property-read string type_name
 * @property-read string semesterString
 *
 */
class Semester extends Model
{
    protected $table = 'semesters';

    const CURRENT_STUDY_YEAR = 2019;

    const TYPE_STUDY = 'study';
    const TYPE_PLAN_APPROVAL = 'plan_approval';
    const TYPE_BUYING = 'buying';
    const TYPE_BUY_CANCEL = 'buy_cancel';
    const TYPE_SYLLABUSES = 'syllabuses';
    const TYPE_TEST1 = 'test1';
    const TYPE_TEST1_RETAKE = 'test1_retake';
    const TYPE_SRO = 'sro';
    const TYPE_SRO_RETAKE = 'sro_retake';
    const TYPE_EXAM = 'exam';
    const TYPE_EXAM_RETAKE = 'exam_retake';

    public static $types = [
        'study',
        'plan_approval',
        'buying',
        'buy_cancel',
        'syllabuses',
        'test1',
        'test1_retake',
        'sro',
        'sro_retake',
        'exam',
        'exam_retake'
    ];

    private static $adminAjaxColumnList = [
        'id',
        '',
        '',
        'start_date',
        'end_date'
    ];

    protected $dates = [
        'start_date',
        'end_date'
    ];

    /**
     * @codeCoverageIgnore
     */
    public function getTypeNameAttribute()
    {
        return __('semester_type_'. $this->type);
    }

    /**
     * Номер семестра этого учебного года. 1, 2, 3.
     * @param string $studyForm
     * @param Carbon|null $date
     * @return int|null
     */
    public static function inStudyYear(string $studyForm, ?Carbon $date = null) : ?int
    {
        if ($date === null) {
            $date = Carbon::now();
        }

        $semester = self::where('study_form', $studyForm)
            ->where('start_date', '<=', $date->format('Y-m-d'))
            ->where('end_date', '>=', $date->format('Y-m-d'))
            ->where('type', self::TYPE_STUDY)
            ->first();

        if (!$semester) {
            return null;
        }

        return $semester->number;
    }

    /**
     * Номер семестра. Какой семестр этой специальности.
     * @param string $studyForm
     * @param int $specialityYear Год специальности
     * @param Carbon|null $date
     * @return int|null
     */
    public static function inSpeciality(string $studyForm, int $specialityYear, ?Carbon $date = null) : ?int
    {
        $semesterNumber = self::inStudyYear($studyForm, $date);

        if (!$semesterNumber) {
            return null;
        }

        return $semesterNumber + ((self::CURRENT_STUDY_YEAR - $specialityYear) * 2);
    }

    /**
     * Semester's number in speciality
     * @param int $specialityYear
     * @param string $semester Like '2019-20.2'
     * @return int|null
     */
    public static function byStringSemester(int $specialityYear, string $semester) : ?int
    {
        [$year, $semesterNumber] = StudentDiscipline::explodeSemester($semester);

        if (empty($semesterNumber) || $semesterNumber > 2) {
            return null;
        }

        return $semesterNumber + (($year - $specialityYear) * 2);
    }

    public static function isTest1Time(string $studyForm, ?Carbon $date = null) : bool
    {
        if ($date === null) {
            $date = Carbon::now();
        }

        return self::where('study_form', $studyForm)
            ->where('start_date', '<=', $date->format('Y-m-d'))
            ->where('end_date', '>=', $date->format('Y-m-d'))
            ->where('type', self::TYPE_TEST1)
            ->exists();
    }

    public static function isExamTime(string $studyForm, ?Carbon $date = null) : bool
    {
        if ($date === null) {
            $date = Carbon::now();
        }

        return self::where('study_form', $studyForm)
            ->where('start_date', '<=', $date->format('Y-m-d'))
            ->where('end_date', '>=', $date->format('Y-m-d'))
            ->where('type', self::TYPE_EXAM)
            ->exists();
    }

    public static function isSROTime(string $studyForm, ?Carbon $date = null) : bool
    {
        if ($date === null) {
            $date = Carbon::now();
        }

        $semester = self::inStudyYear($studyForm, $date);

        // Is not study time (summer)
        if (empty($semester)) {
            return false;
        }

        return true;
    }

    /**
     * Current semester string like '2019-20.2'
     * @param string $studyForm
     * @param Carbon|null $date
     * @return string
     */
    public static function current(string $studyForm, ?Carbon $date = null) : string
    {
        $number = self::inStudyYear($studyForm, $date);

        return self::semesterString(self::CURRENT_STUDY_YEAR, $number);
    }

//    public static function getTypesForSelect() : array
//    {
//        $array = [];
//
//        foreach (self::$types as $type) {
//            $array[$type] = __('semester_type_'. $type);
//        }
//
//        return $array;
//    }

    /**
     * @codeCoverageIgnore
     */
    public static function getTypesForSpecialitySemestersSelect() : array
    {
        $array = [];

        foreach (self::$types as $type) {
            if ($type == self::TYPE_STUDY) {
                continue;
            }

            $array[$type] = __('semester_type_'. $type);
        }

        return $array;
    }

    public static function todayBetween(string $startDate, string $endDate, ?Carbon $today = null) : bool
    {
        if ($today === null) {
            $today = Carbon::now();
        }

        [$date, ] = explode(' ', $startDate);
        $start = Carbon::createFromFormat('Y-m-d', $date);
        $start->setTime(0, 0, 0);

        [$date, ] = explode(' ', $endDate);
        $end = Carbon::createFromFormat('Y-m-d', $date);
        $end->setTime(23, 59, 59);

        return $start <= $today && $today <= $end;
    }

    public static function getNumberFromString(string $semester) : int
    {
        self::checkStringSemester($semester);

        [, $number] = explode('.', $semester);

        return $number;
    }

    public static function checkStringSemester(string $semester): void
    {
        if (!preg_match('/^\d{4}-\d\d\.[1-3|9]$/', $semester)) {
            throw new \Exception('Wrong semester format - ' . $semester);
        }
    }

    public static function todayInDefaultDates(string $studyForm, string $semester, string $type, ?Carbon $date = null) : bool
    {
        if ($date === null) {
            $date = Carbon::now();
        }

        $semesterNum = self::getNumberFromString($semester);

        return self
            ::where('study_form', $studyForm)
            ->where('number', $semesterNum)
            ->where('type', $type)
            ->where('start_date', '<=', $date->format('Y-m-d'))
            ->where('end_date', '>=', $date->format('Y-m-d'))
            ->exists();
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getSemestersList(): array
    {
        $semestersList = [];

        for ($i = 0; $i <= 4; $i++) {
            $currentYear = self::CURRENT_STUDY_YEAR + $i;
            $nextYear = $currentYear + 1 - 2000;

            for ($j = 1; $j <= 3; $j++) {
                $semestersList[] = $currentYear . '-' . $nextYear . '.' . $j;
            }
        }

        return $semestersList;
    }

    /**
     * @param string|null $studyForm
     * @param string|null $type
     * @param int|null $semesterFilter
     * @param int $start
     * @param int $length
     * @param int $orderColumn
     * @param string $orderDirection
     * @return array
     */
    static function getListForAdmin(
        ?string $studyForm = null,
        ?string $type = null,
        ?int $semesterFilter = null,
        int $start = 0,
        int $length = 10,
        int $orderColumn = 0,
        string $orderDirection = 'asc') : array
    {
        $orderColumnName = self::$adminAjaxColumnList[$orderColumn] ?? 'id';

        $query = self::orderBy($orderColumnName, $orderDirection);

        $recordsTotal = $query->count();

        // Without filters
        if (
            empty($studyForm) &&
            empty($type) &&
            empty($semesterFilter)
        ) {
            $recordsFiltered = $recordsTotal;
        } else {
            // $studyFormFilter
            if (!empty($studyForm)) {
                $query->where('study_form', $studyForm);
            }
            // $type
            if (!empty($type)) {
                $query->where('type', $type);
            }
            // $semester
            if (!empty($semesterFilter)) {
                $query->where('number', $semesterFilter);
            }

            $recordsFiltered = $query->count();
        }

        // Get result
        $semesters = $query->offset($start)
            ->take($length)
            ->get();

        $data = [];
        foreach ($semesters as $semester) {
            $data[] = [
                __($semester->study_form),
                $semester->type_name,
                $semester->number,
                $semester->start_date->format('d.m.Y'),
                $semester->end_date->format('d.m.Y'),
                $semester->id
            ];
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    public static function semesterString(int $year, int $semesterNumber) : string
    {
        $nextYear = $year + 1 - 2000;

        return $year . '-'. $nextYear . '.' . $semesterNumber;
    }

    public static function getFinishedYesterdayTest1(Carbon $today) : Collection
    {
        return self::getFinishedYesterday($today, self::TYPE_TEST1);
    }

    private static function getFinishedYesterday(Carbon $today, string $type) : Collection
    {
        $yesterday = $today->subDay(1);

        return self
            ::where('end_date', $yesterday->format('Y-m-d'))
            ->where('type', $type)
            ->get();
    }

    public static function semesterInCurrentYear(int $number) : string
    {
        return self::semesterString(self::CURRENT_STUDY_YEAR, $number);
    }

    public static function getFinishedYesterdayExam(Carbon $today) : Collection
    {
        return self::getFinishedYesterday($today, self::TYPE_EXAM);
    }

    public static function getFinishedYesterdaySRO(Carbon $today) : Collection
    {
        return self::getFinishedYesterday($today, self::TYPE_SRO);
    }

    public function getSemesterStringAttribute()
    {
        return self::semesterString($this->start_date->year, $this->number);
    }

    public function getMonthsWithYearList()
    {
        $months = [];

        $this->start_date = $this->start_date->day(1);
        $this->end_date = $this->end_date->day(1);

        while ($this->start_date <= $this->end_date){
            $months[] =[
                'num' => $this->start_date->month,
                'name' => StudentDisciplineSemesterRating::MONTHS[$this->start_date->month],
                'year' => $this->start_date->year
            ];
            $this->start_date = $this->start_date->addMonths(1);
        }
        return $months;
    }

    public function getIsCurentAttribute()
    {
        $date = Carbon::now();

        return $this->start_date <= $date->format('Y-m-d') and $this->end_date >= $date->format('Y-m-d');
    }
}
