<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManualEducation extends Model
{
	const TYPE_DOCTOR = 'доктор наук';
    const TYPE_CANDIDATE  = 'кандидат наук';
    const TYPE_MASTER  = 'магистр';

    protected $guarded = [];
}
