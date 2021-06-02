<?php

namespace App\Models;

use App\Lists\OrderStateList;
use App\User;
use App\Filters\ScopeFilter;
use App\Models\Traits\HasPpId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Route;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string|null $order_id
 * @property int $offer_id
 * @property \Jenssegers\Date\Date|null $datetime
 * @property \Jenssegers\Date\Date|null $datetime_sale
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
 * @property float|null $fee
 * @property int|null $fee_id
 * @property float|null $fee_advert
 * @property string|null $model
 * @property float|null $gross_amount
 * @property float|null $amount
 * @property float|null $amount_advert
 * @property int $cnt_products
 * @property int|null $sale
 * @property int|null $reject
 * @property int|null $reestr_id
 * @property string $type
 * @property string|null $comment
 * @property string|null $status
 * @property int|null $status_cnt
 * @property \Jenssegers\Date\Date|null $status_datetime
 * @property \Jenssegers\Date\Date|null $last_updated
 * @property int $wholesale Заказ оптовый, не оплачиваем
 * @property \Jenssegers\Date\Date|null $date
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
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
 * @property-read User|null $partner
 * @property-read \App\Models\Pp|null $pp
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrdersProduct[] $products
 * @property-read int|null $products_count
 * @method static Builder|Order filter(\App\Filters\QueryFilter $filters)
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @method static Builder|Order sales()
 * @mixin \Eloquent
 */
class Order extends Model
{
    use ScopeFilter;
    use HasPpId;

    protected $table = 'orders';
    public $incrementing = false;

    protected $primaryKey = "order_id";

    protected $casts = [
        'offer_id'         => 'int',
        'partner_id'       => 'int',
        'pp_id'            => 'int',
        'category_id'      => 'int',
        'landing_id'       => 'int',
        'link_id'          => 'int',
        'pixel_id'         => 'int',
        'business_unit_id' => 'int',
        'fee'              => 'float',
        'fee_id'           => 'int',
        'fee_advert'       => 'float',
        'gross_amount'     => 'float',
        'amount'           => 'float',
        'amount_advert'    => 'float',
        'cnt_products'     => 'int',
        'sale'             => 'int',
        'reject'           => 'int',
        'reestr_id'        => 'int',
        'status_cnt'       => 'int',
        'wholesale'        => 'int',
        'banned_fraud_id'  => 'int',
        'banned_link_id'   => 'int'
    ];

    protected $fillable = [
        'order_id',
        'datetime',
        'datetime_sale',
        'partner_id',
        'pp_id',
        'category_id',
        'landing_id',
        'link_id',
        'click_id',
        'offer_id',
        'web_id',
        'client_id',
        'pixel_id',
        'business_unit_id',
        'fee',
        'fee_advert',
        'model',
        'gross_amount',
        'amount',
        'amount_advert',
        'cnt_products',
        'sale',
        'reject',
        'reestr_id',
        'type',
        'status',
        'status_cnt',
        'status_datetime',
        'last_updated',
        'wholesale',
        'date',
        'banned_fraud_id',
        'banned_link_id'
    ];

    protected $attributes = [
        'status' => 'new',
    ];

    protected $dates = [
        'datetime',
        'status_datetime',
        'last_updated',
        'date'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('user_id', function (Builder $builder) {
            $is_partner = auth()->user() && auth()->user()->role === 'partner';

            $builder->when($is_partner, function (Builder $builder) {
                $builder->where('orders.partner_id', '=', auth()->id());
            });
        });
    }

    public function offerfee()
    {
        return $this->hasOne(RateRule::class, "offer_id", "offer_id")
            ->whereNull('progressive_param')
            ->whereRaw('(rate_rules.partner_id is NULL or rate_rules.partner_id=?)', $this->partner_id)
            ->whereRaw('(rate_rules.link_id is NULL or rate_rules.link_id=?)', $this->link_id)
            ->where(function ($where) {
                $date = $this->date ?? date("Y-m-d", strtotime($this->datetime));
                $where->whereRaw("? between
            rate_rules.date_start and coalesce(rate_rules.date_end, ?)
            ", [$date, $date]);
            })
            //Индивидуальная ставка приоритетнее всех остальных
            ->orderByDesc('partner_id')
            ->orderByDesc('link_id')
            ->orderByDesc('fee');
    }

    public function notifyParams()
    {
        return $this->hasOne(NotifyParam::class, 'partner_id', 'partner_id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSales(Builder $query): Builder
    {
        return $query
            ->where('sale', '=', 1);
    }

    /**
     * Партнер, к которому относится заказ
     *
     * @return Relation
     */
    public function partner(): Relation
    {
        return $this->belongsTo(User::class, 'partner_id', 'id');
    }

    /**
     * Оффер, к которому относится заказ
     *
     * @return Relation
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'offer_id', 'id');
    }

    /**
     * Оффер, к которому относится заказ
     *
     * @return Relation
     */
    public function link(): Relation
    {
        return $this->belongsTo(Link::class, 'link_id', 'id');
    }

    public function getViewLinkAttribute()
    {
        $route = auth()->user()->role . '.orders.show';
        if (Route::has($route)) {
            return view('components.order.view_link')->with([
                'route' => route($route, $this),
                'orderId' => $this->order_id,
            ]);
        }

        return '';
    }

    /**
     * @return string|null
     */
    public function getPenaltyViewLinkAttribute()
    {
        $result = $this->order_id;
        $route = auth()->user()->role . '.penaltys.show';
        if (Route::has($route)) {
            $result = '<a href="' . route($route, ['penalty' => $this->order_id]) . '" class="js-click-tr">' . $this->id . ' - ' . $this->type . '</a>';
        }
        return $result;
    }

    public function getEditLinkAttribute()
    {
        $route = auth()->user()->role . '.penaltys.edit';
        $result = '<a href="' . route($route, [$this->order_id]) . '" class="btn btn-outline-primary btn-sm">' . __('advertiser.penaltys.edit') .  '</a>';

        return $result;
    }

    public function products()
    {
        return $this->hasMany(OrdersProduct::class, "order_id", "order_id");
    }

    public function pp()
    {
        return $this->hasOne(Pp::class, "id", "pp_id");
    }

    public function getReadableStatusAttribute(): string
    {
        return OrderStateList::getList()[$this->status ?? "new"];
    }


    public function getAmountCurrencyAttribute(): string
    {
        if (!is_null($this->amount)) {
            if (auth()->user()->role == 'manager') {
                $pp = Pp::query()->where('id', '=', $this->pp_id)->first();
                return $this->amount . ' ' . $pp->currency;
            }
            return $this->amount . ' ' . auth()->user()->pp->currency;
        }
        return '';
    }

}
