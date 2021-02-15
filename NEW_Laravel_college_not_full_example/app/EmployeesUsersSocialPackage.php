<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeesUsersSocialPackage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'food' => 'boolean',
    ];
}
