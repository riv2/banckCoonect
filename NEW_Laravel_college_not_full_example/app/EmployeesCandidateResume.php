<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeesCandidateResume extends Model
{
    protected $guarded = [];

    public function requirement(){
    	return $this->hasOne('App\EmployeesCandidateRequirement', 'id', 'requirement_id');
    }

    public function resume(){
    	return $this->belongsTo('App\EmployeesUsersResume');
    }
}
