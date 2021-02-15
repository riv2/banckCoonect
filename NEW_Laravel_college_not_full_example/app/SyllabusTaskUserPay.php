<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-11-23
 * Time: 13:25
 */

namespace App;

use Auth;
use Carbon\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use App\{SyllabusTask,User};
use Illuminate\Database\Eloquent\{Model};
use Illuminate\Support\Facades\{Log};

/**
 * Class SyllabusTaskUserPay
 * @package App
 *
 * @property int id
 * @property int task_id
 * @property int user_id
 * @property bool active
 * @property bool payed
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class SyllabusTaskUserPay extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    const STATUS_ACTIVE         = 1;
    const STATUS_INACTIVE       = 0;
    const STATUS_PAYED_ACTIVE   = 1;
    const STATUS_PAYED_INACTIVE = 0;

    protected $table = 'syllabus_task_user_pay';

    protected $fillable = [
        'task_id',
        'user_id',
        'active',
        'payed'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function syllabusTask()
    {
        return $this->hasOne(SyllabusTask::class, 'id', 'task_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @param int $userId
     * @param int $taskId
     * @return int
     * @codeCoverageIgnore
     */
    public static function getCount(int $userId, int $taskId) : int
    {
        return self
            ::where('user_id', $userId)
            ->where('task_id', $taskId)
            ->count();
    }
}
