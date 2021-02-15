<?php

namespace App;

use App\Services\SearchCache;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

/**
 * Class Appeal
 * @package App
 * @property int id
 * @property int user_id
 * @property int student_discipline_id
 * @property int quiz_result_id
 * @property string type
 * @property string status
 * @property int resolution_user_id
 * @property string resolution_action
 * @property int added_value
 * @property int control_result
 * @property int control_result_points
 * @property string control_result_letter
 * @property Carbon control_date
 * @property string reason
 * @property string file
 * @property Carbon created_at
 * @property User user
 * @property User resolutionUser
 * @property User expert1
 * @property User expert2
 * @property User expert3
 * @property StudentDiscipline studentDiscipline
 * @property int expert1_id
 * @property string expert1_resolution
 * @property string expert1_resolution_text
 * @property Carbon expert1_resolution_date
 * @property int expert2_id
 * @property string expert2_resolution
 * @property string expert2_resolution_text
 * @property Carbon expert2_resolution_date
 * @property int expert3_id
 * @property string expert3_resolution
 * @property string expert3_resolution_text
 * @property Carbon expert3_resolution_date
 *
 * @property QuizResult quizResult
 * @property AppealQuizAnswer answers
 *
 * @property-read string type_name
 */
class Appeal extends Model
{
    protected $table = 'appeals';

    protected $dates = ['created_at', 'updated_at', 'control_date', 'expert1_resolution_date', 'expert2_resolution_date', 'expert3_resolution_date'];

    const STATUS_REVIEW = 'review';
    const STATUS_APPROVED = 'approved';
    const STATUS_DECLINED = 'declined';

    const RESOLUTION_APPROVED = 'approved';
    const RESOLUTION_DECLINED = 'declined';

    const RESOLUTION_ACTION_NEW_TRY = 'new_try';
    const RESOLUTION_ACTION_ADD_VALUE = 'add_value';

    private static $adminAjaxColumnList = [
//        0 => 'id',
//        'full_code',
//        'name',
//        'year'
    ];

    public static $adminRedisTable = 'admin_appeals';

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function resolutionUser()
    {
        return $this->hasOne(User::class, 'id', 'resolution_user_id');
    }

    public function studentDiscipline()
    {
        return $this->hasOne(StudentDiscipline::class, 'id', 'student_discipline_id');
    }

    public function quizResult()
    {
        return $this->hasOne(QuizResult::class, 'id', 'quiz_result_id');
    }

    public function expert1()
    {
        return $this->hasOne(User::class, 'id', 'expert1_id');
    }

    public function expert2()
    {
        return $this->hasOne(User::class, 'id', 'expert2_id');
    }

    public function expert3()
    {
        return $this->hasOne(User::class, 'id', 'expert3_id');
    }

    public function answers()
    {
        return $this->hasOne(AppealQuizAnswer::class, 'appeal_id', 'id');
    }

    public function getTypeNameAttribute()
    {
        return __('appeal_type_'. $this->type);
    }

