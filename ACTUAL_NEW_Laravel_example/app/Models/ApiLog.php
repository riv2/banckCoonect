<?php

namespace App\Models;

use App\Filters\ScopeFilter;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ApiLog
 *
 * @property int $api_id
 * @property int|null $offer_id
 * @property string|null $order_id
 * @property string|null $click_id
 * @property \Jenssegers\Date\Date $datetime
 * @property string $data_in
 * @property string|null $data_out
 * @property string|null $status
 * @property int|null $result
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ApiLog filter(\App\Filters\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiLog query()
 * @mixin \Eloquent
 */
class ApiLog extends Model
{
    use ScopeFilter;

    protected $table = 'api_log';
    protected $primaryKey = 'api_id';

    protected $casts = [
        'offer_id' => 'int',
        'result' => 'int'
    ];

    protected $dates = [
        'datetime'
    ];

    protected $fillable = [
        'offer_id',
        'order_id',
        'click_id',
        'datetime',
        'data_in',
        'data_out',
        'status',
        'result'
    ];
}
