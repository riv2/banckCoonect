<?php

namespace App\Models\Student;

use App\StudyGroup;
use Illuminate\Database\Eloquent\Model;

class StudentGroupTeacher extends Model
{
    protected $table = 'students_groups_teachers';

    public function studyGroups()
    {
        return $this->belongsToMany(
            StudyGroup::class,
            'students_study_groups_teachers',
            'student_group_teacher_id',
            'study_group_id',
            'id',
            'id'
        );
    }
}
