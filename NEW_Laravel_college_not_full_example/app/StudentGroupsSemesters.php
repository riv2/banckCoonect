<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class StudentGroupsSemesters
 * @package App
 *
 * @property int id
 * @property int user_id
 * @property int study_group_id
 * @property string semester
 */
class StudentGroupsSemesters extends Model
{
    public static function add(int $userId, int $studyGroupId, string $semester) : bool
    {
        $item = new self;
        $item->user_id = $userId;
        $item->study_group_id = $studyGroupId;
        $item->semester = $semester;

        return $item->save();
    }

    public static function getUserIds(int $groupId, string $semester) : array
    {
        return self::select('user_id')
            ->where('study_group_id', $groupId)
            ->where('semester', $semester)
            ->pluck('user_id')
            ->toArray();
    }

    public static function getSemesters() : array
    {
        return self::select('semester')
            ->distinct()
            ->orderBy('semester', 'desc')
            ->pluck('semester')
            ->toArray();
    }
}