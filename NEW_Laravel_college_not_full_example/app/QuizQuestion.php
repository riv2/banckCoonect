<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Services\Auth;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class QuizQuestion
 * @package App
 *
 * @property int id
 * @property int discipline_id
 * @property string question
 * @property int teacher_id
 * @property int total_points
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property QuizAnswer[]|QuizAnswer answers
 * @property QuizeAudiofile[]|QuizeAudiofile audiofiles
 *
 * @property-read bool has_multi_answer
 * @property-read int correct_answers_count
 * @property-read string question_text_only
 */
class QuizQuestion extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'quize_questions';
    protected $fillable = [
        'question','lang'
    ];

    private $langs = ['ru', 'en', 'kz', 'fr', 'ar', 'de'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'question_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function audiofiles()
    {
        return $this->hasMany(QuizeAudiofile::class, 'quize_question_id', 'id')->orderBy('id', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function syllabuses()
    {
        return $this
            ->belongsToMany(
                Syllabus::class,
                'syllabus_quize_questions',
                'quize_question_id',
                'syllabus_id'
            );
    }

    public function getCorrectAnswersCountAttribute() : int
    {
        $count = 0;

        foreach ($this->answers as $answer) {
            if ($answer->correct) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return |null
     */
    public function getSyllabusAttribute()
    {
        return $this->syllabuses[0] ?? null;
    }

    /**
     * @return string
     */
    public function getDefaultLanguage()
    {
        foreach ($this->langs as $lang)
        {
            if($lang == 'ru' && isset($this->attributes['question']) && $this->attributes['question'])
            {
                return $lang;
            }

            if($lang != 'ru')
            {
                if(isset($this->attributes['question_' . $lang]) && $this->attributes['question_' . $lang])
                {
                    return $lang;
                }
            }
        }

        return false;
    }

    /**
     * @param $field
     * @param $lang
     * @return bool
     */
    public function emptyFieldByLang($field, $lang)
    {
        $suffix = $lang == 'ru' ? '' : '_' . $lang;
        $fieldName = $field . $suffix;

        return !(isset($this->attributes[$fieldName]) && $this->attributes[$fieldName]);
    }

    /**
     * @param $field
     * @return string
     */
    public function fieldByLang($field)
    {
        $lang = Auth::user()->studentProfile->education_lang ?? null;

        if(!$this->emptyFieldByLang($field, $lang))
        {
            return $lang == 'ru' ? $this->attributes[$field ] : $this->attributes[$field . '_' . $lang];
        }

        $lang = $this->getDefaultLanguage();

        if($lang && !$this->emptyFieldByLang($field, $lang))
        {
            return $lang == 'ru' ? $this->attributes[$field] : $this->attributes[$field . '_' . $lang];
        }

        return '';
    }

    /**
     * @return mixeds
     */
    /*public function getQuestionAttribute()
    {
        return $this->fieldByLang('question');
    }*/

    /**
     * @return mixed
     */
    public function getActualQuestionAttribute()
    {
        if(isset($this->attributes['question']) && $this->attributes['question'])
        {
            return $this->attributes['question'];
        }

        if(isset($this->attributes['question_kz']) && $this->attributes['question_kz'])
        {
            return $this->attributes['question_kz'];
        }

        if(isset($this->attributes['question_en']) && $this->attributes['question_en'])
        {
            return $this->attributes['question_en'];
        }

        if(isset($this->attributes['question_fr']) && $this->attributes['question_fr'])
        {
            return $this->attributes['question_fr'];
        }

        if(isset($this->attributes['question_ar']) && $this->attributes['question_ar'])
        {
            return $this->attributes['question_ar'];
        }

        if(isset($this->attributes['question_de']) && $this->attributes['question_de'])
        {
            return $this->attributes['question_de'];
        }
    }

    /**
     * @return bool
     */
    public function getHasMultiAnswerAttribute() : bool
    {
        return $this->answers->where('correct', true)->count() > 1;
    }

    public function getQuestionTextOnlyAttribute() : string
    {
        return strip_tags($this->question);
    }

    /**
     * @param $answerList
     * @return bool
     * @throws \Exception
     */
    public function syncAnswers($answerList)
    {
        $taboo = [];

        foreach ($answerList as $item) {
            $answer = null;

            if (!$item['id']) {
                $answer = QuizAnswer::where('id', $item['id'])->first();
            }

            if (!$answer) {
                $answer = new QuizAnswer();
                $answer->question_id = $this->id;
            }

            $answer->fill($item);
            $answer->save();

            $taboo[] = $answer->id;
        }

        QuizAnswer::deleteNotIn($this->id, $taboo);

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function attachAudio($file)
    {
        $filename = 'audio_' . $this->id . '_' . time() . rand(1, 1000) . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

        $quizeAudiofile = new QuizeAudiofile();
        $quizeAudiofile->quize_question_id = $this->id;
        $quizeAudiofile->filename = $filename;
        $quizeAudiofile->original_filename = $file->getClientOriginalName();

        $file->move(public_path('audio'), $filename);

        return $quizeAudiofile->save();
    }

    /**
     * @param $file
     * @return bool
     */
    public function attachAudioJson($file)
    {
        if(!$file['source'] || !$file['filename'])
        {
            return false;
        }

        $filename = 'audio_' . $this->id . '_' . time() . rand(1, 1000) . '.' . pathinfo($file['filename'], PATHINFO_EXTENSION);

        $quizeAudiofile = new QuizeAudiofile();
        $quizeAudiofile->quize_question_id = $this->id;
        $quizeAudiofile->filename = $filename;
        $quizeAudiofile->original_filename = $file['filename'];
        file_put_contents(public_path('audio') . '/' . $filename, base64_decode($file['source']));

        return $quizeAudiofile->save();
    }

    /**
     * @param $syllabus
     * @param $params
     * @return mixed
     */
    static function createQuestion($disciplineId, $params, $files = null)
    {
        /*Create Question*/
        $question = new self();
        $question->discipline_id = $disciplineId;
        $question->teacher_id = 0;
        $question->total_points = self::getTotalPoints($params['answers']);
        $question->fill($params);

        $question->save();

        if(isset($files['audio']))
        {
            $question->attachAudio($files['audio']);
        }

        $deleteExcept = [];
        /*Create answers*/
        if(isset($params['answers']['new'])) {
            foreach ($params['answers']['new'] as $answer) {
                $answerModel = new QuizAnswer();
                $answerModel->fill($answer);
                $answerModel->question_id = $question->id;
                $answerModel->points = $answer['points'] ?? 0;
                $answerModel->save();

                $deleteExcept[] = $answerModel->id;
            }
        }

        /*Update answers*/
        if(isset($params['answers']['update'])) {
            foreach ($params['answers']['update'] as $akey => $answer) {
                $answerModel = QuizAnswer::where('id', $akey)->first();

                if ($answerModel) {
                    $answerModel->fill($answer);
                    $answerModel->points = $answer['points'] ?? 0;
                    $answerModel->save();

                    $deleteExcept[] = $answerModel->id;
                }
            }
        }

        QuizAnswer
            ::where('question_id', $question->id)
            ->whereNotIn('id', $deleteExcept)
            ->delete();

        return $question->id;
    }

    static function updateQuestion($questionId, $params, $files = null)
    {
        /*Create Question*/
        $question = QuizQuestion::where('id', $questionId)->first();

        if(!$question)
        {
            return false;
        }

        $question->total_points = self::getTotalPoints($params['answers']);
        $question->fill($params);

        $question->save();

        if(isset($files['audio']))
        {
            QuizeAudiofile::where('quize_question_id', $question->id)->delete();
            $question->attachAudio($files['audio']);
        }

        $deleteExcept = [];
        /*Create answers*/
        if(isset($params['answers']['new'])) {
            foreach ($params['answers']['new'] as $answer) {
                $answerModel = new QuizAnswer();
                $answerModel->fill($answer);
                $answerModel->question_id = $question->id;
                $answerModel->points = $answer['points'] ?? 0;
                $answerModel->save();

                $deleteExcept[] = $answerModel->id;
            }
        }

        /*Update answers*/
        if(isset($params['answers']['update'])) {
            foreach ($params['answers']['update'] as $akey => $answer) {
                $answerModel = QuizAnswer::where('id', $akey)->first();

                if ($answerModel) {
                    $answerModel->fill($answer);
                    $answerModel->points = $answer['points'] ?? 0;
                    $answerModel->save();

                    $deleteExcept[] = $answerModel->id;
                }
            }
        }

        QuizAnswer
            ::where('question_id', $question->id)
            ->whereNotIn('id', $deleteExcept)
            ->delete();

        return $question->id;
    }

    /**
     * @return mixed
     */
    public function deleteWithAnswers()
    {
        QuizAnswer::where('question_id', $this->id)->delete();

        return $this->delete();
    }

    /**
     * @param $answers
     */
    static function getTotalPoints($answers)
    {
        $total = 0;

        if(isset($answers['new'])) {
            foreach ($answers['new'] as $answer) {
                $total += $answer['points'] ?? 0;
            }
        }

        if(isset($answers['update'])) {
            foreach ($answers['update'] as $answer) {
                $total += $answer['points'] ?? 0;
            }
        }

        return $total;
    }

    /**
     * @param $disciplineId
     * @param $correctCount
     * @param $syllabusLang
     * @param $limit
     * @return QuizQuestion[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    static function getByCorrectCount($disciplineId, $correctCount, $syllabusLang, $limit, $tabooIds = [])
    {
        $question = QuizQuestion
            ::with([
                'answers' => function ($query) {
                    $query->inRandomOrder();
                }
            ])
            ->whereHas('answers', function($query) use($correctCount){
                $query->where('correct', true);
            }, '=', $correctCount)
            ->whereHas('syllabuses', function($query) use($syllabusLang){
                $query->where('language', $syllabusLang);
            })
            ->where('discipline_id', $disciplineId)
            ->inRandomOrder()
            ->limit($limit);

        if($tabooIds)
        {
            $question->whereNotIn('id', $tabooIds);
        }

        return $question->get();
    }

    /**
     * @return int
     */
    public function getMaxPoints() : int
    {
        $points = 0;

        foreach ($this->answers as $answer) {
            if ($answer->correct) {
                $points += $answer->points;
            }
        }

        return $points;
    }

    /**
     * @return int
     */
    public function getCorrectAnswersCount() : int
    {
        $count = 0;

        foreach ($this->answers as $answer) {
            if ($answer->correct) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param QuizQuestion[] $questions
     * @return int
     */
    public static function getMaxPointsFromArray(array $questions) : int
    {
        $maxPoints = 0;

        foreach ($questions as $question) {
            $maxPoints += $question->getMaxPoints();
        }

        return $maxPoints;
    }

    /**
     * @param QuizQuestion[] $questions
     * @return bool
     */
    public static function hasAudio(array $questions) : bool
    {
        $hasAudio = false;

        foreach ($questions as $question) {
            if (count($question->audiofiles) > 0) {
                $hasAudio = true;
            }
        }

        return $hasAudio;
    }

    public static function getWithAnswers(int $id) : ?self
    {
        return self
            ::select(['id'])
            ->with(['answers' => function ($query) {
                $query->select([
                    'id',
                    'question_id',
                    'points',
                    'correct'
                ]);
            }])
            ->where('id', $id)
            ->first();
    }

    public static function getPointTotalAndAnswers(array $answers) : array
    {
        $totalPoints = 0;
        $answersForSave = [];
        $notEmptyAnswers = 0;

        foreach ($answers as $userAnswer) {
            $answerPoints = 0;
            $questionId = $userAnswer['id'];
            $userAnswersIds = (array)$userAnswer['answer'];

            // No answers
            if (empty($userAnswer['answer'])) {
                $answersForSave[] = [
                    'question_id' => $questionId,
                    'answer_id' => null
                ];

                continue;
            } else {
                $notEmptyAnswers++;
            }

            $question = self::getWithAnswers($questionId);

            // Похоже на попытку взлома. В JS есть ограничение. Не считаем правильные
            if (count($userAnswersIds) > $question->correct_answers_count) {
                foreach ($userAnswersIds as $answerId) {
                    $answersForSave[] = [
                        'question_id' => $question->id,
                        'answer_id' => $answerId
                    ];
                }
                continue;
            }

            // Варианты ответов
            foreach ($question->answers as $answer) {
                // ответ юзера
                if (in_array($answer->id, $userAnswersIds)) {
                    $answersForSave[] = [
                        'question_id' => $question->id,
                        'answer_id' => $answer->id
                    ];

                    // Correct answer
                    if ($answer->correct) {
                        $answerPoints += $answer->points;
                    }
                }
            }

            $totalPoints += $answerPoints;
        }

        $percentsOfSelectedAnswers = (int)round($notEmptyAnswers / count($answers) * 100, 0);

        return [$totalPoints, $answersForSave, $percentsOfSelectedAnswers];
    }

    public function getAnswersForSnapshot() : array
    {
        $answers = [];

        foreach ($this->answers as $answer) {
            $answers[] = [
                'id' => $answer->id,
                'answer' => $answer->answer_text_only,
                'points' => $answer->points,
                'correct' => $answer->correct
            ];
        }

        return $answers;
    }

    public static function getRandom() : self
    {
        return self
            ::inRandomOrder()
            ->first();
    }
}