    /**
     * @param string|null $search
     * @param int $start
     * @param int $length
     * @param int $orderColumn
     * @param string $orderDirection
     * @return array
     */
    static function getListForAdmin(?string $search = '', int $start = 0, int $length = 10, int $orderColumn = 0, string $orderDirection = 'asc')
    {
        $recordsTotal = SearchCache::totalCount(self::$adminRedisTable);

        $query = self::orderBy(self::$adminAjaxColumnList[$orderColumn] ?? 'id', $orderDirection);

        // Without filters
        if (empty($search)) {
            $recordsFiltered = $recordsTotal;
        } else {
            // Search string $search
//            if (!empty($search)) {
//                // Get ids
//                $idList = SearchCache::searchFull(self::$adminRedisTable, $search, 'name');
//                $query->whereIn('id', $idList);
//
//                if (is_numeric($search)) {
//                    $query->orWhere('id', (int)$search);
//                }
//            }
//
//            $recordsFiltered = $query->count();
        }

        // Get result
        $appeals = $query->offset($start)->take($length)->get();

        $data = [];
        foreach ($appeals as $appeal) {
            /** @var self $appeal */

            if ($appeal->status == self::STATUS_REVIEW) {
                $status = '<span class="label label-warning">'. __('appeal_status_'. $appeal->status) .'</span>';
            } elseif ($appeal->status == self::STATUS_APPROVED) {
                $status = '<span class="label label-success">'. __('appeal_status_'. $appeal->status) .'</span>';
            } elseif ($appeal->status == self::STATUS_DECLINED) {
                $status = '<span class="label label-danger">'. __('appeal_status_'. $appeal->status) .'</span>';
            } else {
                $status = __('appeal_status_'. $appeal->status);
            }

            if ($appeal->status == self::STATUS_APPROVED) {
                if ($appeal->resolution_action == self::RESOLUTION_ACTION_NEW_TRY) {
                    $action = '<div>пересдача</div>';
                } elseif ($appeal->resolution_action == self::RESOLUTION_ACTION_ADD_VALUE) {
                    $action = "<div>добавлено $appeal->added_value</div>";
                } else {
                    $action = '<div><span class="label label-info">требует решения</span></div>';
                }
            } else {
                $action = '';
            }


            $data[] = [
                $appeal->created_at->format('d.m.Y H:i'),
                '<a href="'. route('adminAppealReview', ['id' => $appeal->id]) .'" title="Рассмотрение апелляции">' . $appeal->user->studentProfile->fio . '</a>',
                $appeal->user->studentProfile->speciality->name . ' (' . $appeal->user->admission_year . ')',
                __($appeal->user->base_education),
                __($appeal->user->studentProfile->education_study_form),
                $appeal->studentDiscipline->discipline->name,
                __('appeal_type_'. $appeal->type),
                $appeal->control_date->format('d.m.Y H:i'),
                "{$appeal->control_result}%, {$appeal->control_result_points} ({$appeal->control_result_letter})",
                $status . $action
            ];
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    public function setExpert1Resolution(int $userId, string $resolution, ?string $text) : bool
    {
        $this->expert1_id = $userId;
        $this->expert1_resolution = $resolution;
        $this->expert1_resolution_text = $text;
        $this->expert1_resolution_date = Carbon::now();

        return $this->save();
    }

    public function setExpert2Resolution(int $userId, string $resolution, ?string $text) : bool
    {
        $this->expert2_id = $userId;
        $this->expert2_resolution = $resolution;
        $this->expert2_resolution_text = $text;
        $this->expert2_resolution_date = Carbon::now();

        return $this->save();
    }

    public function checkStatus() : void
    {
        // Without expert 3
        if (empty($this->expert3_id)) {
            // Have 2 resolutions
            if (!empty($this->expert1_id) && !empty($this->expert2_id)) {
                // Both approved
                if (
                    $this->expert1_resolution == self::RESOLUTION_APPROVED &&
                    $this->expert2_resolution == self::RESOLUTION_APPROVED
                ) {
                    $this->setStatus(self::STATUS_APPROVED);
                }
            }
        }
        // With expert 3
        else {
            // Have 3 resolutions
            if (!empty($this->expert1_id) && !empty($this->expert2_id) && !empty($this->expert3_resolution)) {
                // All approved
                if (
                    $this->expert1_resolution == self::RESOLUTION_APPROVED &&
                    $this->expert2_resolution == self::RESOLUTION_APPROVED &&
                    $this->expert3_resolution == self::RESOLUTION_APPROVED
                ) {
                    $this->setStatus(self::STATUS_APPROVED);
                }
            }
        }
    }

    public function setStatus(string $status, $resolutionText = '')
    {
        $this->status = $status;
        $this->save();

        if ($status == self::STATUS_DECLINED) {
            $text = 'В аппеляции от '. $this->created_at .' по "'. $this->studentDiscipline->discipline->name .'" форма контроля "'. $this->type_name .'" отказано. Причина - '. $resolutionText;

            Notification::add($this->user_id, $text);
        }
    }

    public function setExpert3(int $id)
    {
        $this->expert3_id = $id;
        $this->save();

        $mailSubject = 'Вас пригласили в качестве эксперта для рассмотрения аппеляции №' . $this->id;

        Mail::send('emails.appeal_call_expert3', [
            'appeal' => $this,
        ], function ($message) use ($mailSubject) {
            $message->from(getcong('site_email'), getcong('site_name'));
            $message->to($this->expert3->email)->subject($mailSubject);
        });
    }

    public function setExpert3Resolution(string $resolution, ?string $text) : bool
    {
        $this->expert3_resolution = $resolution;
        $this->expert3_resolution_text = $text;
        $this->expert3_resolution_date = Carbon::now();

        return $this->save();
    }

    public function addNewTry(int $userId)
    {
        // Set resolution
        $this->resolution_user_id = $userId;
        $this->resolution_action = self::RESOLUTION_ACTION_NEW_TRY;
        $this->save();

        // Test 1
        if ($this->type == StudentDiscipline::CONTROL_TYPE_TEST1) {
            $this->studentDiscipline->test1_result_trial = true;
        }
        // SRO
        elseif ($this->type == StudentDiscipline::CONTROL_TYPE_SRO) {
//            $this->studentDiscipline->task_result_trial = true;
        }
        // Exam
        elseif ($this->type == StudentDiscipline::CONTROL_TYPE_EXAM) {
            $this->studentDiscipline->test_result_trial = true;
        }

        $this->studentDiscipline->save();

        $sms = 'Аппеляция от '. $this->created_at .' по "'. $this->studentDiscipline->discipline->name .'" форма контроля "'. $this->type .'" принята, пройдите тест заново';
        SmsService::send($this->user->studentProfile->mobile, $sms);
    }

    public function addValue(int $userId, int $value)
    {
        // Set resolution
        $this->resolution_user_id = $userId;
        $this->resolution_action = self::RESOLUTION_ACTION_ADD_VALUE;
        $this->added_value = $value;
        $this->save();

        $this->quizResult->addValue($value);

        // Test 1
        if ($this->type == StudentDiscipline::CONTROL_TYPE_TEST1) {
            $this->studentDiscipline->setTest1Result();
        }
        // SRO
        elseif ($this->type == StudentDiscipline::CONTROL_TYPE_SRO) {
//            $this->studentDiscipline->task_result_trial = true;
        }
        // Exam
        elseif ($this->type == StudentDiscipline::CONTROL_TYPE_EXAM) {
            $this->studentDiscipline->setExamResult();
        }

        $sms = 'Аппеляция от '. $this->created_at .' по "'. $this->studentDiscipline->discipline->name .'" форма контроля "'. $this->type .'" принята. Результат повышен на '. $value .'%';
        SmsService::send($this->user->studentProfile->mobile, $sms);
    }

    public static function add(string $type, QuizResult $result, string $reason, ?string $filePath) : ?int
    {
        $appeal = new self;
        $appeal->user_id = $result->user_id;
        $appeal->student_discipline_id = $result->student_discipline_id;
        $appeal->quiz_result_id = $result->id;
        $appeal->type = $type;
        $appeal->status = self::STATUS_REVIEW;
        $appeal->control_result = $result->value;
        $appeal->control_result_points = $result->points;
        $appeal->control_result_letter = $result->letter;
        $appeal->control_date = $result->created_at;
        $appeal->reason = $reason;
        $appeal->file = $filePath;

        $appeal->save();

        return $appeal->id ?? null;
    }

    public static function getSDIDsTest1(int $userId) : array
    {
        return self::getForStudyPage($userId, StudentDiscipline::CONTROL_TYPE_TEST1);
    }

    public static function getSDIDsExam(int $userId) : array
    {
        return self::getForStudyPage($userId, StudentDiscipline::CONTROL_TYPE_EXAM);
    }

    public static function getForStudyPage(int $userId, string $type) : array
    {
        return self
            ::select(['student_discipline_id'])
            ->where('user_id', $userId)
            ->where('type', $type)
            ->pluck('student_discipline_id')
            ->toArray();
    }

    public static function getExistsId(int $studentDisciplineId, string $type) : ?int
    {
        $appeal = self::select('id')
            ->where('student_discipline_id', $studentDisciplineId)
            ->where('type', $type)
            ->first();

        return $appeal->id ?? null;
    }
}
