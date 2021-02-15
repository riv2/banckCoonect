<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-10-30
 * Time: 10:28
 */

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class SyllabusTaskAnswer
 * @package App
 *
 * @property int id
 * @property int question_id
 * @property string	answer
 * @property int points
 * @property int pcorrect
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class SyllabusTaskAnswer extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;


    use SoftDeletes;

    const STATUS_CORRECT    = 1;
    const STATUS_UNCORRECT  = 0;

    protected $table = 'syllabus_task_answer';

    protected $fillable = [
        'question_id',
        'answer',
        'points',
        'correct',
    ];


    /**
     * @param $value
     */
    public function setAnswerAttribute($value)
    {
        $answer = str_replace(['\'','"','`'],'',$value);
        $answer = htmlClearfromMsTags($answer);

        $this->attributes['answer'] = $answer;
    }


    /**
     * @param $value
     * @return mixed
     */
    public function getAnswerAttribute($value)
    {
        return str_replace(['\'','"','`'],'',$value);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function question()
    {
        return $this->hasOne(Syllabus::class, 'id', 'question_id');
    }


}
