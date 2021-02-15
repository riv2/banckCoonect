<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BcApplicationConfig extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'bc_application_config';

    protected $fillable = [
        'deadline_residence_registration',
        'deadline_r086',
        'deadline_r063',
        'deadline_ent',
        'deadline_diploma_supplement',
        'deadline_nostrification'
    ];
}
