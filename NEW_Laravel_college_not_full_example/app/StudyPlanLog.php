<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StudyPlanLog
 * @package App
 *
 * @property int id
 * @property int student_id
 * @property int discipline_id
 * @property int student_discipline_id
 * @property string semester
 * @property string action
 * @property string comment
 * @property int who_did_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class StudyPlanLog extends Model
{
    const AUTO_ADD = 'auto_add';
    const ADD = 'add';
    const DELETE = 'delete';
    const ADMIN_CONFIRM = 'admin_confirm';
    const STUDENT_CONFIRM = 'student_confirm';
    const CHANGE_SEMESTER = 'change';

    public $table = 'study_plan_log';

    public static function add(StudentDiscipline $SD, string $planSemester, int $whoDidId, string $action, string $comment = '') : bool
    {
        $log = new self;
        $log->student_id = $SD->student_id;
        $log->discipline_id = $SD->discipline_id;
        $log->student_discipline_id = $SD->id;
        $log->semester = $planSemester;
        $log->action = $action;
        $log->comment = $comment;
        $log->who_did_id = $whoDidId;

        return $log->save();
    }

    public static function autoAddToPlan(StudentDiscipline $SD, string $planSemester, int $whoDidId) : bool
    {
        return self::add($SD, $planSemester, $whoDidId, self::AUTO_ADD);
    }

    public static function addToPlan(StudentDiscipline $SD, string $planSemester, int $whoDidId) : bool
    {
        return self::add($SD, $planSemester, $whoDidId, self::ADD);
    }

    public static function deleteFromPlan(StudentDiscipline $SD, string $planSemester, int $whoDidId) : bool
    {
        return self::add($SD, $planSemester, $whoDidId, self::DELETE);
    }

    public static function adminConfirm(StudentDiscipline $SD, string $planSemester, int $whoDidId) : bool
    {
        return self::add($SD, $planSemester, $whoDidId, self::ADMIN_CONFIRM);
    }

    public static function studentConfirm(StudentDiscipline $SD, string $planSemester, int $whoDidId) : bool
    {
        return self::add($SD, $planSemester, $whoDidId, self::STUDENT_CONFIRM);
    }

    public static function changeSemester(StudentDiscipline $SD, ?string $oldSemester, string $planSemester, int $whoDidId) : bool
    {
        return self::add($SD, $planSemester, $whoDidId, self::CHANGE_SEMESTER, "$oldSemester -> $planSemester");
    }
}
