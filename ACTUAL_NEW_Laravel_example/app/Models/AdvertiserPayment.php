<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\AdvertiserPayment
 *
 * @property int $payment_id
 * @property int|null $reestr_id
 * @property int|null $advertiser_id
 * @property string|null $advertiser
 * @property string|null $pay_method
 * @property string|null $pay_account
 * @property int $revenue
 * @property int|null $status
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property \Jenssegers\Date\Date|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdvertiserPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdvertiserPayment newQuery()
 * @method static \Illuminate\Database\Query\Builder|AdvertiserPayment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AdvertiserPayment query()
 * @method static \Illuminate\Database\Query\Builder|AdvertiserPayment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AdvertiserPayment withoutTrashed()
 * @mixin \Eloquent
 */
class AdvertiserPayment extends Model
{
    use SoftDeletes;

    protected $table = 'advertiser_payments';
    protected $primaryKey = 'payment_id';

    protected $casts = [
        'reestr_id' => 'int',
        'advertiser_id' => 'int',
        'revenue' => 'int',
        'status' => 'int'
    ];

    protected $fillable = [
        'reestr_id',
        'advertiser_id',
        'advertiser',
        'pay_method',
        'pay_account',
        'revenue',
        'status'
    ];
}
