<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Practice extends Model
{
    public static $filePath = 'practice';

    public function specialities() {
        return $this->belongsToMany(
            'App\Speciality',
            'practice_specialities',
            'practice_id',
            'speciality_id'
        )->using('App\PracticeSpeciality');
    }

    public function scans()
    {
        return $this->hasMany(
            'App\Scan'
        );
    }

}
