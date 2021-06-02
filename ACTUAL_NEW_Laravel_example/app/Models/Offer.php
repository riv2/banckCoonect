<?php

namespace App\Models;

use App\Helpers\PartnerProgramStorage;
use App\Lists\OffersMetumList;
use App\Lists\OrderStateList;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Filters\ScopeFilter;
use App\Models\Traits\HasPpId;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Offer
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $pp_id
 * @property string|null $offer_name
 * @property string|null $model
 * @property string|null $fee_type
 * @property float $fee
 * @property float $fee_advert
 * @property float|null $ctr
 * @property float|null $cr
 * @property float|null $ar
 * @property string|null $info_link
 * @property string|null $description
 * @property string|null $image
 * @property string|null $link_template
 * @property string|null $utm_source
 * @property string|null $click_id_name
 * @property string|null $web_id_name
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property \Jenssegers\Date\Date|null $deleted_at
 * @property-read mixed $fee_string
 * @property-read string $landing_link
 * @property-read mixed $metas
 * @property-read mixed $rate_rules
 * @property-read mixed $view_link
 * @property-read Collection|\App\Models\Link[] $links
 * @property-read int|null $links_count
 * @property-read Collection|\App\Models\Link[] $linksWithDeleted
 * @property-read int|null $links_with_deleted_count
 * @property-read \App\Models\Pp|null $pp
 * @method static Builder|Offer allowed()
 * @method static Builder|Offer filter(\App\Filters\QueryFilter $filters)
 * @method static Builder|Offer newModelQuery()
 * @method static Builder|Offer newQuery()
 * @method static \Illuminate\Database\Query\Builder|Offer onlyTrashed()
 * @method static Builder|Offer query()
 * @method static \Illuminate\Database\Query\Builder|Offer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Offer withoutTrashed()
 * @mixin \Eloquent
 */
class Offer extends Model
{
    use SoftDeletes;
    use ScopeFilter;
    use HasPpId;

    public $metas = null;

    protected $table = 'offers';
    protected $primaryKey = 'id';

    protected $casts = [
        'user_id' => 'int',
        'pp_id' => 'int',
        'fee' => 'float',
        'fee_advert' => 'float',
        'ctr' => 'float',
        'cr' => 'float',
        'ar' => 'float',
        'flag_approvable' => 'boolean',
    ];

    protected $fillable = [
        'user_id',
        'offer_name',
        'category_id',
        'lead',
        'model',
        'fee',
        'fee_advert',
        'datetime',
        'datetime_end',
        'ctr',
        'cr',
        'ar',
        'info_link',
        'tag',
        'not_for_api',
        'only_landings',
        'geo',
        'utm_medium',
        'description',
        'image',
        'link_template',
        'link_domain',
        'click_id_name',
        'web_id_name'
    ];

    /** @return void */
    protected static function booted(): void
    {
        static::creating(function (Offer $offer) {
            // Генерируем link_template при создании оффера
            $offer->link_template = $offer->generateLinkTemplate();
        });
    }

    public function getMetas()
    {
        if (!empty($this->id)) {
            $this->metas = OffersMetum::query()->where("offer_id", "=", $this->id)
                ->whereIn("meta_name", array_keys(OffersMetumList::getList()))->get()->pluck("meta_value", "meta_name");
        }
    }

    /**
     * Получить офферы для этого адверта
     *
     * @deprecated 2020-02-24
     * @param \App\User $user
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\Offer>
     * @throws \Exception
     * @todo https://rt.gocpa.ru/task/2394
     *
     * @deprecated 2020-02-24
     */
    public static function getOwnOffers(User $user)
    {
        $q = static::query();
        if ($user->role == 'advertiser') {
            return $q->where('user_id', '=', $user->id)->get();
        } elseif ($user->role == 'manager') {
            return $q->get();
        } elseif ($user->role == 'partner') {
            return $q->whereHas('links', function (Builder $query) use ($user) {
                $query->where('partner_id', '=', $user->id);
            })->get();
        } elseif ($user->role == 'analyst') {
            return $q->where('user_id', '=', $user->id)->get();
        } else {
            throw new \Exception("Error Processing Request", 1);
        }
    }

