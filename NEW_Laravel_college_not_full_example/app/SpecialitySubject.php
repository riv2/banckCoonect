<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SpecialitySubject extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'speciality_subject';
}
