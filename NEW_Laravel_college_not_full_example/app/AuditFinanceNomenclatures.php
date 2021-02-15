<?php
/**
 * User: dadicc
 * Date: 29.08.19
 * Time: 14:55
 */

namespace App;

use App\Services\Auth;
use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use Illuminate\Support\Facades\{DB,Log};
use OwenIt\Auditing\Contracts\Auditable;

class AuditFinanceNomenclatures extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    const STATUS_PROCESS = 'process';
    const STATUS_FAIL    = 'fail';
    const STATUS_SUCCESS = 'success';

    protected $table = 'audit_finance_nomenclatures';

    protected $fillable = [
        'user_id',
        'user_name',
        'owner_id',
        'owner_name',
        'service_id',
        'service_name',
        'service_code',
        'cost',
        'count',
        'status',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function service()
    {
        return $this->hasOne('App\FinanceNomenclature', 'id', 'service_id');
    }

    /**
     * @param $search
     * @param array $searchParams
     * @param int $start
     * @param int $length
     * @param string $orderDir
     */
    static function getAuditList(
        $search,
        $searchParams = [],
        $start=0,
        $length=10,
        $orderColumn = 0,
        $orderDir='asc'
    )
    {

        $query = self::
        select([
            'user_id',
            'user_name',
            'owner_name',
            'service_id',
            'service_name',
            'service_code',
            'cost',
            'count',
            'status',
            'created_at',
        ])->
        with('user')->
        //with('owner')->
        with('service');

        $recordsTotal = $query->count();

        $columnList = [
            0 => 'user_id',
            1 => 'user_name',
            2 => 'owner_name',
            3 => 'service_name',
            4 => 'service_code',
            5 => 'cost',
            6 => 'status',
            7 => 'created_at'
        ];

        $orderColumnName = $columnList[$orderColumn] ?? 'user_id';
        $query->orderBy($orderColumnName, $orderDir);

        if( !empty($search) )
        {

            if( is_numeric($search) )
            {
                $query->where('user_id','=',$search);
            } else {
                $query->where('user_name','like','%' . $search . '%');
                $query->orWhere('owner_name','like','%' . $search . '%');
                $query->orWhere('service_name','like','%' . $search . '%');
            }

            $recordsFiltered = $query->count();

        } else {

            $recordsFiltered = $recordsTotal;
        }

        if( (count($searchParams) > 0) )
        {
            foreach ($searchParams as $field => $val)
            {
                if( is_numeric($val) )
                {
                    $query->where($field,'=',$val);
                } else {
                    $query->where($field,'ilike','%' . $val . '%');
                }
            }
        }

        $filterResult = $query->limit($length)->offset($start)->get();

        $data = [];
        foreach ($filterResult as $itemAFO)
        {

            $data[] = [
                0 => $itemAFO->user_id,
                1 => $itemAFO->user_name,
                2 => $itemAFO->owner_name,
                3 => $itemAFO->service_name,
                4 => $itemAFO->service_code,
                5 => $itemAFO->cost,
                6 => __($itemAFO->status),
                7 => date('d-m-Y H:i',strtotime($itemAFO->created_at))
            ];

        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];

    }


}