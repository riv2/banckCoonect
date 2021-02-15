<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Services\StudentRating;
use Illuminate\Support\Facades\DB;

class QuizeResultKge extends Model
{
    protected $table = 'quize_result_kge';

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function resultAnswers()
    {
        return $this->hasMany(QuizeResultAnswerKge::class, 'result_kge_id', 'id');
    }

    /**
     * @param $valuePercent
     * @param $creditSum
     * @return bool
     */
    public function setValue($valuePercent)
    {
        $this->value    = $valuePercent;
        $this->points   = StudentRating::getFinalResultPoints($valuePercent);
        $this->letter   = StudentRating::getLetter($valuePercent);
        $this->gpi      = StudentRating::getDisciplineGpa($valuePercent, 1);

        return $this->save();
    }

    /**
     * @param $answerList
     */
    public function setResultAnswers($answerList)
    {
        foreach ($answerList as $k => $item)
        {
            $answerList[$k]['result_kge_id'] = $this->id;
            $answerList[$k]['created_at'] = DB::raw('NOW()');
        }

        QuizeResultAnswerKge::insert($answerList);
    }

}
