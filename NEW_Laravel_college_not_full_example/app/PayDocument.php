<?php

namespace App;

use App\Services\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class PayDocument
 * @package App
 * @property int id
 * @property int order_id
 * @property int user_id
 * @property int student_discipline_id
 * @property float amount
 * @property float balance_before
 * @property int credits
 * @property string status
 * @property string type
 * @property bool complete_pay
 * @property string hash
 * @property Carbon created_at
 */
class PayDocument extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use SoftDeletes;

    const STATUS_PROCESS = 'process';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';
    const STATUS_CANCEL = 'cancel';

    const TYPE_DISCIPLINE = 'discipline';
    const TYPE_LECTURE = 'lecture';
    const TYPE_LECTURE_ROOM = 'lecture_room';
    const TYPE_TEST = 'test';
    const TYPE_RETAKE_TEST = 'retake_test';
    const TYPE_RETAKE_EXAM = 'retake_exam';
    const TYPE_RETAKE_KGE = 'retake_kge';
    const TYPE_REGISTRATION_FEE = 'registration_fee';
    const TYPE_TO_BALANCE = 'to_balance';
    const TYPE_WIFI = 'wifi';
    const TYPE_REGISTRATION = 'registration';

    protected $table = 'pay_documents';

    protected $fillable = [
        'order_id',
        'user_id',
        'student_discipline_id',
        'amount',
        'balance_before',
        'status',
        'hash',
        'type',
        'credits'
    ];

    /**
     * @return mixed
     */
    public function studentDiscipline()
    {
        return $this->belongsToMany('App\StudentDiscipline', 'pay_documents_student_disciplines', 'pay_document_id', 'student_discipline_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function lectures()
    {
        return $this->belongsToMany(
            Lecture::class, 'pay_documents_lectures', 'pay_document_id', 'lecture_id')->withPivot('type');
    }

    public function lectureRooms()
    {
        return $this->belongsToMany(
            Lecture::class, 'pay_documents_lecture_room', 'pay_document_id', 'lecture_id');
    }

    /**
     * @return mixed
     */
    public function lecture()
    {
        return $this->lectures->first();
    }

    public function wifiTarifs()
    {
        return $this->belongsToMany(WifiTariff::class, 'pay_documents_wifi_tariff', 'pay_document_id', 'wifi_tariff_id');
    }

    /**
     * @param int $userId
     * @param string $orderId
     * @param $amount
     * @param int $credits
     * @param int $studentDisciplineId
     * @param float $balanceBefore
     * @return bool|self
     */
    static function createForStudentDiscipline(int $userId, string $orderId, $amount, int $credits, int $studentDisciplineId, float $balanceBefore) : ?self
    {
        $payDocument = self::create([
            'order_id' => $orderId,
            'user_id' => $userId,
            'student_discipline_id' => $studentDisciplineId,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'credits' => $credits,
            'status' => self::STATUS_PROCESS,
            'type' => self::TYPE_DISCIPLINE
        ]);

        if (empty($payDocument)) {
            return null;
        }

        PayDocumentStudentDiscipline::add($payDocument->id, $studentDisciplineId);

        return $payDocument;
    }

    /**
     * @param int $userId
     * @param string $orderId
     * @param $amount
     * @param int $studentDisciplineId
     * @return bool
     */
    static function createForStudentRetakeTest(int $userId, string $orderId, $amount, int $studentDisciplineId)
    {
        $payDocument = self::create([
            'order_id' => $orderId,
            'user_id' => $userId,
            'amount' => $amount,
            'status' => self::STATUS_PROCESS,
            'type' => self::TYPE_RETAKE_TEST
        ]);

        if (empty($payDocument)) {
            return false;
        }

        return PayDocumentStudentDiscipline::insert([
            'pay_document_id' => $payDocument->id,
            'student_discipline_id' => $studentDisciplineId,
            'created_at' => DB::raw('NOW()'),
            'updated_at' => DB::raw('NOW()')
        ]);
    }

    static function createForStudentRetakeKge(int $userId, string $orderId, $amount) : ?self
    {
        return self::create([
            'order_id' => $orderId,
            'user_id' => $userId,
            'amount' => $amount,
            'status' => self::STATUS_PROCESS,
            'type' => self::TYPE_RETAKE_KGE
        ]);
    }

    public static function createForLecture(int $userId, string $orderId, $amount, string $lectureType, int $lectureId) : bool
    {
        $payDocument = self::create([
            'order_id' => $orderId,
            'user_id' => $userId,
            'amount' => $amount,
            'status' => self::STATUS_PROCESS,
            'type' => self::TYPE_LECTURE
        ]);

        if (empty($payDocument)) {
            return false;
        }

        return PayDocumentLecture::insert([
            'pay_document_id' => $payDocument->id,
            'lecture_id' => $lectureId,
            'type' => $lectureType,
            'created_at' => DB::raw('NOW()'),
            'updated_at' => DB::raw('NOW()')
        ]);
    }

    /**
     * @param int $userId
     * @param string $orderId
     * @param $amount
     * @param int $lectureId
     * @return bool
     */
    static function createForLectureRoom(int $userId, string $orderId, $amount, int $lectureId) : bool
    {
        $payDocument = self::create([
            'order_id' => $orderId,
            'user_id' => $userId,
            'amount' => $amount,
            'status' => PayDocument::STATUS_PROCESS,
            'type' => PayDocument::TYPE_LECTURE_ROOM
        ]);

        if (empty($payDocument)) {
            return false;
        }

        return PayDocumentsLectureRoom::insert([
            'pay_document_id' => $payDocument->id,
            'lecture_id' => $lectureId,
            'created_at' => DB::raw('NOW()'),
            'updated_at' => DB::raw('NOW()')
        ]);
    }

    /**
     * @param int $userId
     * @param string $orderId
     * @param $amount
     * @return bool
     */
    static function createForTest(int $userId, string $orderId, $amount) : bool
    {
        $payDocument = self::create([
            'order_id' => $orderId,
            'user_id' => $userId,
            'amount' => $amount,
            'status' => PayDocument::STATUS_PROCESS,
            'type' => PayDocument::TYPE_TEST
        ]);

        return !empty($payDocument);
    }

    /**
     * @param $parameters
     * @return bool
     */
    static function createForBalance($parameters)
    {
        $payDocument = self::create($parameters);
        if($payDocument)
        {
            return true;
        }

        return false;
    }

    /**
     * @param $parameters
     * @return bool
     */
    static function createForWifi($parameters)
    {
        $payDocument = self::create($parameters);
        if($payDocument)
        {
            PayDocumentsWifiTariff::insert([
                'pay_document_id' => $payDocument->id,
                'wifi_tariff_id' => $parameters['tariff_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return true;
        }

        return false;
    }

    static function createForProfile(int $userId, string $orderId, $amount) : bool
    {
        $payDocument = self::create([
            'order_id' => $orderId,
            'user_id' => $userId,
            'amount' => $amount,
            'status' => PayDocument::STATUS_PROCESS,
            'type' => PayDocument::TYPE_REGISTRATION_FEE
        ]);

        return !empty($payDocument);
    }


    /**
     * @param bool $payStatus
     * @param $payResponse
     * @return string
     */
    function changePayStatus(bool $payStatus, $payResponse)
    {
        $oldStatus = $this->status;

        $this->status = $payStatus ? self::STATUS_SUCCESS : self::STATUS_FAIL;
        $this->hash = base64_encode(json_encode($payResponse));
        $this->save();

        if ($payStatus) {
            $this->payDelegate($payResponse, $oldStatus);
            return self::STATUS_SUCCESS;
        }

        return self::STATUS_FAIL;
    }

    /**
     * Method for action after pay
     * @param null $payResponse
     * @param string $oldStatus
     * @return bool
     */
    protected function payDelegate($payResponse = null, $oldStatus = self::STATUS_PROCESS)
    {
        $result = false;

        // Discipline
        if ($this->type == self::TYPE_DISCIPLINE) {
            /** @var StudentDiscipline $studentDiscipline */
            $studentDiscipline = $this->studentDiscipline()->first();

            if (!$studentDiscipline) {
                return false;
            }

            $result = $studentDiscipline->setPayed($this->credits);
        }
        elseif ($this->type == self::TYPE_LECTURE) {
            $lecture = $this->lectures->first();

            if (!$lecture) {
                return false;
            }

            $studentLecture = new StudentLecture();
            $studentLecture->user_id = $this->user_id;
            $studentLecture->lecture_id = $lecture->id;
            $studentLecture->type = $lecture->pivot->type;
            $result = $studentLecture->save();
        }
//        elseif ($this->type == self::TYPE_RETAKE_TEST) {
//            $studentDiscipline = $this->studentDiscipline()->first();
//
//            $quizeResult = QuizResult
//                ::where('user_id', $this->user_id)
//                ->where('discipline_id', $studentDiscipline->discipline_id)
//                ->orderBy('id', 'desc')
//                ->first();
//
//            if ($quizeResult) {
//                $quizeResult->payed = true;
//                $quizeResult->save();
//            }
//        }
        elseif ($this->type == self::TYPE_RETAKE_KGE) {

            $quizeResultKge = QuizeResultKge
                ::where('user_id', $this->user_id)
                ->orderBy('id', 'desc')
                ->first();

            if ($quizeResultKge) {
                $quizeResultKge->payed = true;
                $quizeResultKge->save();
            }
        }
        elseif ($this->type == self::TYPE_LECTURE_ROOM) {
            $lecture = $this->lectureRooms->first();

            if (!$lecture) {
                return false;
            }

            $lecture->room_payed = true;
            $result = $lecture->save();
        }
        elseif ($this->type == self::TYPE_TEST) {
            Log::info('Test pay', ['Pay response' => (array)$payResponse]);
        }
        elseif ($this->type == self::TYPE_TO_BALANCE) {
            Log::info('To balance pay', ['Pay response' => (array)$payResponse]);
            $result = true;
        }
        elseif ($this->type == self::TYPE_WIFI) {
            Log::info('Wifi pay', ['Pay response' => (array)$payResponse]);

            if($oldStatus == self::STATUS_PROCESS)
            {
                $tariff = $this->wifiTarifs()->first();
                $wifi = new Wifi();
                $wifi->user_id = Auth::user()->id;
                $wifi->code = str_random(10);
                $wifi->value = $tariff->value;
                $wifi->status = Wifi::STATUS_ACTIVE;
                $wifi->save();
            }

            $result = true;
        }

        return $result;
    }

    public static function createTest1Trial(int $userId, int $amount, int $studentDisciplineId, float $balanceBefore) : bool
    {
        $payDocument = new self();
        $payDocument->order_id = rand(10000, 10000000);
        $payDocument->user_id = $userId;
        $payDocument->student_discipline_id = $studentDisciplineId;
        $payDocument->amount = $amount;
        $payDocument->balance_before = $balanceBefore;
        $payDocument->status = self::STATUS_SUCCESS;
        $payDocument->type = self::TYPE_RETAKE_TEST;
        $payDocument->complete_pay = true;

        if (!$payDocument->save()) {
            return false;
        }

        return PayDocumentStudentDiscipline::add($payDocument->id, $studentDisciplineId);
    }

    public static function createExamTrial(int $userId, int $amount, int $studentDisciplineId, float $balanceBefore) : bool
    {
        $payDocument = new self();
        $payDocument->order_id = rand(10000, 10000000);
        $payDocument->user_id = $userId;
        $payDocument->student_discipline_id = $studentDisciplineId;
        $payDocument->amount = $amount;
        $payDocument->balance_before = $balanceBefore;
        $payDocument->status = self::STATUS_SUCCESS;
        $payDocument->type = self::TYPE_RETAKE_EXAM;
        $payDocument->complete_pay = true;

        if (!$payDocument->save()) {
            return false;
        }

        return PayDocumentStudentDiscipline::add($payDocument->id, $studentDisciplineId);
    }
}
