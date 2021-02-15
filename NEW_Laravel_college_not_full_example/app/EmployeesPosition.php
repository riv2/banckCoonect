<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeesPosition extends Model
{
    protected $fillable = ['id', 'name', 'description', 'department_id', 'managerial'];

    protected $casts = [
        'managerial' => 'boolean',
    ];

    public function department(){
    	return $this->belongsTo('App\EmployeesDepartment');
    }

    public function requirements(){
    	return $this->hasMany('App\EmployeesPositionRequirement', 'position_id', 'id');
    }

    public function users(){
    	return $this->hasMany('App\EmployeesUsersPosition', 'position_id', 'id');
    }

    public function vacancy(){
        return $this->hasMany('App\EmployeesVacancy', 'position_id', 'id');
    }

    public function positionRequirements(){
        return $this->hasManyThrough('App\EmployeesRequirement', 'App\EmployeesPositionRequirement', 'position_id', 'id', 'id', 'requirement_id');
    }

    public function getRequirementsArray(){
        $requirements = $this->positionRequirements()->with('fields')->get();
        $array['personal_info'] = [];
        $array['education'] = [];
        $array['qualification_increase'] = [];
        $array['seniority'] = [];
        $array['nir'] = [];
        foreach ($requirements as $key => $value) {
            $array[$value->category] += [ $value->name => [$value->toArray()] ];
        }

        return $array;
    }

    public function roles(){
        return $this->hasManyThrough('App\Role', 'App\EmployeesPositionRole', 'position_id', 'id', 'id', 'role_id');
    }
}
