<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeesDepartment extends Model
{
    protected $fillable = ['id', 'name', 'description', 'superviser', 'manager_user_id', 'is_sector'];

    protected $casts = [
        'is_sector' => 'boolean'
    ];

    public function position(){
    	return $this->hasMany('App\EmployeesPosition', 'department_id', 'id');
    }

    public function speciality(){
    	return $this->hasMany('App\SectorSpeciality', 'department_id', 'id');
    }
}
