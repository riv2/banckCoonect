<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-10-30
 * Time: 11:09
 */

namespace App;

use Carbon\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use App\{SyllabusTaskAnswer};
use Illuminate\Database\Eloquent\{Model,SoftDeletes};

/**
 * Class SyllabusTaskQuestions
 * @package App
 *
 * @property int id
 * @property int task_id
 * @property int points
 * @property string question
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class SyllabusTaskQuestions extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;


    use SoftDeletes;

    protected $table = 'syllabus_task_questions';

    protected $fillable = [
        'task_id',
        'points',
        'question',
    ];


    /**
     * @param $value
     */
    public function setQuestionAttribute($value)
    {
        $question = str_replace(['\'','"','`'],'', $value);
        $question = htmlClearfromMsTags($question);

        $this->attributes['question'] = $question;
    }


    /**
     * @param $value
     * @return mixed
     */
    public function getQuestionAttribute($value)
    {
        return str_replace(['\'','"','`'],'',$value);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function task()
    {
        return $this->hasOne(SyllabusTask::class, 'id', 'task_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answer()
    {
        return $this->hasMany(SyllabusTaskAnswer::class, 'question_id', 'id');
    }


    /**
     * @return int
     */
    public function answerCount()
    {

        return SyllabusTaskAnswer::
        where('question_id',$this->id)->
        count();
    }


    /**
     * @return int
     */
    public function answerCorrect()
    {

        return SyllabusTaskAnswer::
        where('question_id',$this->id)->
        where('correct',SyllabusTaskAnswer::STATUS_CORRECT)->
        count();
    }


    /**
     * @return int
     */
    public function answerUncorrect()
    {

        return SyllabusTaskAnswer::
        where('question_id',$this->id)->
        where('correct',SyllabusTaskAnswer::STATUS_UNCORRECT)->
        count();
    }


    /**
     * remove relation data
     */
    public function removeData()
    {

        $oSyllabusTaskAnswer = SyllabusTaskAnswer::
        where('question_id',$this->id)->
        delete();
    }


    /**
     * @return int
     */
    public function getCorrectAnswersCount()
    {

        $oAnswers = $this->answer;
        $iCount = 0;

        foreach ($oAnswers as $oAnswer) {
            if ($oAnswer->correct) {
                $iCount++;
            }
        }

        return $iCount;

    }


}