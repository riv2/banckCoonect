<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class StudentLecture extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    const TYPE_ONLINE   = 'online';
    const TYPE_OFFLINE  = 'offline';

    protected $table = 'student_lecture';
}