    public function getMeta($meta, $metaType = "string", $default = null)
    {
        if (is_null($this->metas)) {
            $this->getMetas();
        }
        $result = isset($this->metas[$meta]) ? $this->metas[$meta] : $default;
        switch ($metaType) {
            case "db_list":
            case "list":
                return json_decode($result, true) ?? [];
            default:
                return $result;
        }
    }

    public function setMeta($meta, $value, $metaData)
    {
        $item = OffersMetum::query()->where("offer_id", "=", $this->id)
            ->where("meta_name", "=", $meta)->firstOrNew();
        if (is_null($value)) {
            if (!empty($item)) {
                $item->delete();
                return;
            }
        }
        switch ($metaData["type"]) {
            case "db_list":
            case "list":
            {
                if (!is_array($value)) {
                    $value = [];
                }
                $value = json_encode($value);
                break;
            }
            default:
            {
                $value = trim($value);
            }
        }
        $item->fill([
            "offer_id" => $this->id,
            "meta_name" => $meta,
            "meta_value" => $value
        ])->save();
    }

    /** @return bool */
    public function needApprove()
    {
        return $this->flag_approvable;
    }

    public function getMaterialCount()
    {
        return $this
            ->hasMany(OfferMaterial::class, 'offer_id')
            ->count();
    }

    /**
     * Список ссылок, которые можно просматривать пользователю
     *
     * @return Relation
     */
    public function links()
    {
        return $this
            ->hasMany(Link::class, 'offer_id')
            ->allowed()
            ->active();
    }

    /**
     * Список ссылок, которые можно просматривать пользователю
     *
     * @return Relation
     */
    public function linksWithDeleted()
    {
        return $this
            ->hasMany(Link::class, 'offer_id')
            ->allowed()
            ->withDeleted();
    }

    public function partnersApproves($partner_id = null, $status = null)
    {
        return $this
            ->belongsToMany(User::class, 'offers_partners_approves', 'offer_id', 'partner_id')
            ->when($partner_id, function (Builder $query, $value) {
                $query->where('partner_id', '=', $value);
            })
            ->when($status, function (Builder $query, $value) {
                $query->where('offers_partners_approves.status', '=', $value);
            });
    }

    /**
     * Автоматическое заполнение поля link_template
     *
     * @throws Exception
     * @return string
     */
    public function generateLinkTemplate(): string
    {
        if (config('app.gocpa_project') == 'cloud') {
            $params = [
                'utm_medium' => 'cpa',
                'utm_source' => 'partners',
                'utm_campaign' => '{$link_id}',
                'utm_content' => '{$partner_id}',
                'utm_term' => '{WEB_ID}',
                'click_id' => '{CLICK_ID}',
            ];
        } elseif (config('app.gocpa_project') == 'cpadroid') {
            $params = [
                'utm_medium' => 'cpa',
                'utm_source' => '{$partner_hash_name}',
                'utm_campaign' => 'Pochta@Cash@lpCash@{$partner_hash_name}@Platform@{$link_id}@{WEB_ID}@{CLICK_ID}',
                'cpa_partner_id' => '{WEB_ID}',
                'cpa_click_id' => '{CLICK_ID}',
            ];
        }
        $link_template = urldecode(http_build_query($params));
        return $link_template;
    }

