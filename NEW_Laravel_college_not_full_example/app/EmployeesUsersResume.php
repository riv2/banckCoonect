<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeesUsersResume extends Model
{
    protected $guarded = [];

    public static $statuses = [
        'pending' => 'На Рассмотрении', 
        'interview' => 'Приглашен на собеседование', 
        'approved' => 'Нанят',
        'declined' => 'Отказ',
        'revision_position_requirements' => 'Требует доработки требований'
    ];

    public function vacancy(){
    	return $this->hasOne('App\EmployeesVacancy', 'id', 'vacancy_id');
    }

    public function user(){
    	return $this->belongsTo('App\User');
    }

    public function requirements(){
        return $this->hasMany('App\EmployeesUserRequirement', 'resume_id', 'id');
    }

    public function userRequirementsWithOriginal(){
        $resumeRequirements = $this->requirements()->with('requirement')->with('requirement.fields')->get();
        $array['personal_info'] = [];
        $array['education'] = [];
        $array['qualification_increase'] = [];
        $array['seniority'] = [];
        $array['nir'] = [];

        foreach ($resumeRequirements as $key => $value) {
            $array[$value->requirement->category] += [ $key => $value->toArray() ];
        }

        return $array;
    }
}
