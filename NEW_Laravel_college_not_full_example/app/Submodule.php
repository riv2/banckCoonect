<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class Submodule
 * @package App
 * @property int ects
 * @property Discipline[] disciplines
 * @property array languageLevels
 * @property mixed dependence
 * @property mixed dependence2
 * @property mixed dependence3
 * @property mixed dependence4
 * @property mixed dependence5
 */
class Submodule extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class)->withTimestamps();
    }

    public function dependenciesWithoutResult($studentSubmodules) {
        $dependenceIdList = [];
        $dependenceIdList[] = $this->dependence ? explode(',', $this->dependence) : [];
        $dependenceIdList[] = $this->dependence2 ? explode(',', $this->dependence2) : [];
        $dependenceIdList[] = $this->dependence3 ? explode(',', $this->dependence3) : [];
        $dependenceIdList[] = $this->dependence4 ? explode(',', $this->dependence4) : [];
        $dependenceIdList[] = $this->dependence5 ? explode(',', $this->dependence5) : [];

        $dependenceModelGroupList = [];

        foreach ($dependenceIdList as $groupKey => $idGroup) {
            $models = collect($studentSubmodules)->whereIn('submodule_id', $idGroup)->all();

            if (count($models) == 0 && count($idGroup) > 0) {
                $dispList = self::whereIn('id', $idGroup)->get();
                if ($dispList) {
                    $dependenceModelGroupList[$groupKey] = $dispList;
                }
            }
        }

        return $dependenceModelGroupList;
    }

    public static function getDependentId(int $parentSubmoduleId) : ?int
    {
        $submodule = self::select('id')->where('dependence', $parentSubmoduleId)->first();

        return $submodule->id ?? null;
    }

    public function setAvailableLanguageLevels()
    {
        $languageLevels = [];

        // TODO do not use relation. Use DB query or model
        foreach ($this->disciplines as $discipline) {
            $languageLevels[$discipline->language_level] = Discipline::$languageLevels[$discipline->language_level];
        }
        ksort($languageLevels);

        $this->languageLevels = $languageLevels;

        unset($this->disciplines);
    }
}
