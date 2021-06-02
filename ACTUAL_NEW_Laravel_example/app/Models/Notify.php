<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Notify
 *
 * @property int $notify_id
 * @property \Jenssegers\Date\Date $datetime
 * @property int|null $partner_id
 * @property string|null $click_id
 * @property string|null $web_id
 * @property string|null $order_id
 * @property int|null $link_id
 * @property string|null $link_postback_param
 * @property string|null $model
 * @property string|null $status
 * @property int|null $fee_id
 * @property int|null $amount
 * @property int|null $gross_amount
 * @property string|null $comment
 * @property int $sent_cnt
 * @property string|null $sent_url
 * @property string|null $sent_method
 * @property string|null $sent_request
 * @property \Jenssegers\Date\Date|null $sent_datetime
 * @property int|null $responce_httpcode
 * @property string|null $responce_body
 * @property-read \App\Models\NotifyParam|null $notify_param
 * @method static \Illuminate\Database\Eloquent\Builder|Notify newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notify newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notify query()
 * @mixin \Eloquent
 */
class Notify extends Model
{
    protected $table = 'notify';
    protected $primaryKey = 'notify_id';
    public $timestamps = false;

    protected $casts = [
        'partner_id' => 'int',
        'link_id' => 'int',
        'amount' => 'int',
        'gross_amount' => 'int',
        'sent_cnt' => 'int',
        'responce_httpcode' => 'int'
    ];

    protected $dates = [
        'datetime',
        'sent_datetime'
    ];

    protected $fillable = [
        'datetime',
        'partner_id',
        'click_id',
        'web_id',
        'order_id',
        'link_id',
        'link_postback_param',
        'model',
        'status',
        'fee_id',
        'amount',
        'comment',
        'sent_cnt',
        'sent_url',
        'sent_method',
        'sent_request',
        'sent_datetime',
        'responce_httpcode',
        'responce_body'
    ];

    public function notify_param()
    {
        return $this->hasOne(NotifyParam::class, 'partner_id', 'partner_id');
    }

}
