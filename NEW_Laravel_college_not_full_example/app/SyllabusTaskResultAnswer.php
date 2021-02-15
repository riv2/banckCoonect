<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-10-30
 * Time: 11:32
 */

namespace App;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use OwenIt\Auditing\Contracts\Auditable;

class SyllabusTaskResultAnswer extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;


    use SoftDeletes;

    protected $table = 'syllabus_task_result_answer';

    protected $fillable = [
        'question_id',
        'answer_id',
        'result_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function question()
    {
        return $this->hasOne(Syllabus::class, 'id', 'question_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function answer()
    {
        return $this->hasOne(SyllabusTaskAnswer::class, 'id', 'answer_id')->
        whereNull('deleted_at');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function result()
    {
        return $this->hasOne(SyllabusTaskResult::class, 'id', 'result_id');
    }


}