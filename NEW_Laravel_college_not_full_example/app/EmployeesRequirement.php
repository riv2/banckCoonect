<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeesRequirement extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public static $fieldTypes = [
    	['code' => 'text', 'name' => 'Ввод текста'],
    	['code' => 'date', 'name' => 'Дата'],
    	['code' => 'file', 'name' => 'Файл'],
        ['code' => 'select', 'name' => 'Выпадающий список']
    ];

    public static $categories = [
        'personal_info' => 'Персональная информация',
        'education' => 'Образование',
        'nir' => 'Блок НИР',
        'seniority' => 'Трудовой стаж',
        'qualification_increase' => 'Повышение квлификации'
    ];

    protected $casts = [
        'options' => 'array',
        'multiple' => 'boolean'
    ];

    public function positions(){
    	return $this->hasMany('App\EmployeesPositionRequirement', 'requirement_id', 'id');
    }

    public function fields(){
        return $this->hasMany('App\EmployeesRequirementsField', 'requirement_id', 'id');
    }
}
