<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PollQuestion extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'polls_questions';

    public $timestamps = false;

    protected $casts = [
        'is_multiple' => 'boolean',
    ];

    /**
     * Get the default answers for the question.
     */
    public function answers()
    {
        return $this->hasMany('App\PollAnswer', 'question_id');
    }

    /**
     * Get the user answers for the question.
     */
    public function userAnswers()
    {
        return $this->hasMany('App\PollUserAnswer', 'question_id');
    }

    public static function getReportByQuestion($poll_id)
    {
        return self::select(['polls_questions.id', 'polls_questions.text_ru', 'pua.answer', DB::raw('count(pua.answer) AS count')])
                ->leftJoin('poll_users_answers AS pua', 'polls_questions.id', '=', 'pua.question_id')
                ->where('polls_questions.poll_id', $poll_id)
                ->groupBy(['pua.answer', 'polls_questions.id', 'polls_questions.text_ru'])
                ->orderBy('polls_questions.id')
                ->get();
    }
}
