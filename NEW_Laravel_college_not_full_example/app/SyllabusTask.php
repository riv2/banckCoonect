<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-10-30
 * Time: 9:53
 */

namespace App;

use App\SyllabusTaskQuestions;
use App\SyllabusTaskResult;
use App\SyllabusTaskUserPay;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;
use App\Profiles;
use App\SyllabusTaskAnswer;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Support\Facades\{Log};
use Intervention\Image\Facades\{Image};

/**
 * Class SyllabusTask
 * @package App
 *
 * @property int id
 * @property int syllabus_id
 * @property int discipline_id
 * @property string language
 * @property string type
 * @property int points
 * @property Carbon event_date
 * @property string event_place
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property string text_data
 * @property string img_data
 * @property string link_data
 * @property string audio_data
 * @property string video_data
 *
 * @property-read SyllabusTaskUserPay pay
 * @property-read SyllabusTaskQuestions[] questions
 *
 * @property bool proceedButtonShow
 * @property bool retakeButtonShow
 */
class SyllabusTask extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use SoftDeletes;

    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'img';
    const TYPE_LINK = 'link';
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';
    const TYPE_EVENT = 'event';
    const TYPE_ESSAY = 'essay';

    const FREE_ATTEMPTS = 2;
    const CORONA_FREE_ATTEMPTS = 5;

    protected $table = 'syllabus_task';

    protected $fillable = [
        'syllabus_id',
        'discipline_id',
        'language',
        'type',
        'points',
        'event_date',
        'event_place',
        'text_data',
        'img_data',
        'link_data',
        'audio_data',
        'video_data',
        'week',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function discipline()
    {
        return $this->hasOne(Discipline::class, 'id', 'discipline_id');
    }

    /**
     * @return mixed
     */
    public function taskResult()
    {
        return $this->hasOne(SyllabusTaskResult::class, 'task_id', 'id')->
        where('user_id', Auth::user()->id)->
        whereNull('deleted_at')->
        orderBy('value', 'DESC');
    }

    public function taskResultAll()
    {
        return $this->hasMany(SyllabusTaskResult::class, 'task_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function syllabus()
    {
        return $this->hasOne(Syllabus::class, 'id', 'syllabus_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasMany(SyllabusTaskQuestions::class, 'task_id', 'id')->
        whereNull('deleted_at');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function pay()
    {
        return $this
            ->hasOne(SyllabusTaskUserPay::class, 'task_id', 'id')
            ->where('syllabus_task_user_pay.user_id', Auth::user()->id)
            ->orderBy('syllabus_task_user_pay.id', 'DESC');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payCount()
    {
        return $this
            ->hasMany(SyllabusTaskUserPay::class, 'task_id', 'id')
            ->where('user_id', Auth::user()->id);
    }

    /**
     * @param $value
     * @return false|string
     */
    public function setEventDateAttribute($value)
    {
        $this->attributes['event_date'] = date('Y-m-d', strtotime($value));
    }

    /**
     * @param $value
     */
    public function setTextDataAttribute($value)
    {
        $this->attributes['text_data'] = htmlClearfromMsTags($value);
    }

    /**
     * @return false|string
     */
    public function getEventDateAttribute($value)
    {
        if (!empty($value)) {
            return date('Y-m-d', strtotime($value));
        }
        return null;
    }

    /**
     * @param $value
     */
    public function getImgDataAttribute($value)
    {
        if ($value) {
            return $value;
        }
    }

    /**
     * @param $value
     */
    public function getAudioDataAttribute($value)
    {
        if ($value) {
            return $value;
        }
    }

    /**
     * remove task and relation data
     * @param
     * @return void
     */
    public function removeData()
    {
        if (!empty($this->questions)) {
            foreach ($this->questions as $questions) {
                if (!empty($questions->answer)) {
                    foreach ($questions->answer as $answer) {
                        $answer->delete();
                    }
                }
                $questions->delete();
            }
        }
    }


    /**
     * @param $iSyllabusId
     * @return bool
     */
    public static function getTaskData($iSyllabusId)
    {
        $aData = [];
        if (!empty($iSyllabusId)) {
            // get task data
            $oSyllabusTask = self::
            where('syllabus_id', $iSyllabusId)->
            whereNull('deleted_at')->
            get();
            if (!empty($oSyllabusTask) && (count($oSyllabusTask) > 0)) {
                foreach ($oSyllabusTask as $itemST) {
                    $aData['syllabusTaskIds'][] = $itemST->id;
                    $aData['syllabusTaskData'][$itemST->id] = $itemST;
                }
            }

            // get task question
            if (!empty($aData['syllabusTaskData']) && (count($aData['syllabusTaskData']) > 0)) {
                foreach ($aData['syllabusTaskData'] as $itemTask) {
                    if (!empty($itemTask->questions)) {
                        foreach ($itemTask->questions as $question) {
                            $aData['syllabusTaskQuestionsIds'][$itemTask->id][] = $question->id;
                            $aData['syllabusTaskQuestionsData'][$itemTask->id][$question->id] = $question;
                        }
                    }
                }
            }

            // get task question audio and answer
            if (!empty($aData['syllabusTaskQuestionsData']) && (count($aData['syllabusTaskQuestionsData']) > 0)) {
                foreach ($aData['syllabusTaskQuestionsData'] as $syllabusT) {
                    foreach ($syllabusT as $itemTQ) {
                        if (!empty($itemTQ->answer)) {
                            foreach ($itemTQ->answer as $itemSTAn) {
                                $aData['syllabusTaskAnswerIds'][$itemTQ->id][] = $itemSTAn->id;
                                $aData['syllabusTaskAnswerData'][$itemTQ->id][$itemSTAn->id] = $itemSTAn;
                            }
                        }
                    }
                }
            }


            if (count($aData) > 0) {
                return $aData;
            }
        }

        return false;
    }


    /**
     * @return bool
     */
    public function saveData($syllabusTaskImgData = null, $syllabusTaskAudioData = null)
    {
        if (!empty($syllabusTaskImgData) && !empty($this->img_data)) {
            $filename = 'img_' . $this->id . '_' . time() . rand(1, 1000) . '.' . pathinfo($this->img_data, PATHINFO_EXTENSION);
            $this->img_data = $filename;
            file_put_contents(public_path('images/uploads/syllabustask') . '/' . $filename, base64_decode($syllabusTaskImgData));

            if (filesize(public_path('images/uploads/syllabustask') . '/' . $filename) > 10000000) {
                unlink(public_path('images/uploads/syllabustask') . '/' . $filename);
                return false;
            }
        }
        if (!empty($syllabusTaskAudioData) && !empty($this->audio_data)) {
            $filename = 'audio_' . $this->id . '_' . time() . rand(1, 1000) . '.' . pathinfo($this->audio_data, PATHINFO_EXTENSION);
            $this->audio_data = $filename;
            file_put_contents(public_path('audio') . '/' . $filename, base64_decode($syllabusTaskAudioData));

            if (filesize(public_path('audio') . '/' . $filename) > 10000000) {
                unlink(public_path('audio') . '/' . $filename);
                return false;
            }
        }

        return true;
    }


    /**
     * get percent
     * @param $iValue
     * @return int
     */
    public function getPercent($iValue)
    {
        $iPercent = 0;
        if (!empty($iValue) && !empty($this->points)) {
            $iPercent = intval(($iValue * 100) / $this->points);
        }
        return $iPercent;
    }


    /**
     * @param $aAnswers
     * @return array
     */
    public static function testProcessSaveResultGetData($aAnswers)
    {
        $aAnswersData = [];
        $iCorrectPoints = 0;
        $iTotalAnswers = 0;

        if (!empty($aAnswers) && (count($aAnswers) > 0)) {
            foreach ($aAnswers as $answer) {
                if (empty($answer['answer'])) {
                    continue;
                }

                $aIds = [];
                if (is_array($answer['answer'])) {
                    $aIds = $answer['answer'];
                } else {
                    $aIds[] = $answer['answer'];
                }

                $oSyllabusTaskAnswer = SyllabusTaskAnswer::
                whereIn('id', $aIds)->
                get();
                if (!empty($oSyllabusTaskAnswer) && (count($oSyllabusTaskAnswer) > 0)) {
                    foreach ($oSyllabusTaskAnswer as $itemSTA) {
                        if (!empty($itemSTA->correct) && !empty($itemSTA->points)) {
                            $iCorrectPoints += $itemSTA->points;
                        }
                        $iTotalAnswers += 1;
                        $aAnswersData[] = $itemSTA;
                    }
                }
                unset($oSyllabusTaskAnswer);
            }
        }

        return [$aAnswersData, $iCorrectPoints, $iTotalAnswers];
    }


    /**
     * @param $iTaskId
     * @return int
     */
    public static function getTotalCorrectAnswersByTaskId($iTaskId)
    {
        $iResponse = 0;

        $oSyllabusTaskQuestions = SyllabusTaskQuestions::
        where('task_id', $iTaskId)->
        whereNull('deleted_at')->
        get();

        if (!empty($oSyllabusTaskQuestions) && (count($oSyllabusTaskQuestions) > 0)) {
            foreach ($oSyllabusTaskQuestions as $oItemQ) {
                $iCurCorrect = SyllabusTaskAnswer::
                where('question_id', $oItemQ->id)->
                where('correct', SyllabusTaskAnswer::STATUS_CORRECT)->
                whereNull('deleted_at')->
                count();

                $iResponse += $iCurCorrect;
            }
        }
        unset($oSyllabusTaskQuestions);

        return $iResponse;
    }


    /**
     * @param $iDisciplineId
     * @param $sLang
     * @return \Illuminate\Database\Eloquent\Collection|null|static[]
     */
    public static function getSyllabusTaskData($iDisciplineId, $sLang) : Collection
    {
        $oSyllabusTasksData     = null;
        $oSyllabusTasksDataTemp = null;

        $oSyllabusTasksDataKZ = self::
        with('taskResult')->
        with('pay')->
        with('payCount')->
        where('discipline_id', $iDisciplineId)->
        where('language', Profiles::EDUCATION_LANG_KZ)->
        whereNull('deleted_at')->
        get();

        if (!empty($oSyllabusTasksDataKZ) && (count($oSyllabusTasksDataKZ) > 0)) {
            foreach ($oSyllabusTasksDataKZ as $itemSTKZ) {
                // проверяем наличие результатов
                if (!empty($itemSTKZ->taskResult)) {
                    $oSyllabusTasksDataTemp   = $oSyllabusTasksDataKZ;
                    break;
                }
            }
        }

        $oSyllabusTasksDataRU = self::
        with('taskResult')->
        with('pay')->
        with('payCount')->
        where('discipline_id', $iDisciplineId)->
        where('language', Profiles::EDUCATION_LANG_RU)->
        whereNull('deleted_at')->
        get();

        if (!empty($oSyllabusTasksDataRU) && (count($oSyllabusTasksDataRU) > 0)) {
            foreach ($oSyllabusTasksDataRU as $itemSTRU) {
                // проверяем наличие результатов
                if (!empty($itemSTRU->taskResult) && empty($oSyllabusTasksData)) {
                    $oSyllabusTasksDataTemp   = $oSyllabusTasksDataRU;
                    break;
                }
            }
        }

        $oSyllabusTasksDataEN = self::
        with('taskResult')->
        with('pay')->
        with('payCount')->
        where('discipline_id', $iDisciplineId)->
        where('language', Profiles::EDUCATION_LANG_EN)->
        whereNull('deleted_at')->
        get();

        if (!empty($oSyllabusTasksDataEN) && (count($oSyllabusTasksDataEN) > 0)) {
            foreach ($oSyllabusTasksDataEN as $itemSTEN) {
                // проверяем наличие результатов
                if (!empty($itemSTEN->taskResult) && empty($oSyllabusTasksData)) {
                    $oSyllabusTasksDataTemp   = $oSyllabusTasksDataEN;
                    break;
                }
            }
        }

        switch ($sLang) {
            case Profiles::EDUCATION_LANG_KZ:
                $oSyllabusTasksData = $oSyllabusTasksDataKZ;
            break;
            case Profiles::EDUCATION_LANG_RU:
                $oSyllabusTasksData = $oSyllabusTasksDataRU;
            break;
            case Profiles::EDUCATION_LANG_EN:
                $oSyllabusTasksData = $oSyllabusTasksDataEN;
            break;
        }

        // если нет результатов
        if (empty($oSyllabusTasksData)) {
            $oSyllabusTasksData = $oSyllabusTasksDataTemp;
        }

        return $oSyllabusTasksData;
    }

    public function setProceedButtonShow(User $user) : void
    {
        $this->proceedButtonShow =
            SyllabusTaskUserPay::getCount($user->id, $this->id) < self::FREE_ATTEMPTS
            ||
            (
                SyllabusTaskUserPay::getCount($user->id, $this->id) >= self::FREE_ATTEMPTS &&
                $this->pay->active
            );
    }

    public function setRetakeButtonShow(User $user) : void
    {
        $this->retakeButtonShow =
            SyllabusTaskUserPay::getCount($user->id, $this->id) >= self::FREE_ATTEMPTS &&
            !$this->pay->active &&
            !$this->pay->payed;
    }

    public function setProceedButtonShowCorona(User $user) : void
    {
        $this->proceedButtonShow =
            SyllabusTaskUserPay::getCount($user->id, $this->id) < self::CORONA_FREE_ATTEMPTS ||
            (
                SyllabusTaskUserPay::getCount($user->id, $this->id) >= self::CORONA_FREE_ATTEMPTS &&
                $this->pay->active
            );
    }

    public function setRetakeButtonShowCorona(User $user) : void
    {
        $this->retakeButtonShow =
            SyllabusTaskUserPay::getCount($user->id, $this->id) >= self::CORONA_FREE_ATTEMPTS &&
            !empty($this->pay) &&
            !$this->pay->active &&
            !$this->pay->payed;
    }

    public function hasFreeAttempt(int $userId) : bool
    {
        return SyllabusTaskResult::attemptsCount($this->id, $userId) < self::FREE_ATTEMPTS;
    }

    public function hasFreeAttemptCorona(int $userId) : bool
    {
        return SyllabusTaskResult::attemptsCount($this->id, $userId) < self::CORONA_FREE_ATTEMPTS;
    }

    public function isTrial(int $userId) : bool
    {
        /** @var SyllabusTaskUserPay $lastAttempt */
        $lastAttempt = SyllabusTaskUserPay
            ::where('task_id', $this->id)
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->first();

        return !empty($lastAttempt) && $lastAttempt->payed && $lastAttempt->active;
    }

    public static function getRandomId()
    {
        $one = self
            ::select('id')
            ->inRandomOrder()
            ->first();

        return $one->id ?? null;
    }


    /**
     * @param $sLink
     * @return mixed|string
     */
    public static function getVideoLink( $sLink )
    {
        $sResponse = '';
        $sTemp = str_replace('watch?v=','embed/',$sLink);
        $aTemp = explode('&',$sTemp);
        if( !empty($aTemp) && (count($aTemp) > 0))
        {
            $sResponse = $aTemp[0];
        } else {
            $sResponse = $sTemp;
        }
        return $sResponse;
    }

}
