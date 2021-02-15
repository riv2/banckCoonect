<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NomenclatureFileVoteUsers extends Model
{
    protected $guarded = [];

    protected $casts = [
        'vote' => 'boolean'
    ];
}
