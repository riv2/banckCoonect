<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeesOrder extends Model
{
	protected $guarded = [];

    public static $statuses = [
        'new' 	   => 'Новый', 
        'review'   => 'На рассмотрении', 
        'approved' => 'Одобрено', 
        'declined' => 'Отказ', 
    ];

    public function orderName(){
    	return $this->hasOne('App\EmployeesOrderName', 'id', 'employees_order_name_id');
    }

    public function users(){
    	return $this->hasManyThrough('App\EmployeesUser', 'App\EmployeesOrderUser', 'order_id', 'id', 'id', 'employees_id');
    }

    public function candidates(){
    	return $this->hasManyThrough('App\EmployeesUsersResume', 'App\EmployeesOrderUser', 'order_id', 'id', 'id', 'employees_id');
    }

    public function votes(){
        return $this->hasMany('App\EmployeesUsersVote', 'order_id', 'id');
    }
}
