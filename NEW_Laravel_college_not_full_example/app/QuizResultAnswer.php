<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class QuizResultAnswer
 * @package App
 *
 * @property int id
 * @property int question_id
 * @property int answer_id
 * @property int result_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property QuizQuestion question
 * @property QuizAnswer answer
 */
class QuizResultAnswer extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'quiz_result_answers';

    public function question()
    {
        return $this->hasOne(QuizQuestion::class, 'id', 'question_id');
    }

    public function answer()
    {
        return $this->hasOne(QuizAnswer::class, 'id', 'answer_id');
    }

    public function result()
    {
        return $this->hasOne(QuizResult::class, 'id', 'result_id');
    }
}
