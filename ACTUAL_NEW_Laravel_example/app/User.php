<?php

namespace App;

use App\Filters\ScopeFilter;
use App\Helpers\PartnerProgramStorage;
use App\Lists\TaxationSystemList;
use App\Models\Link;
use App\Models\Order;
use App\Models\PartnerPayment;
use App\Models\PayMethod;
use App\Models\Pp;
use App\Models\UsersPayMethod;
use App\Role\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Lab404\Impersonate\Models\Impersonate;

/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property int|null $pp_id
 * @property string|null $hash_name
 * @property \Jenssegers\Date\Date|null $email_verified_at
 * @property string $password
 * @property int $status
 * @property bool $need_api
 * @property string|null $comment
 * @property string|null $company
 * @property string|null $contract_number
 * @property \datetime|null $contract_date
 * @property string|null $phone
 * @property string|null $skype
 * @property bool $all_docs
 * @property bool $all_fields
 * @property bool $balance_popup
 * @property bool $email_unsubs
 * @property string $model
 * @property string|null $remember_token
 * @property string|null $auth_token
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property-read string|false $impersonate_link
 * @property-read bool|\Illuminate\View\View $impersonate_link_button
 * @property-read string $onboarding_status_text
 * @property-read PayMethod $pay_method
 * @property-read string|null $pay_method_text
 * @property-read string|null $pp_domain
 * @property-read string|null $pp_name
 * @property-read string $ref_link
 * @property-read string|null $unsubscribe_link
 * @property-read string $view_link
 * @property-read Collection|Link[] $links
 * @property-read int|null $links_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read Collection|PayMethod[] $pay_methods
 * @property-read int|null $pay_methods_count
 * @property-read Pp|null $pp
 * @property-read User $referrer
 * @method static Builder|User active()
 * @method static Builder|User advertiser()
 * @method static Builder|User filter(\App\Filters\QueryFilter $filters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User partner()
 * @method static Builder|User query()
 * @mixin \Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use Impersonate;
    use ScopeFilter;

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'hash',
        'auth_token',
    ];

    protected $fillable = [
        'name',
        'email',
        'role',
        'pp_id',
        'password',
        'status',
        'contract_number',
        'contract_date',
        'email_unsubs',
        'phone',
        'skype',
        'hash_name',
    ];

    /** @var array */
    protected $casts = [
        'pp_id' => 'integer',
        'email_verified_at' => 'datetime',
        'status' => 'integer',
        'has_many_accounts' => 'integer',
        'max_traffic_link_nosite' => 'boolean',
        'is_callcenter' => 'boolean',
        'need_api' => 'boolean',
        'cat' => 'integer',
        'contract_date' => 'datetime:Y-m-d',
        'all_docs' => 'boolean',
        'all_fields' => 'boolean',
        'flag_need_acts' => 'boolean',
        'balance_popup' => 'boolean',
        'email_unsubs' => 'boolean',
        'email_disabled' => 'boolean',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /** @var array */
    protected $appends = [];

    /** @var array */
    public static array $fields = [];

    /** @var array */
    public static array $pay_method_fields = [
        1 => [
            'company_name' => [
                'type' => 'text',
            ],
            'company_inn' => [
                'type' => 'text',
            ],
            'bank_company_account' => [
                'type' => 'text',
            ],
            'bank_identifier_code' => [
                'type' => 'text',
            ],
            'bank_beneficiary' => [
                'type' => 'text',
            ],
            'bank_correspondent_account' => [
                'type' => 'text',
            ],
            'contract_number' => [
                'type' => 'text',
            ],
            'contract_date' => [
                'type' => 'date',
            ],
            'vat_tax' => [
                'type' => 'select',
                'options' => [
                    '1' => 'с НДС',
                    '0' => 'без НДС',
                ],
            ],
            'taxation_system' => [
                'type' => 'select',
                'options' => [],
            ],
        ],
        2 => [
            'cc_number' => [
                'type' => 'cc-number',
            ],
        ],
        3 => [
            'webmoney_number' => [
                'type' => 'text',
            ],
        ],
    ];

    public bool $registered_by_advert = false;

    public function __construct(array $attributes = [])
    {
        self::$fields = [
            'name' => [
                'type' => 'text',
                'required' => true,
            ],
            'phone' => [
                'type' => 'tel',
                'required' => false,
            ],
            'skype' => [
                'type' => 'text',
                'required' => false,
            ],
        ];
        self::$pay_method_fields[1]['taxation_system']['options'] = TaxationSystemList::getList();
        parent::__construct($attributes);
    }

    /** @return void */
    public static function booted()
    {
        self::$fields['role']['values'] = UserRole::getRoleList();

        static::creating(function ($user) {
            $user->pp_id = PartnerProgramStorage::getPP()->id ?? $user->pp_id;
        });
    }

    /** @return bool */
    public function canBlockUsers()
    {
        return $this->hasRole(['manager']);
    }

    /** @return bool */
    public function canImpersonate()
    {
        return !$this->isImpersonated() && auth()->user()->hasRole(['manager', 'advertiser']);
    }

    /** @return bool */
    public function canBeImpersonated()
    {
        /** @todo корректная проверка */
        $can_impersonate = $this->canImpersonate();
        return $can_impersonate;
    }

    /**
     * @return string|false
     */
    public function getImpersonateLinkAttribute(): string
    {
        if (!$this->canBeImpersonated()) {
            return false;
        }

        if (auth()->user()->getRole() == 'manager') {
            // Авторизация из под менеджера
            $prefx = (request()->isSecure()) ? 'https://' : 'http://';
            $result = $prefx . $this->getPpDomainAttribute();
            $result .= URL::SignedRoute('login_as_id', ['user' => $this->id], null, false);

            return $result;
        }
        return route('impersonate', ['id' => $this->id]);
    }

    /**
     * @return bool|\Illuminate\View\View
     */
    public function getImpersonateLinkButtonAttribute()
    {
        if (!$route = $this->getImpersonateLinkAttribute()) {
            return false;
        }

        return view('components.user.impersonate_link_button')->with(['route' => $route]);
    }

    /** @return string|null */
    public function getPpNameAttribute(): ?string
    {
        if (empty($this->pp)) {
            return null;
        }

        return $this->pp->short_name ?? null;
    }

    /** @return string|null */
    public function getPpDomainAttribute()
    {
        if (empty($this->pp)) {
            return null;
        }
        if (config('app.gocpa_project') === 'cpadroid') {
            return str_replace('https://', '', env('APP_URL'));
        }

        return $this->pp->pp_domain ?? null;
    }

    /** @return string */
    public function getViewLinkAttribute(): ?string
    {
        $current_role = auth()->user()->role;
        if ($current_role === 'manager' || $current_role === 'advertiser') {
            return view('components.user.view_link', ['user' => $this])->render();
        } else {
            return null;
        }
    }

    /** @return string */
    public function getRefLinkAttribute()
    {
        // return $this->ref_link = route('index', ['ref' => $this->id]);
        return '';
    }

    /** @return string|null */
    public function getPayMethodTextAttribute()
    {
        throw new \Exception('deprecated');
    }

    /** @return string */
    public function getOnboardingStatusTextAttribute()
    {
        return $this->pp->onboarding_status_text ?? __('user.unknown-status');
    }

    /** @return string|null */
    public function getUnsubscribeLinkAttribute(): string
    {
        return URL::signedRoute('unsubscribe', ['email' => $this->routeNotificationFor('mail')]);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ref_id', 'id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'partner_id', 'id')->orderBy('datetime', 'desc');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany */
    public function partner_payments(): HasMany
    {
        return $this->hasMany(PartnerPayment::class, 'partner_id', 'id')->orderBy('updated_at', 'desc');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany */
    public function links(): HasMany
    {
        return $this->hasMany(Link::class, 'partner_id', 'id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo */
    public function pp(): ?BelongsTo
    {
        return $this->belongsTo(Pp::class, 'pp_id', 'id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsToMany */
    public function pay_methods(): BelongsToMany
    {
        return $this->belongsToMany(PayMethod::class, 'users_pay_methods')
            ->whereNull('users_pay_methods.deleted_at')
            ->withPivot([
                'id',
                'cc_type',
                'cc_number',
                'company_name',
                'company_inn',
                'contract_number',
                'contract_date',
                'bank_company_account',
                'bank_identifier_code',
                'bank_beneficiary',
                'bank_correspondent_account',
                'webmoney_number',
                'vat_tax',
                'taxation_system',
                'created_at',
                'updated_at',
            ]);
    }

    public function pay_method()
    {
        return $this->hasOne(UsersPayMethod::class, 'user_id', 'id')->whereNull('deleted_at');
    }

    /**
     * Область видимости, только рекламодатели
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeAdvertiser(Builder $query): Builder
    {
        return $query->where('role', '=', 'advertiser');
    }

    /**
     * Область видимости, только партнеры
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePartner(Builder $query): Builder
    {
        return $query->where('role', '=', 'partner');
    }

    /**
     * Область видимости, только активные юзеры
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * @param string|array $role
     * @return boolean
     */
    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->getRole(), $role);
        }
        return $role === $this->getRole();
    }

    /**
     * @param string $role
     * @return $this
     */
    public function setRole(string $role)
    {
        $this->setAttribute('role', $role);
        return $this;
    }

    /** @return string */
    public function getRole()
    {
        return $this->role;
    }

    public function sendEmailVerificationNotification()
    {
        // Тут не отправляем письмо, отправка происходит в контроллере регистраций
        // $this->notify(new VerifyEmail());
    }

    public function getMainPageUrl(): string
    {
        return "/" . $this->role . "/report";
    }

    /**
     * Получить партнёров для этого адверта
     * @param \App\User $user
     * @return \Illuminate\Database\Eloquent\Collection<\App\User>
     */
    public static function getPartners(User $user): ?Collection
    {
        $q = static::query();
        if ($user->role == "advertiser") {
            return $q->where("pp_id", "=", $user->pp_id)->where("role", "=", "partner")->get();
        } elseif ($user->role == "analyst") {
            return $q->where("pp_id", "=", $user->pp_id)->where("role", "=", "partner")->get();
        } elseif ($user->role == "manager") {
            return $q->where("role", "=", "partner")->get();
        } else {
            /**
             * @todo Доделать получение партнёров или чего-то ещё
             */
            return null;
//            return $q->join("links", "links.partner_id", "=", "pp.id")
//                ->where("links.partner_id", "=", $user->id)->get();
        }
    }
}
