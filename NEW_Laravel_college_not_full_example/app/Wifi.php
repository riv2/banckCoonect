<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-09-23
 * Time: 12:13
 */

namespace App;

use Illuminate\Database\Eloquent\{Log,Model,SoftDeletes};

class Wifi extends Model
{

    const STATUS_NEW        = 'new';
    const STATUS_ACTIVE     = 'active';
    const STATUS_INACTIVE   = 'inactive';

    use SoftDeletes;
    protected $table = 'wifi';
    protected $fillable = [
        'user_id',
        'code',
        'value',
        'status',
        'expire',

    ];


    /**
     * @param $value
     * @return false|string
     */
    public function getExpireAttribute($value)
    {

        return date('d.m.Y H:i',strtotime($value));
    }


    /**
     * @param $value
     */
    public function setExpireAttribute($value)
    {

        $this->attributes['expire'] = date('Y-m-d H:i:s',strtotime($value));
    }


    /**
     * @param $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {

        return $value;
    }

    /**
     * @param $value
     */
    public function setValueAttribute($value)
    {

        $this->attributes['value'] = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }


}