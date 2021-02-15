<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    const STATUS_ACTIVE  = 'active';
    const STATUS_ARCHIVE = 'archive';

    protected $table = 'promotions';
}
