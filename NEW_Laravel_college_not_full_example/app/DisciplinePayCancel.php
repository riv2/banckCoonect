<?php

namespace App;

use App\Services\Auth;
use App\Services\SearchCache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class DisciplinePayCancel
 * @package App
 *
 * @property int id
 * @property int discipline_id
 * @property int user_id
 * @property int admin_id
 * @property string status
 * @property int executed_1c
 * @property int executed_miras
 * @property string decline_reason
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class DisciplinePayCancel extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    const STATUS_NEW = 'new';
    const STATUS_APPROVE = 'approve';
    const STATUS_DECLINE = 'decline';
    const REDIS_TABLE = 'discipline_pay_cancel';
    const COLUMN_LIST = [
        0 => 'id',
        1 => 'user_fio',
        2 => 'discipline',
        3 => 'date',
        4 => 'status'
    ];

    const FIELD_LIST = [
        0 => 'id',
        1 => 'user_id',
        2 => 'discipline_id',
        3 => 'created_at',
        4 => 'status'
    ];

    const REDIS_STATUS_TRANSLATE = [
        'new' => 'новая',
        'approve' => 'подтверждено',
        'decline' => 'отклонено'
    ];

    protected $table = 'discipline_pay_cancel';

    public function studentProfile()
    {
        return $this->hasOne(Profiles::class, 'user_id', 'user_id');
    }

    public function discipline()
    {
        return $this->hasOne(Discipline::class, 'id', 'discipline_id');
    }

    static function getListForAdmin(
        $search,
        $searchParams = [],
        $start = 0,
        $length = 10,
        $orderColumn = 0,
        $orderDir = 'asc'
    )
    {
        $recordsTotal = DisciplinePayCancel
            ::select('id')
            ->where('executed_1c', false)
            ->where('executed_miras', false)
            ->count();

        $orderColumnName = self::FIELD_LIST[$orderColumn] ?? 'id';
        $query = self
            ::with('studentProfile')
            ->with('discipline')
            ->orderBy($orderColumnName, $orderDir);

        if (!empty($search)) {
            $searchIds = SearchCache::search(self::REDIS_TABLE, $search);
            $orderIds = $searchIds;
            $recordsFiltered = count($orderIds);
        } else {
            $orderIds = [];
            $recordsFiltered = $recordsTotal;
        }

        foreach ($searchParams as $fieldNum => $val) {
            $filteredIds = SearchCache::search(self::REDIS_TABLE, $val, self::COLUMN_LIST[$fieldNum]);
            $orderIds = array_intersect($orderIds, $filteredIds);
        }


        if($search || $searchParams)
        {
            $query->whereIn('id', $orderIds);
        }

        $orders = $query
            ->where('executed_1c', false)
            ->where('executed_miras', false)
            ->limit($length)
            ->offset($start)
            ->get();

        $data = [];
        foreach ($orders as $order) {

            $approveShow = true;
            $declineShow = true;
            if($order->status == self::STATUS_APPROVE) {
                $approveShow = false;
            }

            if($order->status == self::STATUS_DECLINE) {
                $declineShow = false;
            }

            $approveButton = '<a class="btn btn-success" style="display:' . ($approveShow ? 'block' : 'none') . '" id="btn-approve-'. $order->id .'" onclick="payCancelStatus(' . $order->id . ', \'approve\')">Подтвердить</a>';
            $declineButton = '<a class="btn btn-danger" style="display:' . ($declineShow ? 'block' : 'none') . '" id="btn-decline-'. $order->id .'" onclick="payCancelStatus(' . $order->id . ', \'decline\')">Отклонить</a>';

            $data[] = [
                $order->id,
                $order->studentProfile->fio ?? '',
                $order->discipline->name,
                date('d.m.Y H:s', strtotime($order->created_at)),
                '<span id="status-'. $order->id . '">' . self::REDIS_STATUS_TRANSLATE[$order->status] . '</span>',
                Auth::user()->hasRight('discipline_pay_cancel', 'edit') ? $approveButton . $declineButton : ''
            ];
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    /**
     * @return bool
     */
    public function redisCacheRefresh()
    {
        SearchCache::addOrUpdate(self::REDIS_TABLE, $this->id, [
            'id' => $this->id,
            'user_fio' => $this->studentProfile->fio,
            'discipline' => $this->discipline->name,
            'date'  => date('d.m.Y', strtotime($this->created_at)),
            'status' => self::REDIS_STATUS_TRANSLATE[$this->status]
        ]);

        return true;
    }

    public static function getDisciplineArray(int $userId) : array
    {
        return $disciplinePayCancelList = self
            ::select(['discipline_id'])
            ->where('user_id', $userId)
            ->whereIn('status', [self::STATUS_NEW, self::STATUS_APPROVE])
            ->where('executed_1c', false)
            ->where('executed_miras', false)
            ->pluck('discipline_id')
            ->toArray();
    }
}
