<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeesUserRequirement extends Model
{
    protected $guarded = [];

    protected $casts = [
    	'json_content' => 'array'
    ];

    public function requirement(){
    	return $this->hasOne('App\EmployeesRequirement', 'id', 'requirement_id');
    }
}
