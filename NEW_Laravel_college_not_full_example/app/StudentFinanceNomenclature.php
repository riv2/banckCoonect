<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int id
 * @property int user_id
 * @property int finance_nomenclature_id
 * @property int student_discipline_id
 * @property string comment
 * @property int cost
 * @property int semester
 * @property float balance_before
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class StudentFinanceNomenclature extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    const COMMENT_TEST1_TRIAL = 'Test1Trial';
    const COMMENT_EXAM_TRIAL = 'ExamTrial';
    const COMMENT_REMOTE_ACCESS = 'RemoteAccess';

    protected $table = 'student_finance_nomenclature';

    public static function add(
        int $userId,
        FinanceNomenclature $service,
        int $semester,
        float $balanceBefore,
        ?int $studentDisciplineId = null,
        ?string $comment = null
    ) : ?self
    {
        $link = new self;
        $link->user_id = $userId;
        $link->finance_nomenclature_id = $service->id;
        $link->student_discipline_id = $studentDisciplineId;
        $link->comment = $comment;
        $link->cost = $service->cost;
        $link->semester = $semester;
        $link->balance_before = $balanceBefore;
        $link->save();

        return $link ?? null;
    }

    public static function isBought(int $userId, int $serviceId, ?int $semester = null): bool
    {
        $service = self::where('user_id', $userId)->where('finance_nomenclature_id', $serviceId);

        if (!empty($semester)) {
            $service->where('semester', $semester);
        }

        return $service->exists();
    }

    public static function getBoughtServiceIds($userId, $semester)
    {
        return self::select('finance_nomenclature_id')
            ->join('finance_nomenclatures', 'finance_nomenclatures.id', '=', 'student_finance_nomenclature.finance_nomenclature_id')
            ->where('user_id', $userId)
            ->where(function ($query) use ($semester) {
                $query->where('finance_nomenclatures.only_one', 1);
                $query->orWhere(function ($query) use ($semester) {
                    $query->where('finance_nomenclatures.only_one_per_semester', 1);
                    $query->where('student_finance_nomenclature.semester', $semester);
                });
            })
            ->pluck('finance_nomenclature_id')
            ->toArray();
    }

    /**
     * @param int $userId
     * @param FinanceNomenclature $financeNomenclature
     * @param int $currentSemester
     * @param float $balanceBeforeCall
     * @param int $studentDisciplineId
     * @codeCoverageIgnore
     */
    public static function addTest1Trial(
        int $userId,
        FinanceNomenclature $financeNomenclature,
        int $currentSemester,
        float $balanceBeforeCall,
        int $studentDisciplineId
    ) : void
    {
        self::add($userId, $financeNomenclature, $currentSemester, $balanceBeforeCall, $studentDisciplineId, self::COMMENT_TEST1_TRIAL);
    }

    /**
     * @param int $userId
     * @param FinanceNomenclature $financeNomenclature
     * @param int $currentSemester
     * @param float $balanceBeforeCall
     * @param int $studentDisciplineId
     * @codeCoverageIgnore
     */
    public static function addExamTrial(
        int $userId,
        FinanceNomenclature $financeNomenclature,
        int $currentSemester,
        float $balanceBeforeCall,
        int $studentDisciplineId
    ) : void
    {
        self::add($userId, $financeNomenclature, $currentSemester, $balanceBeforeCall, $studentDisciplineId, self::COMMENT_EXAM_TRIAL);
    }

    /**
     * @param int $userId
     * @param FinanceNomenclature $financeNomenclature
     * @param int $currentSemester
     * @param float $balanceBeforeCall
     * @param int $studentDisciplineId
     * @codeCoverageIgnore
     */
    public static function addRemoteAccess(
        int $userId,
        FinanceNomenclature $financeNomenclature,
        int $currentSemester,
        float $balanceBeforeCall,
        int $studentDisciplineId
    ) : void
    {
        self::add($userId, $financeNomenclature, $currentSemester, $balanceBeforeCall, $studentDisciplineId, self::COMMENT_REMOTE_ACCESS);
    }
}
