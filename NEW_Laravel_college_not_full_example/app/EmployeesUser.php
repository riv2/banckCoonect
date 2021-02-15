<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeesUser extends Model
{
    const STATUS_CANDIDATE = 'кандидат';
    const STATUS_EMPLOYEE  = 'сотрудник';
    const STATUS_MATERNITY_LEAVE  = 'декретный отпуск';
    const STATUS_FIRED  = 'уволен';

    public static $adminEmployeesUsersTable = 'admin_employees_users';

    protected $guarded = [];

    protected $appends = ['user_positions'];

    public function user(){
    	return $this->belongsTo('App\User');
    }

    public function socialPackage(){
        return $this->hasOne('App\EmployeesUsersSocialPackage', 'employees_user_id', 'id');
    }

    public function requirements(){
        return $this->hasManyThrough('App\EmployeesUserRequirement', 'App\EmployeesUsersResume', 'user_id', 'resume_id', 'user_id', 'id');
    }

    public function positions(){
        return $this->hasManyThrough('App\EmployeesPosition', 'App\EmployeesUsersPosition', 'user_id', 'id', 'user_id', 'position_id');
    }

    public function employeesUserPositions(){
        return $this->hasMany('App\EmployeesUsersPosition', 'user_id', 'user_id');
    }

    public function getUserPositionsAttribute(){
        $positions = $this->positions;

        return implode(", ", $positions->pluck('name')->toArray());
    }
}
