<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeesCandidateRequirement extends Model
{
    protected $guarded = [];

    public function vacancies(){
    	return $this->hasMany('App\EmployeesVacancyRequirement', 'requirement_id', 'id');
    }
}
