<?php

namespace App\Models;

use App\Models\Traits\HasPpId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Reestr
 *
 * @property int $reestr_id
 * @property int $pp_id
 * @property \Jenssegers\Date\Date|null $datetime
 * @property float|null $total
 * @property float|null $payed
 * @property int|null $status
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property \Jenssegers\Date\Date|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrdersProduct[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PartnerPayment[] $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Pp $pp
 * @method static \Illuminate\Database\Eloquent\Builder|Reestr newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reestr newQuery()
 * @method static \Illuminate\Database\Query\Builder|Reestr onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Reestr query()
 * @method static \Illuminate\Database\Query\Builder|Reestr withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Reestr withoutTrashed()
 * @mixin \Eloquent
 */
class Reestr extends Model
{
    use SoftDeletes;
    use HasPpId;
    protected $table = 'reestrs';
    protected $primaryKey = 'reestr_id';

    protected $casts = [
        'pp_id' => 'int',
        'total' => 'float',
        'payed' => 'float',
        'status' => 'int'
    ];

    protected $dates = [
        'datetime'
    ];

    protected $fillable = [
        'datetime',
        'pp_id',
        'total',
        'payed',
        'status'
    ];


    public function orders()
    {
        return $this->hasMany(OrdersProduct::class, "reestr_id", "reestr_id");
    }

    public function payments()
    {
        return $this->hasMany(PartnerPayment::class, "reestr_id", "reestr_id");
    }
}
