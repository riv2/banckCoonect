<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeesUsersDecree extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_signed' => 'boolean',
    ];

    public static $statuses = [
        'recruitment' => 'Принятие на должность', 
    ];

    public function user(){
    	return $this->belongsTo('App\User');
    }
}
