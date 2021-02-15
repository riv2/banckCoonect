<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDisciplineDayLimit extends Model
{
    protected $table = 'student_discipline_day_limits';

    const ACCESS_TO_ADMIN_LIST = [4951];
}
