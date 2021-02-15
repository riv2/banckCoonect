<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LectureRating extends Model
{
    use SoftDeletes;

    protected $table = 'lecture_rating';
}
