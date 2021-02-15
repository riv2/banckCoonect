<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeesPositionResume extends Model
{
    protected $guarded = [];

    public function requirement(){
    	return $this->hasOne('App\EmployeesRequirement', 'id', 'requirement_id');
    }
}
