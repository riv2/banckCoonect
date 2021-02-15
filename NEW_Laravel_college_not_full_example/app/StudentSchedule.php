<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentSchedule extends Model
{
    protected $table = 'student_schedules';

    protected $guarded = [];

    public function timetableSchedules()
    {
        return $this->hasMany(TimetableSchedule::class, 'id', 'timetable_schedules_id');
    }
}
