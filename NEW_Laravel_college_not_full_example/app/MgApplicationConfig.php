<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MgApplicationConfig extends Model
{
    protected $table = 'mg_application_config';

    protected $fillable = [
        'deadline_residence_registration',
        'deadline_r086',
        'deadline_r063',
        'deadline_ent',
        'deadline_diploma_supplement',
        'deadline_nostrification',
        'deadline_military_commision',
        'deadline_english_certificate',
        'deadline_isbn',
        'deadline_work_book'
    ];
}
