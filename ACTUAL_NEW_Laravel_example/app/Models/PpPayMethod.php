<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PpPayMethod
 *
 * @property int $pp_id
 * @property int $pay_method_id
 * @property-read \App\Models\PayMethod $pay_method
 * @property-read \App\Models\Pp $pp
 * @method static \Illuminate\Database\Eloquent\Builder|PpPayMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PpPayMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PpPayMethod query()
 * @mixin \Eloquent
 */
class PpPayMethod extends Model
{
    protected $table = 'pp_pay_methods';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'pp_id' => 'int',
        'pay_method_id' => 'int'
    ];

    protected $fillable = [
        'pp_id',
        'pay_method_id'
    ];

    public function pay_method()
    {
        return $this->belongsTo(PayMethod::class);
    }

    public function pp()
    {
        return $this->belongsTo(Pp::class);
    }
}
