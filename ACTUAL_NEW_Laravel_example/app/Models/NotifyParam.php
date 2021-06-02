<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NotifyParam
 *
 * @property int $partner_id
 * @property string $type
 * @property string|null $postback_url
 * @property string|null $postback_auth
 * @property string $method
 * @property string|null $order_id
 * @property string|null $status
 * @property string|null $fee_id
 * @property string|null $amount
 * @property string|null $gross_amount
 * @property string|null $status_new_value
 * @property string|null $status_approve_value
 * @property string|null $status_sale_value
 * @property string|null $status_reject_value
 * @property string|null $web_id
 * @property string|null $click_id
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|NotifyParam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotifyParam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotifyParam query()
 * @mixin \Eloquent
 */
class NotifyParam extends Model
{
    protected $table = 'notify_params';
    protected $primaryKey = 'partner_id';
    public $incrementing = false;

    protected $casts = [
        'partner_id' => 'int'
    ];

    protected $fillable = [
        'partner_id',
        'fee_id',
        'type',
        'postback_url',
        'postback_auth',
        'method',
        'web_id',
        'click_id',
        'gross_amount',
        'amount',
        'status',
        'order_id',
        'status_new_value',
        'status_approve_value',
        'status_sale_value',
        'status_reject_value',
        'status_transaction_value'
    ];
}
