<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PartnerPayment
 *
 * @property int $payment_id
 * @property int|null $reestr_id
 * @property int|null $partner_id
 * @property int $pp_id
 * @property string|null $partner
 * @property string|null $pay_method
 * @property string|null $pay_account
 * @property float $revenue
 * @property int|null $status
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property \Jenssegers\Date\Date|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PartnerPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PartnerPayment newQuery()
 * @method static \Illuminate\Database\Query\Builder|PartnerPayment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PartnerPayment query()
 * @method static \Illuminate\Database\Query\Builder|PartnerPayment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PartnerPayment withoutTrashed()
 * @mixin \Eloquent
 */
class PartnerPayment extends Model
{
    use SoftDeletes;
    protected $table = 'partner_payments';
    protected $primaryKey = 'payment_id';

    protected $casts = [
        'reestr_id' => 'int',
        'partner_id' => 'int',
        'revenue' => 'float',
        'status' => 'int'
    ];

    protected $fillable = [
        'reestr_id',
        'partner_id',
        'partner',
        'pay_method',
        'pay_account',
        'revenue',
        'status',
        'datetime',
        'comment'
    ];

    public function payMethod() {
        return $this->belongsTo(PayMethod::class, 'pay_method');
    }

    public function payAccount() {
        return $this->belongsTo(UsersPayMethod::class, 'pay_account');
    }
}
