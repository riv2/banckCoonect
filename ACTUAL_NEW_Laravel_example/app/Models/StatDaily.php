<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StatDaily
 *
 * @property \Jenssegers\Date\Date $date
 * @property int $partner_id
 * @property int|null $offer_id
 * @property int|null $landing_id
 * @property int|null $link_id
 * @property int|null $clicks
 * @property int $orders
 * @property int|null $approve
 * @property int $sale
 * @property int|null $activated
 * @property int|null $transactioned
 * @property int|null $revenue
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|StatDaily newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StatDaily newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StatDaily query()
 * @mixin \Eloquent
 */
class StatDaily extends Model
{
    protected $table = 'stat_daily';
    public $incrementing = false;

    protected $casts = [
        'partner_id' => 'int',
        'offer_id' => 'int',
        'landing_id' => 'int',
        'link_id' => 'int',
        'clicks' => 'int',
        'orders' => 'int',
        'approve' => 'int',
        'sale' => 'int',
        'activated' => 'int',
        'transactioned' => 'int',
        'revenue' => 'int'
    ];

    protected $dates = [
        'date'
    ];

    protected $fillable = [
        'offer_id',
        'landing_id',
        'clicks',
        'orders',
        'approve',
        'sale',
        'activated',
        'transactioned',
        'revenue'
    ];
}
