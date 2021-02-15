<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-09-05
 * Time: 11:11
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{

    const TYPE_PAYMENT_IN   = 'in';
    const TYPE_PAYMENT_OUT  = 'out';

    protected $table = 'transaction_history';

    protected $fillable = [
        'user_id',
        'iin',
        'type',
        'code',
        'name',
        'cost',
        'date'
    ];

    /**
     * @param $value
     */
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = date('Y-m-d H:i:s',strtotime($value));
    }

    /**
     * @param $value
     */
    public function getDateAttribute($value)
    {
        return date('d.m.Y H:i',strtotime($value));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @param $search
     * @param array $searchParams
     * @param int $start
     * @param int $length
     * @param int $orderColumn
     * @param string $orderDir
     */
    public static function getTransactionList(
        $iin,
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
            'type',
            'code',
            'name',
            'cost',
            'date'
        ])->
        where('iin',$iin);

        $recordsTotal = $query->count();

        $columnList = [
            0 => 'type',
            1 => 'code',
            2 => 'name',
            3 => 'cost',
            4 => 'date'
        ];

        $orderColumnName = $columnList[$orderColumn] ?? 'user_id';
        $query->orderBy($orderColumnName, $orderDir);

        if( !empty($search) )
        {
            $query->where('name','like', $search . '%');
            $query->orWhere('code','like', $search . '%');

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
                0 => __($itemAFO->type),
                1 => $itemAFO->code,
                2 => $itemAFO->name,
                3 => $itemAFO->cost,
                4 => $itemAFO->date
            ];

        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];


    }

}