    /**
     * Scope для показа офферов по ролям
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeAllowed(Builder $query): Builder
    {
        $user = auth()->user();
        // $is_admin = $user->role === 'admin';
        // $is_manager = $user->role === 'manager';
        // $is_advertiser = $user->role === 'advertiser';
        // $is_partner = $user->role === 'partner';

        return $query;
    }

    public function getMetasAttribute()
    {
        $route = auth()->user()->role . '.offers.show';
        if (Route::has($route)) {
            return view('components.offer.metas')->with(['route' => route($route, $this)]);
        }

        return '';
    }

    public function getViewLinkAttribute()
    {
        $route = auth()->user()->role . '.offers.show';
        if (Route::has($route)) {
            return view('components.offer.view_link')
                ->with([
                    'route' => route($route, $this),
                    'offerName' => $this->offer_name,
                    'offerId' => $this->id,
                ]);
        }

        return '';
    }

    public function getLandingLinkAttribute(): string
    {
        if ($this->flag_approvable && !$this->partnersApproves(auth()->id(), 'approved')->exists()) {
            return '-';
        }

        $OfferMaterial = OfferMaterial::query()
            ->where('offer_id', '=', $this->id)
            ->where('material_type', '=', 'landing')
            ->first();
        if (isset($OfferMaterial)) {
            return view('components.offer.landing_link')->with(['route' => route('partner.links.create', [
                'offer_id' => $this->id,
                'offer_material_id' => $OfferMaterial->offer_material_id,])]);
        } else {
            return '';
        }
    }

    public function getFeeStringAttribute(): string
    {
        //Временно закомментировал TODO сделать настройкой
        //if ($this->flag_approvable && !$this->partnersApproves(auth()->id(), 'approved')->exists()) {
        //    return '-';
        //}

        //Получим инфо про НДС
        $user = auth()->user();

        $nds = '';
        if ($user->pay_method && $user->pay_method->pay_method_id == 1 && $this->fee_type == 'fix') {
            if ($user->pay_method->vat_tax == 0) {
                $nds = 'без НДС';
            } else {
                $nds = 'с НДС';
            }
        }

        //Получим все ставки (по offer_id и pp_id)
        $rateRules = RateRule::query()->where('offer_id', '=', $this->id)->get();

        //Сначала ищем индивидуальную ставку
        $individualRateRule = [];
        foreach ($rateRules as $rateRule) {
            if (auth()->user()->id == $rateRule->partner_id) {
                array_push($individualRateRule, $rateRule);
            }
        }
        if (count($individualRateRule) == 1) {
            //вывод индивидуальной ставки
            $fee_string = '';
            switch ($this->fee_type):
                case 'fix':
                    $fee_string .= $individualRateRule[0]->fee . ' ' . auth()->user()->pp->currency;
            break;
            case 'share':
                    $fee_string .= $individualRateRule[0]->fee . '%';
            break;
            default:
                    $fee_string .= $individualRateRule[0]->fee;
            endswitch;

            $fee_string .= ' ' . __('offer.for') . ' ' . '<span style="text-transform: lowercase">'
                . OrderStateList::getList('V')[$this->model] . ' ' . $nds . '</span>';
            return $fee_string;
        } else {
            //Индивидуальная ставка не найдена
            //Сформируем актуальные ставки
            $currentRateRules = [];
            foreach ($rateRules as $rateRule) {
                $startDate = Carbon::parse($rateRule->date_start);
                if (is_null($rateRule->date_end)) {
                    if ($startDate->lessThan(now())) {
                        array_push($currentRateRules, $rateRule);
                    }
                } else {
                    $endDate = Carbon::parse($rateRule->date_end);
                    if (now()->between($startDate, $endDate)) {
                        array_push($currentRateRules, $rateRule);
                    }
                }
            }

            if (isset($currentRateRules[0])) {
                switch ($this->fee_type):
                    case 'fix':
                        $fee_string = $currentRateRules[0]->fee . ' '
                            . Pp::query()->where('id', '=', $this->pp_id)->first()->currency;
                break;
                case 'share':
                        $fee_string = $currentRateRules[0]->fee . '%';
                break;
                default:
                        $fee_string = $currentRateRules[0]->fee;
                endswitch;

                $fee_string .= ' ' . __('offer.for') . ' ' . '<span style="text-transform: lowercase">'
                    . OrderStateList::getList('V')[$this->model] . ' ' . $nds . '</span>';
                return $fee_string;
            }
        }
        return __('offer.missing_current_fee');
    }

    public function getFeeAdvertStringAttribute(): string
    {
        //Получим все ставки (по offer_id и pp_id)
        $rateRules = RateRule::query()->where('offer_id', '=', $this->id)->get();

        //Сначала ищем индивидуальную ставку
        $individualRateRule = [];
        foreach ($rateRules as $rateRule) {
            if (auth()->user()->id == $rateRule->partner_id) {
                array_push($individualRateRule, $rateRule);
            }
        }
        if (count($individualRateRule) == 1) {
            //вывод индивидуальной ставки
            $fee_string = '';
            switch ($this->fee_type):
                case 'fix':
                    $fee_string .= $individualRateRule[0]->fee_advert . ' ' . auth()->user()->pp->currency;
            break;
            case 'share':
                    $fee_string .= $individualRateRule[0]->fee_advert . '%';
            break;
            default:
                    $fee_string .= $individualRateRule[0]->fee_advert;
            endswitch;

            $fee_string .= ' ' . __('offer.for') . ' ' . '<span style="text-transform: lowercase">'
                . OrderStateList::getList('V')[$this->model] . '</span>';
            return $fee_string;
        } else {
            //Индивидуальная ставка не найдена
            //Сформируем актуальные ставки
            $currentRateRules = [];
            foreach ($rateRules as $rateRule) {
                $startDate = Carbon::parse($rateRule->date_start);
                if (is_null($rateRule->date_end)) {
                    if ($startDate->lessThan(now())) {
                        array_push($currentRateRules, $rateRule);
                    }
                } else {
                    $endDate = Carbon::parse($rateRule->date_end);
                    if (now()->between($startDate, $endDate)) {
                        array_push($currentRateRules, $rateRule);
                    }
                }
            }

            if (isset($currentRateRules[0])) {
                switch ($this->fee_type):
                    case 'fix':
                        $fee_string = $currentRateRules[0]->fee_advert . ' '
                            . Pp::query()->where('id', '=', $this->pp_id)->first()->currency;
                break;
                case 'share':
                        $fee_string = $currentRateRules[0]->fee_advert . '%';
                break;
                default:
                        $fee_string = $currentRateRules[0]->fee_advert;
                endswitch;

                $fee_string .= ' ' . __('offer.for') . ' ' . '<span style="text-transform: lowercase">'
                    . OrderStateList::getList('V')[$this->model] . '</span>';
                return $fee_string;
            }
        }
        return __('offer.missing_current_fee');
    }

    public function getRateRulesAttribute()
    {
        //Получим все ставки (по offer_id и pp_id)
        $rateRules = RateRule::query()->where('offer_id', '=', $this->id)->get();

        //Сначала ищем индивидуальную ставку
        $individualRateRule = [];
        foreach ($rateRules as $rateRule) {
            if (auth()->user()->id == $rateRule->partner_id) {
                array_push($individualRateRule, $rateRule);
            }
        }
        if (count($individualRateRule) == 1) {
            //вывод индивидуальной ставки

            return view('components.offer.rate_rules')
                ->with([
                    'rateRules' => $individualRateRule,
                    'feeType' => $this->fee_type,
                    'orderState' => OrderStateList::getList('V')[$this->model],
                    'currency' => auth()->user()->pp->currency,
                ]);
        } else {
            //Индивидуальная ставка не найдена
            //Сформируем актуальные ставки
            $currentRateRules = [];
            foreach ($rateRules as $rateRule) {
                $startDate = Carbon::parse($rateRule->date_start);
                if (is_null($rateRule->date_end)) {
                    if ($startDate->lessThan(now())) {
                        array_push($currentRateRules, $rateRule);
                    }
                } else {
                    $endDate = Carbon::parse($rateRule->date_end);
                    if (now()->between($startDate, $endDate)) {
                        array_push($currentRateRules, $rateRule);
                    }
                }
            }
            if (is_null($currentRateRules)) {
                return '';
            } elseif (count($currentRateRules) == 1) {
                return view('components.offer.rate_rules')
                    ->with([
                        'rateRules' => $currentRateRules,
                        'feeType' => $this->fee_type,
                        'orderState' => OrderStateList::getList('V')[$this->model],
                        'currency' => auth()->user()->pp->currency,
                    ]);
            } else {
                foreach ($currentRateRules as $currentRateRule):
                    if (!is_null($currentRateRule->business_unit_id)) {
                        $currentRateRule->category_name = BusinessUnit::query()
                            ->where('category_id', '=', $currentRateRule->business_unit_id)
                            ->first()
                            ->category_name;
                    }
                endforeach;

                return view('components.offer.rate_rules')
                    ->with([
                        'rateRules' => $currentRateRules,
                        'feeType' => $this->fee_type,
                        'orderState' => OrderStateList::getList('V')[$this->model],
                        'currency' => auth()->user()->pp->currency,
                    ]);
            }
        }
    }
}
