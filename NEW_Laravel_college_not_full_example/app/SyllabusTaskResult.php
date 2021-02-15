<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-10-30
 * Time: 11:20
 */

namespace App;

use App\{
    SyllabusTask,
    SyllabusTaskResultAnswer
};
use Carbon\Carbon;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Support\Facades\{Log};
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class SyllabusTaskResult
 * @package App
 *
 * @property int id
 * @property int user_id
 * @property int syllabus_id
 * @property bool payed
 * @property int value
 * @property int points
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property bool blur
 * @property int task_id
 */
class SyllabusTaskResult extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use SoftDeletes;

    protected $table = 'syllabus_task_result';

    protected $fillable = [
        'user_id',
        'syllabus_id',
        'student_discipline_id',
        'payed',
        'value',
        'points',
        'task_id',
        'blur',
    ];

    public $casts = [
        'payed' => 'boolean',
        'blur' => 'boolean'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function syllabus()
    {
        return $this->hasOne(Syllabus::class, 'id', 'syllabus_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function studentsDisciplines()
    {
        return $this->hasOne(StudentDiscipline::class, 'id', 'student_discipline_id');
    }

    /**
     * @return int
     */
    public function getPercent()
    {
        $response = 0;
        if (!empty($this->value) && !empty($this->points)) {
            $response = intval(($this->value * 100) / $this->points);
        }
        return $response;
    }

    /**
     * @param int $taskId
     * @param int $userId
     * @return int
     *
     * @codeCoverageIgnore
     */
    public static function attemptsCount(int $taskId, int $userId) : int
    {
        return self
            ::where('task_id', $taskId)
            ->where('user_id', $userId)
            ->count();
    }


    /**
     * @param $iResultId
     * @param $iTaskId
     * @return int
     */
    public static function recalculationData( $iResultId, $iTaskId )
    {

        // init
        $iCorrectPoint = 0;
        $iCurPoint = 0;
        $iPercent  = 0;

        $oSyllabusTaskResult = self::
        where('id',$iResultId)->
        whereNull('deleted_at')->
        first();

        // достаем правильные баллы по заданию
        $oSyllabusTask = SyllabusTask::
        with('questions')->
        with('questions.answer')->
        where('id',$iTaskId)->
        whereNull('deleted_at')->
        first();

        // считаем корректные баллы по заданию
        if( !empty($oSyllabusTask) && !empty($oSyllabusTask->questions) )
        {
            foreach( $oSyllabusTask->questions as $itemSTQ )
            {
                if( !empty($itemSTQ->answer) )
                {
                    foreach( $itemSTQ->answer as $itemA )
                    {
                        if( !empty($itemA->points) && !empty($itemA->correct) )
                        {
                            $iCorrectPoint += $itemA->points;
                        }
                    }
                }
            }
        }

        // достаем ответы студика по заданию
        $oSyllabusTaskResultAnswer = SyllabusTaskResultAnswer::
        with('answer')->
        where('result_id',$iResultId)->
        whereNull('deleted_at')->
        get();

        // считаем баллы студента по заданию
        if( !empty($oSyllabusTaskResultAnswer) )
        {
            foreach( $oSyllabusTaskResultAnswer as $itemSTRA )
            {
                if( !empty($itemSTRA->answer) && !empty($itemSTRA->answer->points) && !empty($itemSTRA->answer->correct) )
                {
                    $iCurPoint += $itemSTRA->answer->points;
                }
            }
        }

        //Log::info('$iCorrectPoint: ' . var_export($iCorrectPoint,true));
        //Log::info('$iCurPoint: ' . var_export($iCurPoint,true));

        // находим %
        if( !empty($iCorrectPoint) )
        {
            $iPercent = intval( ($iCurPoint * 100) / $iCorrectPoint );
        }

        // если платная попытка сдачи, то возвращаем результат
        if( !empty($oSyllabusTaskResult->payed) )
        {
            return $oSyllabusTaskResult->points;
        }

        $oSyllabusTaskResult->value  = $iPercent;
        $oSyllabusTaskResult->points = $iCurPoint;
        $oSyllabusTaskResult->save();
        unset($oSyllabusTaskResult);

        return $iCurPoint;

    }

}