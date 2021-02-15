<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SyllabusQuizeQuestion extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'syllabus_quize_questions';

    /**
     * @return bool|null
     */
    public function deleteWithQuestion()
    {
        $question = QuizQuestion::where('id', $this->quize_question_id)->first();

        self::where('syllabus_id', $this->syllabus_id)
            ->where('quize_question_id', $this->quize_question_id)
            ->delete();

        if($question)
        {
            return $question->deleteWithAnswers();
        }

        return false;
    }
}
