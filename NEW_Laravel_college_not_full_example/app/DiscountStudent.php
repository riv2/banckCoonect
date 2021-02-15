<?php

namespace App;

use App\Services\SearchCache;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class DiscountStudent
 * @package App
 *
 * @property int id
 * @property int type_id
 * @property int user_id
 * @property string status
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property string comment
 * @property Carbon date_approve
 * @property int moderator_id
 *
 * @property DiscountSemester[] semesters
 * @property DiscountTypeList type
 */
class DiscountStudent extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'discount_student';

    const STATUS_NEW = 'new';
    const STATUS_APPROVED = 'approved';
    const STATUS_DENIED = 'denied';
    const STATUS_CANCELED = 'canceled';
    const STATUS_FINISH = 'finish';

    protected $dates = ['created_at', 'updated_at', 'date_approve'];

    private static $adminAjaxColumnList = [
        'discount_student.id',
        'profiles.fio',
        'discount_type_list.name_ru',
        'discount_student.status',
        'date_approve'
    ];

    public static $adminRedisTable = 'admin_discount_student';

    public static $adminRedisCustomTable = 'admin_discount_student_custom';

    public function semesters()
    {
        return $this->hasMany(DiscountSemester::class);
    }

    public function type()
    {
        return $this->hasOne(DiscountTypeList::class, 'id', 'type_id');
    }

    public static function getListForAdminByCategory(
        int $categoryId,
        ?string $search = '',
        int $start = 0,
        int $length = 10,
        int $orderColumn = 0,
        string $orderDirection = 'asc'
    ) {
        $redisTable = self::$adminRedisTable . $categoryId;

        $orderColumnName = self::$adminAjaxColumnList[$orderColumn] ?? 'discount_student.user_id';

        $recordsTotal = SearchCache::totalCount($redisTable);

        $query = self::select(
            [
                'discount_student.id',
                'profiles.fio',
                'discount_type_list.name_ru AS name',
                'discount_student.status',
                'profiles.user_id',
                DB::raw('DATE_FORMAT(discount_student.created_at, "%d-%m-%Y") as created_at'),
                'category_id',
                'discount_type_list.id as type_id'
            ]
        )
            ->leftJoin('discount_type_list', 'discount_type_list.id', '=', 'discount_student.type_id')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'discount_student.user_id')
            ->where('category_id', $categoryId)
            ->orderBy($orderColumnName, $orderDirection);

        // Search string $search
        if (!empty($search)) {
            // Get ids
            $idList = SearchCache::searchFull($redisTable, $search);

            $query->whereIn('discount_student.id', $idList);

            if (is_numeric($search)) {
                $query->orWhere('discount_student.id', (int)$search);
            }

            $recordsFiltered = count($idList);
        } else {
            $recordsFiltered = $recordsTotal;
        }

        // Get result
        $filterResult = $query
            ->take($length)
            ->offset($start)
            ->get();

        $data = [];
        foreach ($filterResult as $item) {
            if ($item->status == self::STATUS_APPROVED) {
                $status = '<span class="label label-success">' . __($item->status) . '</span>';
            } elseif ($item->status == self::STATUS_DENIED) {
                $status = '<span class="label label-danger">' . __($item->status) . '</span>';
            } elseif ($item->status == self::STATUS_CANCELED) {
                $status = '<span class="label label-default">' . __($item->status) . '</span>';
            } else {
                $status = __($item->status);
            }

            $data[] = [
                $item->id,
                $item->fio,
                $item->name,
                $status,
                $item->attributes['created_at'],
                '',
                $item->attributes['user_id']
            ];
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    /**
     * @param string $search
     * @param int $start
     * @param int $length
     * @param int $orderColumn
     * @param string $orderDirection
     * @return array
     */
    static function getCustomListForAdmin(?string $search = '', int $start = 0, int $length = 10, int $orderColumn = 0, string $orderDirection = 'asc')
    {
        $orderColumnName = self::$adminAjaxColumnList[$orderColumn] ?? 'id';

        $recordsTotal = SearchCache::totalCount(self::$adminRedisCustomTable);

        $query = self::select(
            [
                'discount_student.id',
                'profiles.fio',
                'profiles.user_id',
                'discount_type_list.name_ru AS name',
                'discount_student.status',
                DB::raw('DATE_FORMAT(discount_student.created_at, "%d-%m-%Y") as created_at'),
                'category_id',
                'discount_type_list.id as type_id'
            ]
        )
            ->leftJoin('discount_type_list', 'discount_type_list.id', '=', 'discount_student.type_id')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'discount_student.user_id')
            ->orderBy($orderColumnName, $orderDirection);

        // Search string $search
        if (!empty($search)) {
            // Get ids
            $idList = SearchCache::searchFull(self::$adminRedisCustomTable, $search);
            $query->whereIn('discount_student.id', $idList);

            if (is_numeric($search)) {
                $query->orWhere('discount_student.id', (int)$search);
            }

            $recordsFiltered = count($idList);
        } else {
            $recordsFiltered = $recordsTotal;
        }

        // Get result
        $filterResult = $query
            ->offset($start)
            ->take($length)
            ->get();

        $data = [];
        foreach ($filterResult as $item) {
            $data[] = [
                $item->id,
                $item->fio,
                $item->name,
                $item->status,
                $item->attributes['created_at'],
                '',
                $item->attributes['user_id']

            ];
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    public static function addToAdminSearchCache(self $discount, string $fio = '')
    {
        if (empty($fio)) {
            $fio = Profiles::getFioByUserId($discount->user_id);
        }

        $type = DiscountTypeList::getById($discount->type_id);

        SearchCache::addOrUpdate(
            self::$adminRedisTable . $type->category_id,
            $discount->id,
            [
                'id' => $discount->id,
                'fio' => $fio,
                'name' => $type->name_ru,
                'status' => __($discount->status) ?? '',
            ]
        );
    }

    public static function getCreditPriceDiscount(int $userId, string $semester): int
    {
        $discount = self::where('user_id', $userId)
            ->where('status', self::STATUS_APPROVED)
            ->whereHas(
                'semesters',
                function ($query) use ($semester) {
                    $query->where('semester', $semester);
                }
            )
            ->first();

        if (empty($discount) || empty($discount->type->discount)) {
            return 0;
        }

        return $discount->type->discount;
    }

}
