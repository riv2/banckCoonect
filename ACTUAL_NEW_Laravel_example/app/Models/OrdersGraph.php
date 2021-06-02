<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\OrdersGraph
 *
 * @property int $id
 * @property string|null $order_id
 * @property int $offer_id
 * @property Date|null $datetime
 * @property int|null $partner_id
 * @property int|null $pp_id
 * @property int|null $category_id
 * @property int|null $landing_id
 * @property int|null $link_id
 * @property string|null $click_id
 * @property string|null $web_id
 * @property string|null $client_id
 * @property int|null $pixel_id
 * @property int|null $business_unit_id
 * @property string|null $fee
 * @property int|null $fee_id
 * @property string|null $fee_advert
 * @property string|null $model
 * @property string|null $gross_amount
 * @property string|null $amount
 * @property string|null $amount_advert
 * @property int $cnt_products
 * @property int|null $sale
 * @property int|null $reject
 * @property int|null $reestr_id
 * @property string $type
 * @property string|null $comment
 * @property string|null $status
 * @property int|null $status_cnt
 * @property Date|null $status_datetime
 * @property Date|null $last_updated
 * @property int $wholesale Заказ оптовый, не оплачиваем
 * @property Date|null $date
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property-read string $amount_currency
 * @property-read mixed $edit_link
 * @property-read string|null $penalty_view_link
 * @property-read string $readable_status
 * @property-read mixed $view_link
 * @property-read \App\Models\Link|null $link
 * @property-read \App\Models\NotifyParam|null $notifyParams
 * @property-read \App\Models\Offer $offer
 * @property-read \App\Models\RateRule|null $offerfee
 * @property-read \App\User|null $partner
 * @property-read \App\Models\Pp|null $pp
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrdersProduct[] $products
 * @property-read int|null $products_count
 * @method static Builder|Order filter(\App\Filters\QueryFilter $filters)
 * @method static Builder|OrdersGraph new(\Jenssegers\Date\Date $datetime_gt, \Jenssegers\Date\Date $datetime_lt, ?int $partner_id = null, ?int $link_id = null)
 * @method static Builder|OrdersGraph newModelQuery()
 * @method static Builder|OrdersGraph newQuery()
 * @method static Builder|OrdersGraph orders(\Jenssegers\Date\Date $datetime_gt, \Jenssegers\Date\Date $datetime_lt, ?int $partner_id = null, ?int $link_id = null)
 * @method static Builder|OrdersGraph query()
 * @method static Builder|OrdersGraph reject(\Jenssegers\Date\Date $datetime_gt, \Jenssegers\Date\Date $datetime_lt, ?int $partner_id = null, ?int $link_id = null)
 * @method static Builder|OrdersGraph sale(\Jenssegers\Date\Date $datetime_gt, \Jenssegers\Date\Date $datetime_lt, ?int $partner_id = null, ?int $link_id = null)
 * @method static Builder|Order sales()
 * @mixin \Eloquent
 */
class OrdersGraph extends Order
{
    protected $casts = [
        'date_datetime' => 'string',
        'aggregate' => 'integer',
    ];

    /**
     * Получает статистику
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Jenssegers\Date\Date $datetime_gt
     * @param \Jenssegers\Date\Date $datetime_lt
     * @param integer|null $partner_id
     * @param integer|null $link_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrders(Builder $query, Date $datetime_gt, Date $datetime_lt, int $partner_id = null, int $link_id = null)
    {
        return $query
            ->selectRaw('DATE(datetime) AS date_datetime')
            ->selectRaw('COUNT(*) aggregate')
            ->whereBetween(DB::raw('DATE(datetime)'), [$datetime_gt->toDateString(), $datetime_lt->toDateString()])
            ->when($partner_id, function ($query) use ($partner_id) {
                return $query->where('partner_id', $partner_id);
            })
            ->when($link_id, function ($query) use ($link_id) {
                return $query->where('link_id', $link_id);
            })
            ->groupByRaw('DATE(datetime)');
    }

    /**
     * Получает статистику
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Date $datetime_gt
     * @param Date $datetime_lt
     * @param integer|null $partner_id
     * @param integer|null $link_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReject(Builder $query, Date $datetime_gt, Date $datetime_lt, int $partner_id = null, int $link_id = null)
    {
        return $query
            ->selectRaw('DATE(datetime) AS date_datetime')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = "reject" THEN 1 END), 0) AS aggregate')
            ->whereBetween(DB::raw('DATE(datetime)'), [Date::parse($datetime_gt)->toDateString(), Date::parse($datetime_lt)->toDateString()])
            ->when($partner_id, function ($query) use ($partner_id) {
                return $query->where('partner_id', $partner_id);
            })
            ->when($link_id, function ($query) use ($link_id) {
                return $query->where('link_id', $link_id);
            })
            ->groupByRaw('DATE(datetime)');
    }

    /**
     * Получает статистику
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Date $datetime_gt
     * @param Date $datetime_lt
     * @param integer|null $partner_id
     * @param integer|null $link_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSale(Builder $query, Date $datetime_gt, Date $datetime_lt, int $partner_id = null, int $link_id = null)
    {
        return $query
            ->selectRaw('DATE(datetime) AS date_datetime')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = "sale" THEN 1 END), 0) AS aggregate')
            ->whereBetween(
                DB::raw('DATE(datetime)'),
                [Date::parse($datetime_gt)->toDateString(), Date::parse($datetime_lt)->toDateString()]
            )
            ->when($partner_id, function ($query) use ($partner_id) {
                return $query->where('partner_id', $partner_id);
            })
            ->when($link_id, function ($query) use ($link_id) {
                return $query->where('link_id', $link_id);
            })
            ->groupByRaw('DATE(datetime)');
    }

    public function scopeNew(Builder $query, Date $datetime_gt, Date $datetime_lt, int $partner_id = null, int $link_id = null)
    {
        return $query
            ->selectRaw('DATE(datetime) AS date_datetime')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = "new" THEN 1 END), 0) AS aggregate')
            ->whereBetween(
                DB::raw('DATE(datetime)'),
                [Date::parse($datetime_gt)->toDateString(), Date::parse($datetime_lt)->toDateString()]
            )
            ->when($partner_id, function ($query) use ($partner_id) {
                return $query->where('partner_id', $partner_id);
            })
            ->when($link_id, function ($query) use ($link_id) {
                return $query->where('link_id', $link_id);
            })
            ->groupByRaw('DATE(datetime)');
    }
}
