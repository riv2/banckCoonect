<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeesUsersVote extends Model
{
    protected $guarded = [];

    protected $casts = [
        'vote' => 'boolean',
    ];

    public static $statuses = [
        1 	 => 'За', 
        0    => 'Против', 
        null => 'Не голосовал'
    ];

    public function user(){
    	return $this->hasOne('App\User', 'id', 'user_id');
    }
}
