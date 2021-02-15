<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class DisciplineSubmodule
 * @package App
 *
 * @property int id
 * @property int discipline_id
 * @property int submodule_id
 */
class DisciplineSubmodule extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'discipline_submodule';

    public static function getDisciplineIdByLanguageLevel(int $submoduleId, int $languageLevel): ?int
    {
        $discipline = self
            ::select('discipline_id')
            ->join('disciplines', 'disciplines.id', '=', 'discipline_submodule.discipline_id')
            ->where('discipline_submodule.submodule_id', $submoduleId)
            ->where('disciplines.language_level', $languageLevel)
            ->first();

        return $discipline->discipline_id ?? null;
    }

    public static function getSecondLanguageDisciplineId(int $secondSubmoduleId, int $languageLevel1) : ?int
    {
        $languageLevel2 = $languageLevel1 + 1;

        $discipline = self
            ::select('discipline_id')
            ->join('disciplines', 'disciplines.id', '=', 'discipline_submodule.discipline_id')
            ->where('discipline_submodule.submodule_id', $secondSubmoduleId)
            ->where('disciplines.language_level', $languageLevel2)
            ->first();

        return $discipline->discipline_id ?? null;
    }
}
