<?php

namespace App;

use Auth;
use Carbon\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use App\{Models\Discipline\DisciplineSemester, Models\Speciality\SpecialityDisciplineSemester, Profiles};
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class SpecialityDiscipline
 * @package App
 *
 * @property int id
 * @property int speciality_id
 * @property int discipline_id
 * @property string language_type
 * @property string exam_type
 * @property int exam
 * @property string pressmark
 * @property int semester
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property string discipline_cicle
 * @property string mt_tk
 * @property int has_coursework
 * @property int verbal_sro
 * @property int sro_hours
 * @property int laboratory_hours
 * @property int practical_hours
 * @property int lecture_hours
 */
class SpecialityDiscipline extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    const LANGUAGE_TYPE_NATIVE  = 'native';
    const LANGUAGE_TYPE_SECOND  = 'second';
    const LANGUAGE_TYPE_OTHER   = 'other';

    protected $table = 'speciality_discipline';

    protected $fillable = [
        'discipline_cicle',
        'mt_tk',
        'has_coursework',
        'pressmark',
        'semester',
        'control_form',
        'verbal_sro',
        'sro_hours',
        'laboratory_hours',
        'practical_hours',
        'lecture_hours',
        'cloned'
    ];

    public static function getDisciplineIdsExcludingIds(int $specialityId, array $excludingDisciplinesIds)
    {
        return self::select(['discipline_id'])
            ->where('speciality_id', $specialityId)
            ->whereNotIn('discipline_id', $excludingDisciplinesIds)
            ->pluck('discipline_id')
            ->toArray();
    }

    public static function getOne(int $specialityId, int $disciplineId) : ?self
    {
        return self::where('speciality_id', $specialityId)
            ->where('discipline_id', $disciplineId)
            ->first();
    }

    public static function getSemester(int $specialityId, int $disciplineId) : ?string
    {
        $sd = self::select('semester')
            ->where('speciality_id', $specialityId)
            ->where('discipline_id', $disciplineId)
            ->first();

        return $sd->semester ?? null;
    }

    public static function getLanguageType(int $specialityId, int $disciplineId) : ?string
    {
        $sd = self::select('language_type')
            ->where('speciality_id', $specialityId)
            ->where('discipline_id', $disciplineId)
            ->first();

        return $sd->language_type ?? null;
    }

    public static function isExist(int $specialityId, int $disciplineId) : bool
    {
        return self::where('speciality_id', $specialityId)
            ->where('discipline_id', $disciplineId)
            ->exists();
    }

    /**
     * @return string
     */
    public function getLangForSRO()
    {

        $sLang = false;
        if( !empty(Auth::user()->studentProfile) ) {

            switch ($this->language_type) {
                // тот же язык который выбрал студик
                case SpecialityDiscipline::LANGUAGE_TYPE_NATIVE:
                    $sLang = Auth::user()->studentProfile->education_lang;
                    break;
                // инверсия языка (ru => kz or kz => ru)
                case SpecialityDiscipline::LANGUAGE_TYPE_SECOND:
                    $sLang = (Auth::user()->studentProfile->education_lang == Profiles::EDUCATION_LANG_RU) ? Profiles::EDUCATION_LANG_KZ : Profiles::EDUCATION_LANG_RU;
                    break;
                // выбираем иностранный язык en
                case SpecialityDiscipline::LANGUAGE_TYPE_OTHER:
                    $sLang = Profiles::EDUCATION_LANG_EN;
                    break;
            }
        }
        return $sLang;

    }

    public function semesters()
    {
        return $this->hasMany(SpecialityDisciplineSemester::class, 'speciality_discipline_id', 'id');
    }

    public function disciplineSemesters()
    {
        return $this->hasMany(DisciplineSemester::class, 'discipline_id', 'discipline_id');
    }
}
