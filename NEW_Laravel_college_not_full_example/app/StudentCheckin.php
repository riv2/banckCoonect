<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class StudentCheckin
 * @package App
 * @property int student_id
 * @property int teacher_id
 * @property int created_at
 * @property int updated_at
 *
 */
class StudentCheckin extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'student_checkin';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function teacher()
    {
        return $this->hasOne(User::class, 'id', 'teacher_id');
    }

    public static function checkedToday(int $userId, int $teacherId) : bool
    {
        return self::where('student_id', $userId)
            ->where('teacher_id', $teacherId)
            ->where('created_at', '>=', date('Y-m-d', time()))
            ->exists();
    }

    public static function add(int $userId, int $teacherId) : bool
    {
        $checkIn = new self;
        $checkIn->student_id = $userId;
        $checkIn->teacher_id = $teacherId;
        return $checkIn->save();
    }

    public static function countByTeacher(int $studentId, int $teacherId) : int
    {
        return self::where('student_id', $studentId)
            ->where('teacher_id', $teacherId)
            ->count();
    }
}
