<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntranceTest extends Model
{
    protected $table = 'entrance_test';

    protected $fillable = [
        'name',
        'total_points'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function quizeQuestions()
    {
        return $this
            ->belongsToMany(
                QuizQuestion::class,
                'entrance_test_quize',
                'entrance_test_id',
                'quize_question_id');
    }

    /**
     * @param $questionList
     */
    public function attachQuizeQuestions($questionList)
    {
        $deleteExcept = [];

        if(isset($questionList['new'])) {
            foreach ($questionList['new'] as $question) {
                $newQuestionId = QuizQuestion::createQuestion(null, $question);

                if($newQuestionId) {
                    $relation = new EntranceTestQuize();
                    $relation->entrance_test_id = $this->id;
                    $relation->quize_question_id = $newQuestionId;
                    $relation->save();

                    $deleteExcept[] = $newQuestionId;
                }
            }
        }

        if(isset($questionList['update'])) {
            foreach ($questionList['update'] as $k => $question) {
                $newQuestionId = QuizQuestion::updateQuestion($k, $question);

                if($newQuestionId) {
                    $deleteExcept[] = $newQuestionId;
                }
            }
        }

        $forDelete = EntranceTestQuize
            ::where('entrance_test_id', $this->id)
            ->whereNotIn('quize_question_id', $deleteExcept)
            ->get();

        if($forDelete)
        {
            foreach ($forDelete as $item) {
                $item->deleteWithQuestion();
            }
        }
    }
}
