<?php

namespace App\Models\Speciality;

use App\Discipline;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SpecialityDisciplineDependence
 * @package App\Models\Speciality
 *
 * @property int id
 * @property int speciality_id
 * @property int discipline_id
 * @property string year
 *
 * @property Discipline discipline
 * @property Discipline dependenceDisciplines
 */
class SpecialityDisciplineDependence extends Model
{
    protected $table = 'speciality_discipline_dependencies';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function discipline(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Discipline::class, 'id', 'discipline_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dependenceDisciplines(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Discipline::class,
            'speciality_discipline_dependencies_disciplines',
            'speciality_discipline_dependence_id',
            'discipline_id'
        );
    }
}
