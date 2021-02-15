<?php

namespace App\Models\Discipline;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DisciplineSemester
 * @package App\Models\Discipline
 *
 * @property int discipline_id
 * @property string semester
 * @property string study_form
 * @property string lecture_hours
 * @property string practical_hours
 * @property string laboratory_hours
 * @property string sro_hours
 * @property string srop_hours
 * @property string control_form
 */
class DisciplineSemester extends Model
{
    protected $table = 'discipline_semesters';

    public const FULL_TIME = 'fulltime';

    public const EXTRAMURAL = 'extramural';

    /**
     * @param int $disciplineId
     * @param array $semesters
     * @return void
     */
    public static function updateSemesters(int $disciplineId, array $semesters): void
    {
        foreach ($semesters as $semester => $studyFormsHours){
            foreach ($studyFormsHours as $studyForm => $fields){
                $disciplineSemester = self::where('study_form', $studyForm)
                    ->where('discipline_id', $disciplineId)
                    ->where('semester', $semester)
                    ->first();

                if (empty($disciplineSemester)){
                    $disciplineSemester = new self();
                    $disciplineSemester->semester = $semester;
                    $disciplineSemester->study_form = $studyForm;
                }
                $disciplineSemester->discipline_id = $disciplineId;
                $disciplineSemester->lecture_hours = $fields['lecture'];
                $disciplineSemester->practical_hours = $fields['practice'];
                $disciplineSemester->laboratory_hours = $fields['lab'];
                $disciplineSemester->sro_hours = $fields['sro'];
                $disciplineSemester->srop_hours = $fields['srop'];
                $disciplineSemester->control_form = $fields['control_form'];
                $disciplineSemester->save();
            }
        }
    }
}
