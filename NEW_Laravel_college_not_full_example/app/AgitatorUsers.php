<?php
/**
 * User: dadicc
 * Date: 12/8/19
 * Time: 9:03 PM
 */

namespace App;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};

class AgitatorUsers extends Model
{

    use SoftDeletes;

    const STATUS_PROCESS = "process";
    const STATUS_OK      = "ok";
    const STATUS_ERROR   = "error";
    const STATUS_PAYED   = "payed";

    protected $table = 'agitator_users';

    protected $fillable = [
        'user_id',
        'stud_id',
        'cost',
        'status'
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
    public function stud()
    {
        return $this->hasOne('App\User', 'id', 'stud_id')->withTrashed();
    }


    /**
     * @param $iUserId
     * @param $search
     * @param array $searchParams
     * @param int $start
     * @param int $length
     * @param int $orderColumn
     * @param string $orderDir
     * @return array
     */
    public static function getList(
        $iUserId,
        $search,
        $searchParams = [],
        $start=0,
        $length=10,
        $orderColumn = 0,
        $orderDir='desc'
    )
    {

        $query = self::
        with('stud')->
        with('stud.studentProfile')->
        where('user_id',$iUserId)->
        whereNull('deleted_at');

        $recordsTotal = $query->count();

        $columnList = [
            0 => 'cost',
            1 => 'status',
            2 => 'created_at'
        ];

        $orderColumnName = $columnList[$orderColumn] ?? 'id';
        $query->orderBy($orderColumnName, $orderDir);

        $recordsFiltered = $recordsTotal;

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
                0 => $itemAFO->stud->studentProfile->fio,
                1 => $itemAFO->cost,
                2 => __($itemAFO->status),
                3 => date('Y-m-d H:i',strtotime($itemAFO->created_at))
            ];

        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];

    }



}