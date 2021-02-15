<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrendQualification extends Model
{
    protected $fillable = [
        'trand_id',
        'name_ru',
        'name_kz',
        'name_en',
    ];

    public $timestamps = false;
}
