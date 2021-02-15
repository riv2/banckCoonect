<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeesVacancy extends Model
{
    protected $guarded = [];

    public function position(){
    	return $this->belongsTo('App\EmployeesPosition');
    }

    public function schedule(){
        return $this->hasOne('App\ManualWorkShedule', 'id', 'schedule_id');
    }

    public function requirements(){
    	return $this->hasMany('App\EmployeesVacancyRequirement', 'vacancy_id', 'id');
    }

    public function candidateRequirements(){
    	return $this->hasManyThrough('App\EmployeesCandidateRequirement', 'App\EmployeesVacancyRequirement', 'vacancy_id', 'id', 'id', 'requirement_id');
    }

    public function resume(){
        return $this->hasMany('App\EmployeesUsersResume', 'vacancy_id', 'id');
    }
}
