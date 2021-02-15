<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeesPositionRequirement extends Model
{
    protected $fillable = ['position_id', 'requirement_id'];
}
