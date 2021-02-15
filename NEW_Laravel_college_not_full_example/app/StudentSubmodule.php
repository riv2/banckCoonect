<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Auth;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class StudentSubmodule
 * @package App
 * @property $submodule_id
 * @property $student_id
 * @property Submodule submodule
 * @property bool chooseAvailable
 * @property string color
 * @property bool buyAvailable
 */
class StudentSubmodule extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'student_submodule';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function submodule()
    {
        return $this->hasOne(Submodule::class, 'id', 'submodule_id');
    }

    public static function getForStudyPage(int $userId, int $specialityId)
    {
        $studentSubmodules = self::getListForStudyPage($userId, $specialityId);

        $studentSubmodulesAll = self::where('student_id', $userId);

        foreach ($studentSubmodules as $studentSubmodule) {
            /** @var self $studentSubmodule */

            // Get dependencies
            $studentSubmodule->submodule->depWithoutResult = $studentSubmodule->submodule->dependenciesWithoutResult($studentSubmodulesAll);

            $studentSubmodule->setColor();
            $studentSubmodule->submodule->setAvailableLanguageLevels();
        }

        return $studentSubmodules;
    }

    /**
     * Set color for Study page
     */
    public function setColor()
    {
        $this->color = 'default';
    }

    /**
     * Has user discipline in his submodules?
     * @param int $userId
     * @param int $disciplineId
     * @return bool
     */
    public static function studentHasDiscipline(int $userId, int $disciplineId) : bool
    {
        $submoduleIds = self::getStudentSubmoduleIds($userId);

        $count = \DB::table('discipline_submodule')
            ->whereIn('submodule_id', $submoduleIds)
            ->where('discipline_id', $disciplineId)
            ->count();

        return $count > 0;
    }

    public static function getStudentSubmoduleIds(int $userId) : array
    {
        return self::select('submodule_id')->where('student_id', $userId)->pluck('submodule_id')->toArray();
    }

    public static function addDisciplineRelations(int $userId, int $disciplineId, int $submoduleId, int $specialityId) : int
    {
        // Add relation
        $link = StudentDiscipline::add($userId, $disciplineId, $submoduleId, $specialityId);

        if (empty($link->id)) {
            abort(500);
        }

        // Delete student-submodule relation
        self::deleteRelation($userId, $submoduleId);

        $secondSubmoduleId = Submodule::getDependentId($submoduleId);

        if (!empty($secondSubmoduleId)) {
            $languageLevel1 = Discipline::getLanguageLevel($disciplineId);

            $secondDisciplineId = DisciplineSubmodule::getSecondLanguageDisciplineId($secondSubmoduleId, $languageLevel1);

            if (!empty($secondDisciplineId)) {
                // Add relation
                StudentDiscipline::add($userId, $secondDisciplineId, $secondSubmoduleId, $specialityId);

                // Delete student-submodule relation
                self::deleteRelation($userId, $secondSubmoduleId);
            }
        }

        return $link->id;
    }

    public static function deleteRelation(int $userId, int $submoduleId) {
        return self::where('submodule_id', $submoduleId)->where('student_id', $userId)->delete();
    }

    public function setChooseAvailable($creditsSum, $semester, $studentCategory) : int
    {
        if (in_array($studentCategory, [Profiles::CATEGORY_STANDART_RECOUNT, Profiles::CATEGORY_TRANSFER])) {
            $creditsSum += $this->submodule->ects;

            $this->chooseAvailable = ($creditsSum <= StudentDiscipline::MAX_CHOOSE_CREDITS);
        } else {
            // FIXME семестр тут не назначается. Всегда будет true
            $this->chooseAvailable = ($this->semester <= ($semester + 1));
        }

        return $creditsSum;
    }

    /**
     * Is buying available
     * @param StudentSubmodule $studentDiscipline
     * @param int $boughtDisciplinesCredits
     * @param int|null $semesterCreditsLimit
     */
    public static function setBuyAvailable(self $studentDiscipline, int $boughtDisciplinesCredits, ?int $semesterCreditsLimit) : void
    {
        // Already payed or not available
        if (!$studentDiscipline->chooseAvailable) {
            $studentDiscipline->buyAvailable = false;
        } else {
            $limit = $semesterCreditsLimit ?? StudentDiscipline::MAX_CREDITS_AT_SEMESTER;
            $studentDiscipline->buyAvailable = ($studentDiscipline->submodule->ects + $boughtDisciplinesCredits) <= $limit;
        }
    }

    /**
     * @param int $userId
     * @param int $submoduleId
     * @return bool
     */
    public static function existsByUserAndSubmodule(int $userId, int $submoduleId) : bool
    {
        return self::where('submodule_id', $submoduleId)
        ->where('student_id', $userId)
        ->exists();
    }

    private static function getListForStudyPage(int $userId, int $specialityId)
    {
        return self::select([
            'student_submodule.*',
            'speciality_submodule.semester'
        ])
            ->with('submodule')

            // For getting semester
            ->join('speciality_submodule', function (JoinClause $join) use ($specialityId) {
                $join->on('speciality_submodule.submodule_id', '=', 'student_submodule.submodule_id')
                    ->where('speciality_submodule.speciality_id', '=', $specialityId);
            })

            ->where('student_submodule.student_id', $userId)
            ->get();
    }

    public static function getTopSubmodules(int $userId) : Collection
    {
        return self::join('submodules', 'submodules.id', '=', 'student_submodule.submodule_id')
            ->where('student_id', $userId)
            ->where('submodules.dependence', '')
            ->get();
    }
}
