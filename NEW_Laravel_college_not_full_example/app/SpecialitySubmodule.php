<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class SpecialitySubmodule
 * @package App
 * @property int semester
 * @property string language_type
 */
class SpecialitySubmodule extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'speciality_submodule';

    public static function getSemester(int $specialityId, int $submoduleId)
    {
        $sd = self::select('semester')
            ->where('speciality_id', $specialityId)
            ->where('submodule_id', $submoduleId)
            ->first();

        return $sd->semester ?? null;
    }

    public static function getByDisciplineId(int $specialityId, int $disciplineId) : ?self
    {
        return self::leftJoin('discipline_submodule', 'discipline_submodule.submodule_id', '=', 'speciality_submodule.submodule_id')
            ->where('discipline_submodule.discipline_id', $disciplineId)
            ->where('speciality_id', $specialityId)
            ->first();
    }

    public static function getLanguageType(int $specialityId, int $submoduleId) : ?string
    {
        $ss = self::select('language_type')
            ->where('speciality_id', $specialityId)
            ->where('submodule_id', $submoduleId)
            ->first();

        return $ss->language_type ?? null;
    }

    public static function disciplineExists(int $specialityId, int $disciplineId) : bool
    {
        return self::leftJoin('discipline_submodule', 'discipline_submodule.submodule_id', '=', 'speciality_submodule.submodule_id')
            ->where('discipline_submodule.discipline_id', $disciplineId)
            ->where('speciality_id', $specialityId)
            ->exists();
    }
}
