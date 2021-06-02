<?php

namespace App\Models;

use App\Lists\PpOnboardingList;
use App\Lists\PpTargetList;
use App\Lists\PpTariffList;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Pp
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $tech_domain
 * @property string|null $prod_domain
 * @property string|null $short_name
 * @property string|null $long_name
 * @property string|null $onboarding_status
 * @property string|null $company_url
 * @property string|null $pp_target
 * @property string|null $currency
 * @property string|null $logo
 * @property string|null $favicon
 * @property string|null $branch
 * @property string|null $color1
 * @property string|null $color2
 * @property string|null $color3
 * @property string|null $color4
 * @property array $lang
 * @property string $tariff
 * @property string $status
 * @property int $stopupdate
 * @property \Jenssegers\Date\Date|null $demo_ends_at
 * @property string|null $comment
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property-read mixed $onboarding_status_text
 * @property-read mixed $pp_domain
 * @property-read string|null $pp_target_text
 * @property-read string|null $tariff_text
 * @property-read Collection|\App\Models\Offer[] $offers
 * @property-read int|null $offers_count
 * @property-read Collection|\App\Models\PayMethod[] $pay_methods
 * @property-read int|null $pay_methods_count
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Pp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pp query()
 * @mixin \Eloquent
 */
class Pp extends Model
{
    protected $table = 'pp';

    protected $casts = [
        'user_id' => 'int',
        'demo_ends_at' => 'datetime',
        'lang' => 'json',
    ];

    protected $fillable = [
        'user_id',
        'tech_domain',
        'prod_domain',
        'short_name',
        'stopupdate',
        'long_name',
        'onboarding_status',
        'company_url',
        'logo',
        'favicon',
        'branch',
        'color1',
        'color2',
        'color3',
        'lang',
        'tariff',
        'pp_target',
        'status',
        'demo_ends_at',
        'comment'
    ];

    /**
     * Заполняем языки по умолчанию
     */
    protected $attributes = [
        'lang' => '{"en": true, "es": true, "ru": true}',
    ];

    public function getShortNameAttribute()
    {
        return $this->short_name ?? explode('.', $this->tech_domain)[0];
    }

    public function getPpDomainAttribute()
    {
        if (env('APP_ENV') === 'local') {
            if ($this->tech_domain === config('app.domain')) {
                return config('app.domain');
            }
            return explode('.', $this->tech_domain)[0] .  '.' . config('app.domain');
        }
        return $this->prod_domain ?? $this->tech_domain ?? null;
    }

    public function getPpTargetTextAttribute(): ?string
    {
        return PpTargetList::getList()[$this->pp_target] ?? null;
    }

    public function getTariffTextAttribute(): ?string
    {
        return PpTariffList::getList()[$this->tariff] ?? null;
    }

    public function getOnboardingStatusTextAttribute()
    {
        return PpOnboardingList::getList()[$this->onboarding_status] ?? $this->onboarding_status;
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany */
    public function users()
    {
        return $this->hasMany(User::class, 'pp_id', 'id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsToMany */
    public function pay_methods()
    {
        return $this->belongsToMany(PayMethod::class, 'pp_pay_methods');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany */
    public function offers()
    {
        return $this->hasMany(Offer::class, 'pp_id', 'id');
    }

    /** @return string */
    public function getPpOwnerAttribute(): ?string
    {
        if (empty($this->id)) {
            return null;
        }
        $user = User::query()
            ->where('id', '=', $this->user_id)
            ->firstOrFail();
        $current_role = auth()->user()->role;
        if ($current_role === 'manager') {
            return view('components.user.owner_link', ['user' => $user])->render();
        } else {
            return null;
        }
    }
}
