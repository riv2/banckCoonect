<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeesRequirementsField extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'options' => 'array',
    ];
}
