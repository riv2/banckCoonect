<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Services\Auth;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class QuizAnswer
 * @property int id
 * @property int question_id
 * @property string answer
 * @property int points
 * @property bool correct
 * @property string img
 * @property Carbon updated_at
 * @property Carbon created_at
 *
 * @property-read string answer_text_only
 */
class QuizAnswer extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'quize_answers';

    protected $fillable = [
        'answer',
        'points',
        'correct'
    ];

    protected $casts = [
        'correct' => 'boolean',
    ];

    private $langs = ['ru', 'en', 'kz', 'fr', 'ar', 'de'];

    public function getAnswerTextOnlyAttribute() : string
    {
        return strip_tags($this->answer);
    }

    /**
     * @param int $questionId
     * @param array $excludeIds
     * @throws \Exception
     */
    public static function deleteNotIn(int $questionId, array $excludeIds)
    {
        self::where('question_id', $questionId)->whereNotIn('id', $excludeIds)->delete();
    }

    public static function getRandomId(int $questionId) : ?int
    {
        /** @var self $student */
        $student = self
            ::select(['id'])
            ->where('question_id', $questionId)
            ->inRandomOrder()
            ->first();

        return $student->id;
    }
}
