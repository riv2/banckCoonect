<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Lists\OrdersProductStateList;
use App\Lists\OrderStateList;
use App\Models\Traits\HasPpId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

/**
 * App\Models\OrdersProduct
 *
 * @property int $id
 * @property string $order_id
 * @property int $parent_id
 * @property \Jenssegers\Date\Date $datetime
 * @property int $pp_id
 * @property int|null $partner_id
 * @property int|null $category_id
 * @property int|null $offer_id
 * @property int|null $link_id
 * @property string $product_id
 * @property string|null $product_name
 * @property string|null $category
 * @property int|null $business_unit_id
 * @property float $price
 * @property int|null $quantity
 * @property float|null $total
 * @property float $amount
 * @property float $amount_advert
 * @property float|null $fee
 * @property int|null $fee_id
 * @property float $fee_advert
 * @property string|null $fee_type
 * @property string $status
 * @property string|null $web_id
 * @property string|null $click_id
 * @property int|null $pixel_id
 * @property int|null $reestr_id
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property string|null $deleted_at
 * @property-read mixed $edit_link
 * @property-read string $readable_status
 * @property-read \App\Models\RateRule|null $offerfee
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Pp $pp
 * @method static Builder|OrdersProduct newModelQuery()
 * @method static Builder|OrdersProduct newQuery()
 * @method static Builder|OrdersProduct query()
 * @mixin \Eloquent
 */
class OrdersProduct extends Model
{
    use HasPpId;

    protected $table = 'orders_products';
    public $incrementing = false;

    protected $primaryKey = 'id';

    protected $casts = [
        'partner_id'       => 'int',
        'category_id'      => 'int',
        'offer_id'         => 'int',
        'link_id'          => 'int',
        'business_unit_id' => 'int',
        'price'            => 'float',
        'amount'           => 'float',
        'amount_advert'    => 'float',
        'fee_advert'       => 'float',
        'quantity'         => 'int',
        'total'            => 'float',
        'fee'              => 'float',
        'pixel_id'         => 'int',
        'reestr_id'        => 'int',
        'banned_fraud_id'  => 'int',
        'banned_link_id'   => 'int',
    ];

    protected $dates = [
        'datetime'
    ];

    protected $fillable = [
        'datetime',
        'parent_id',
        'partner_id',
        'category_id',
        'offer_id',
        'link_id',
        'product_name',
        'category',
        'business_unit_id',
        'quantity',
        'total',
        'amount',
        'fee',
        'fee_id',
        'fee_advert',
        'amount_advert',
        'fee_type',
        'status',
        'web_id',
        'click_id',
        'pixel_id',
        'reestr_id',
        'banned_fraud_id',
        'banned_link_id'
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
                $builder->where('orders_products.partner_id', '=', auth()->id());
            });
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class, "parent_id", "id");
    }

    public function offerfee()
    {
        $date = date("Y-m-d", strtotime($this->datetime));
        $res = $this->hasOne(RateRule::class, "offer_id", "offer_id")
          ->whereNull('progressive_param')
            ->where(function ($where) {
                $where->where("rate_rules.business_unit_id", "=", $this->business_unit_id)
                    ->orWhereNull("rate_rules.business_unit_id");
            })
            ->whereRaw('(rate_rules.partner_id is NULL or rate_rules.partner_id=?)', $this->partner_id)
            ->whereRaw('(rate_rules.link_id is NULL or rate_rules.link_id=?)', $this->link_id)
            //Индивидуальная ставка приоритетнее всех остальных
            ->orderByDesc('partner_id')
            ->orderByDesc('link_id')
            ->orderByDesc('fee')
            ->orderBy("rate_rules.business_unit_id", "DESC")
            ->whereRaw("? between rate_rules.date_start and coalesce(rate_rules.date_end, ?)", [$date, $date])
        ;

        return $res;
    }

    public function getEditLinkAttribute()
    {
        $route = auth()->user()->role . '.orders.products.edit';
        if (Route::has($route)) {

            return view('components.orders_product.edit_link')->with([
                'route' => route($route, [$this->order_id, $this->product_id])
            ]);
        }

        return '';
    }

    public function getReadableStatusAttribute(): string
    {
        return OrdersProductStateList::getList()[$this->status ?? "new"];
    }


    public function getFeeStringAttribute()
    {
        if ($this->fee_type == 'sale_share') {
            $unit = '%';
        } elseif ($this->fee_type == 'sale_fix') {
            $unit = auth()->user()->pp->currency;
        } else {
            $unit = '';
        }

        if (!is_null($this->fee)) {
            return $this->fee . '&nbsp;' . $unit;
        }
        return '';
    }
}
