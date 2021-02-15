<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $table = 'promo';

    protected $dates = [
        'bdate',
        'issuedate',
        'expire_date'
    ];
}
