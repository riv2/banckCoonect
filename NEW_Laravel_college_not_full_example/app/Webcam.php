<?php

namespace App;

use App\Services\Auth;
use Illuminate\Database\Eloquent\Model;

class Webcam extends Model
{
    const TEST_TYPES = [
        't1' => 'T1',
        'exam' => 'Экзамен'
    ];

    protected $table = 'students_webcam_exam';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }
}
