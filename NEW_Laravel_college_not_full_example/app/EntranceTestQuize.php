<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntranceTestQuize extends Model
{
    protected $table = 'entrance_test_quize';

    public function deleteWithQuestion()
    {
        $question = QuizQuestion::where('id', $this->quize_question_id)->first();

        self::where('entrance_test_id', $this->entrance_test_id)
            ->where('quize_question_id', $this->quize_question_id)
            ->delete();

        if($question)
        {
            return $question->deleteWithAnswers();
        }

        return false;
    }
}
