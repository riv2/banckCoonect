<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Speciality;

/**
 * Class SpecialitySemester
 * @package App
 *
 * @property int id
 * @property int speciality_id
 * @property string study_form
 * @property string base_education
 * @property int semester
 * @property string type
 * @property Carbon start_date
 * @property Carbon end_date
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property-read string type_name
 *
 * @property Speciality speciality
 */
class SpecialitySemester extends Model
{

    public $table = 'speciality_semester';

    public $dates = ['start_date', 'end_date'];

    public static $adminRedisTable = 'admin_speciality_semesters';

    private static $adminAjaxColumnList = [
        'id',
        '',
        '',
        '',
        '',
        '',
        'start_date',
        'end_date'
    ];

    public function getTypeNameAttribute()
    {
        return __('semester_type_'. $this->type);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function speciality()
    {
        return $this->hasOne(Speciality::class, 'id', 'speciality_id');
    }

    /**
     * @param int|null $specialityId
     * @param string|null $baseEducationFilter
     * @param string|null $studyFormFilter
     * @param string|null $type
     * @param int|null $semester
     * @param int $start
     * @param int $length
     * @param int $orderColumn
     * @param string $orderDirection
     * @return array
     */
    static function getListForAdmin(
        ?int $specialityId = null,
        ?string $baseEducationFilter = null,
        ?string $studyFormFilter = null,
        ?string $type = null,
        ?int $semester = null,
        int $start = 0,
        int $length = 10,
        int $orderColumn = 0,
        string $orderDirection = 'asc'
    )
    {
        $orderColumnName = self::$adminAjaxColumnList[$orderColumn] ?? 'id';

        $query = self::orderBy($orderColumnName, $orderDirection);

        $recordsTotal = $query->count();

        // Without filters
        if (
            empty($search) &&
            empty($specialityId) &&
            empty($baseEducationFilter) &&
            empty($studyFormFilter) &&
            empty($type) &&
            empty($semester)
        ) {
            $recordsFiltered = $recordsTotal;
        } else {
            // Speciality Filter
            if (!empty($specialityId)) {
                $query->where('speciality_id', $specialityId);
            }
            // $baseEducationFilter
            if (!empty($baseEducationFilter)) {
                $query->where('base_education', $baseEducationFilter);
            }
            // $studyFormFilter
            if (!empty($studyFormFilter)) {
                $query->where('study_form', $studyFormFilter);
            }
            // $type
            if (!empty($type)) {
                $query->where('type', $type);
            }
            // $semester
            if (!empty($semester)) {
                $query->where('semester', $semester);
            }

            // Search string $search
//            if (!empty($search)) {
//                // Get ids
//                $ids = SearchCache::searchFull(self::$adminRedisTable, $search);
//
//                $query->whereIn('id', $ids);
//
//                if (is_numeric($search)) {
//                    $query->orWhere('id', (int)$search);
//                }
//            }

            $recordsFiltered = $query->count();
        }

        // Get result
        $specialitySemesters = $query->with(['speciality'])
            ->offset($start)
            ->take($length)
            ->get();

        $data = [];
        foreach ($specialitySemesters as $specialitySemester) {
            $data[] = [
                $specialitySemester->speciality->name . ' (' . $specialitySemester->speciality->year . ') id ' . $specialitySemester->speciality->id,
                __($specialitySemester->base_education),
                __($specialitySemester->study_form),
                $specialitySemester->type_name,
                $specialitySemester->semester,
                $specialitySemester->start_date->format('d.m.Y'),
                $specialitySemester->end_date->format('d.m.Y'),
                $specialitySemester->id
            ];
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    public static function getOne(int $specialityId, string $studyForm, string $baseEducation, int $semester, string $type) : ?self
    {
        return self::where('speciality_id', $specialityId)
            ->where('study_form', $studyForm)
            ->where('base_education', $baseEducation)
            ->where('semester', $semester)
            ->where('type', $type)
            ->first();
    }

    public static function getDatesArray(int $specialityId, string $studyForm, string $baseEducation, string $semester, string $type) : array
    {
        $dates = self::select(['start_date', 'end_date'])
            ->where('speciality_id', $specialityId)
            ->where('study_form', $studyForm)
            ->where('base_education', $baseEducation)
            ->where('semester', Semester::getNumberFromString($semester))
            ->where('type', $type)
            ->first();

        if (empty($dates)) {
            return [];
        }

        return $dates->toArray();
    }

    /**
     * @param string $type
     * @return Collection|self[]
     */
    public static function getAllByType(string $type) : Collection
    {
        return self
            ::where('type', $type)
            ->get();
    }

    public static function getAllTest1() : Collection
    {
        return self::getAllByType(Semester::TYPE_TEST1);
    }

    public static function getAllExam() : Collection
    {
        return self::getAllByType(Semester::TYPE_EXAM);
    }

    public static function getAllSRO() : Collection
    {
        return self::getAllByType(Semester::TYPE_SRO);
    }
}