<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class StudyGroupTeacher
 * @package App
 *
 * @property int id
 * @property int user_id
 * @property int study_group_id
 * @property int discipline_id
 * @property Carbon date_from
 * @property Carbon date_to
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 */
class StudyGroupTeacher extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'study_group_teacher';

    protected $dates = ['created_at', 'updated_at', 'date_from', 'date_to'];

    public static function getOne(int $userId, int $groupId, int $disciplineId) : ?self
    {
        return self::where('user_id', $userId)
            ->where('study_group_id', $groupId)
            ->where('discipline_id', $disciplineId)
            ->first();
    }

    public function isManualExamTime() : bool
    {
        if (empty($this->date_from) || empty($this->date_to)) {
            return false;
        }

        $now = Carbon::today();

        return $this->date_from <= $now && $now <= $this->date_to;
    }

    public static function getGroupsByDiscipline(int $disciplineId)
    {
        $ids = self::select('study_group_id')
            ->distinct()
            ->where('discipline_id', $disciplineId)
            ->pluck('study_group_id');

        return StudyGroup::select(['id', 'name'])
            ->whereIn('id', $ids)
            ->get();
    }
}
