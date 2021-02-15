<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimetableSchedule extends Model
{
    protected $table = 'timetable_schedules';

    protected $guarded = [];

    protected $casts = [
        'date' => 'datetime:d.m.Y',
    ];

    public function studentsLessons()
    {
        return $this->hasMany(StudentSchedule::class, 'timetable_schedules_id', 'id');
    }
}
