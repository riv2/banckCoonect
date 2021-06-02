<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Lists\RateRulesParams;
use App\Models\Traits\HasPpId;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

/**
 * App\Models\RateRule
 *
 * @property int $id
 * @property int|null $partner_id id партнера
 * @property int|null $user_cat id категории партнера
 * @property int|null $business_unit_id id БЮ
 * @property float|null $fee Ставка для БЮ
 * @property float|null $fee_advert
 * @property int $pp_id
 * @property int $offer_id
 * @property int|null $link_id
 * @property int|null $progressive_value Мин. значение для срабатывания прогрессивной ставки
 * @property string|null $progressive_param Параметр, учитываемый в ставке(оборот, кол-во заказов)
 * @property \Jenssegers\Date\Date $date_start Дата старта
 * @property \Jenssegers\Date\Date|null $date_end Дата окончания
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property \Jenssegers\Date\Date|null $deleted_at
 * @property-read mixed $business_unit_string
 * @property-read mixed $edit_button
 * @property-read mixed $link_name
 * @property-read mixed $partner_name
 * @property-read mixed $progressive_param_name
 * @property-read mixed $progressive_value_txt
 * @property-read \App\Models\Link|null $link
 * @property-read User|null $partner
 * @property-read \App\Models\Pp $pp
 * @method static \Illuminate\Database\Eloquent\Builder|RateRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RateRule newQuery()
 * @method static \Illuminate\Database\Query\Builder|RateRule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RateRule query()
 * @method static \Illuminate\Database\Query\Builder|RateRule withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RateRule withoutTrashed()
 * @mixin \Eloquent
 */
class RateRule extends Model
{
    use SoftDeletes;
    use HasPpId;

    protected $table = 'rate_rules';

    protected $casts = [
        'partner_id' => 'int',
        'progressive_value' => 'int',
        'user_cat' => 'int',
        'business_unit_id' => 'int',
        'fee' => 'float',
        'fee_advert' => 'float',
        'pp_id' => 'int',
        'offer_id' => 'int',
        'link_id' => 'int'
    ];

    protected $dates = [
        'date_start',
        'date_end'
    ];

    protected $fillable = [
        'partner_id',
        'pp_id',
        'progressive_value',
        'progressive_param',
        'user_cat',
        'business_unit_id',
        'fee',
        'fee_advert',
        'offer_id',
        'link_id',
        'date_start',
        'date_end'
    ];
    /**
     * @var mixed|void
     */

    public function getAvailableProgressiveParam()
    {
        return $this->available_progressive_params ??
            $this->available_progressive_params = ($this->pp->pp_target == 'products') ?
                Arr::only(RateRulesParams::getList(),['orders','amount'])
                : Arr::only(RateRulesParams::getList(),['orders']);
    }

    public function partner()
    {
        return $this->hasOne(User::class, 'id', 'partner_id');
    }

    public function link()
    {
        return $this->hasOne(Link::class, 'id', 'link_id');
    }


    public function getProgressiveParamNameAttribute()
    {
        return $this->progressive_param ? RateRulesParams::getList()[$this->progressive_param] : '';
    }

    public function getProgressiveValueTxtAttribute()
    {
        return $this->progressive_value ? __('От ') . $this->progressive_value : '';
    }

    public function getPartnerNameAttribute()
    {
        return optional($this->partner)->email;
    }

    public function getLinkNameAttribute()
    {
        return optional($this->link)->link_name;
    }

    public function getEditButtonAttribute()
    {
        $route = auth()->user()->role . '.rateRule.edit';
        if (Route::has($route)) {
            return view('components.rate_rule.edit_button')->with([
                'route' => route($route, $this)
            ]);
        }

        return '';
    }

    public function getBusinessUnitStringAttribute()
    {
        if (is_null($this->business_unit_id)) {
            $business_unit_id = 'Категория по умолчанию';
        } else {
            $business_unit = BusinessUnit::query()
                ->where('category_id', '=', $this->business_unit_id)->first();
            $business_unit_id = $business_unit->category_name;
        }
        return $business_unit_id;
    }
}
