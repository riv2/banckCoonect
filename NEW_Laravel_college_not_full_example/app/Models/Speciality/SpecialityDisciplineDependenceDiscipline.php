<?php

namespace App\Models\Speciality;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SpecialityDisciplineDependenceDiscipline
 * @package App\Models\Speciality
 *
 * @property int id
 * @property int speciality_discipline_dependence_id
 * @property int discipline_id
 */
class SpecialityDisciplineDependenceDiscipline extends Model
{
    protected $table = 'speciality_discipline_dependencies_disciplines';
}
