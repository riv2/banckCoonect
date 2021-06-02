<?php

namespace App\Models;

use App\Exceptions\PixelLogException;
use App\Filters\ScopeFilter;
use App\Models\Traits\HasPpId;
use App\Models\Click;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

/**
 * App\Models\PixelLog
 *
 * @property int $id
 * @property int $pp_id
 * @property array $data
 * @property string|null $ip
 * @property bool|null $is_valid
 * @property bool|null $is_click
 * @property bool|null $is_order
 * @property string|null $parsed_client_id
 * @property int|null $parsed_partner_id
 * @property int|null $parsed_link_id
 * @property string|null $parsed_order_id
 * @property string|null $parsed_click_id
 * @property string|null $parsed_web_id
 * @property int|null $saved_clicks_id
 * @property string|null $status
 * @property \Jenssegers\Date\Date|null $created_at
 * @property-read Click|null $click
 * @property-read \App\Models\Client|null $client
 * @property-read string|null $browser_icon
 * @property-read string|null $event_text
 * @property-read \Torann\GeoIP\GeoIP|\Torann\GeoIP\Location $geo_ip
 * @property-read string|null $tr_class
 * @property-read \App\Models\Link|null $link
 * @property-read \App\Models\Order|null $order
 * @property-read User|null $partner
 * @property-read \App\Models\Pp $pp
 * @method static Builder|PixelLog filter(\App\Filters\QueryFilter $filters)
 * @method static Builder|PixelLog newModelQuery()
 * @method static Builder|PixelLog newQuery()
 * @method static Builder|PixelLog query()
 * @mixin \Eloquent
 */
class PixelLog extends Model
{
    use HasPpId;
    use ScopeFilter;

    /**
     * Поле updated_at отсутствует в таблице, выключаем его
     */
    public const UPDATED_AT = null;

    protected const PIXEL_EVENT_PURCHASE = 'purchase';

    protected const PIXEL_EVENT_PAGELOAD = 'pageload';

    public static $event_names = [
        self::PIXEL_EVENT_PURCHASE => 'Переход',
        self::PIXEL_EVENT_PAGELOAD => 'Покупка',
    ];

    /**
     * Таблица называется не pixel_logs, поэтому переопределяем
     *
     * @var string
     */
    protected $table = 'pixel_log';

    protected $casts = [
        'pp_id' => 'int',
        'data' => 'json',
        'is_valid' => 'boolean',
        'is_click' => 'boolean',
        'is_order' => 'boolean',
        'parsed_partner_id' => 'int',
        'parsed_link_id' => 'int',
        'saved_offer_id' => 'int',
        'saved_clicks_id' => 'int',
    ];

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 50;

    /** @return void */
    public static function booted()
    {
        static::creating(function (PixelLog $pixel_log) {
            static::convertSubJson($pixel_log, 'ed');
            static::convertSubJson($pixel_log, 'dataLayer');
        });
        static::updating(function (PixelLog $pixel_log) {
            static::convertSubJson($pixel_log, 'ed');
            static::convertSubJson($pixel_log, 'dataLayer');
        });
    }

    /******************************************************************************/
    /******************************************************************************/
    /******************************************************************************/

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class, 'parsed_client_id')->where('pp_id', '=', $this->pp_id);
    }

    public function click(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Click::class, 'pixel_log_id')->where('pp_id', '=', $this->pp_id);
    }

    public function partner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'parsed_partner_id')->where('pp_id', '=', $this->pp_id)->where('role', '=', 'partner');
    }

    public function link(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        // Должно быть совпадение по двум полям
        return $this->belongsTo(Link::class, 'parsed_link_id')
            ->where('pp_id', '=', $this->pp_id)
            ->whereHas('partner', function (Builder $query) {
                $query->where('partner_id', '=', $this->parsed_partner_id);
            });
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'parsed_order_id', 'order_id')
            ->where('pp_id', '=', $this->pp_id)
            ->whereHas('offer', function (Builder $query) {
                return $query->when($this->link, function (Builder $query) {
                    return $query->withTrashed()->where('id', '=', $this->link->id);
                });
            });
    }

    /******************************************************************************/
    /******************************************************************************/
    /******************************************************************************/

    /**
     * Возвращает client_id из данных пикселя
     *
     * @return string|null
     */
    public function parseClientIdFromOpenPixel(): ?string
    {
        return !empty($this->data['uid']) && is_string($this->data['uid']) ? (string) $this->data['uid'] : null;
    }

    /**
     * Возвращает partner_id из куки в пикселе
     *
     * @return string|null
     */
    public function parsePartnerIdFromOpenPixel(): ?int
    {
        return !empty($this->data['utm_content']) && is_numeric($this->data['utm_content']) ? (int) $this->data['utm_content'] : null;
    }

    /**
     * Возвращает link_id из куки в пикселе
     *
     * @return string|null
     */
    public function parseLinkIdFromOpenPixel(): ?int
    {
        return !empty($this->data['utm_campaign']) && is_numeric($this->data['utm_campaign']) ? (int) $this->data['utm_campaign'] : null;
    }

    /**
     * Проверяет id партнера, пришедший из пикселя.
     * Указан ли партнер
     * Есть ли этот партнер в данной pp
     *
     * @throws PixelLogException
     * @return integer
     */
    public function parseAndValidatePartnerId(): int
    {
        $parsed_partner_id = $this->parsePartnerIdFromOpenPixel();

        if (is_null($parsed_partner_id)) {
            throw new PixelLogException('partner_id не задан (или не число)');
        }

        $user_id_to_pp_id = Cache::remember('user_id_to_pp_id', now()->addMinutes(1), fn () => User::partner()->pluck('pp_id', 'id'));
        if (!isset($user_id_to_pp_id[$parsed_partner_id])) {
            throw new PixelLogException(sprintf('partner_id #%d не найден в системе', $parsed_partner_id));
        }

        if ($user_id_to_pp_id[$parsed_partner_id] !== $this->pp_id) {
            throw new PixelLogException(sprintf('partner_id #%d найден в системе, но принадлежит не pp_id#%d, а pp_id#%d', $parsed_partner_id, $this->pp_id, $user_id_to_pp_id[$parsed_partner_id]));
        }

        return $parsed_partner_id;
    }

    /**
     * Проверяет id ссылки, пришедший из пикселя.
     * Проверяет, что
     *
     * @throws PixelLogException
     * @return integer
     */
    public function parseAndValidateLinkId(): int
    {
        if (is_null($this->parsed_partner_id)) {
            throw new PixelLogException('partner_id не задан (или не число)');
        }

        $parsed_link_id = $this->parseLinkIdFromOpenPixel();
        if (is_null($parsed_link_id)) {
            throw new PixelLogException('link_id не задан (или не число)');
        }

        $link_id_to_user_id = Cache::remember('link_id_to_user_id', now()->addMinutes(1), fn () => Link::pluck('partner_id', 'id'));
        if (!isset($link_id_to_user_id[$parsed_link_id])) {
            throw new PixelLogException(sprintf('link_id #%d не найден в системе', $parsed_link_id));
        }

        if ($link_id_to_user_id[$parsed_link_id] !== $this->parsed_partner_id) {
            throw new PixelLogException(sprintf('link_id #%d найден в системе, но принадлежит не partner_id#%d, а partner_id#%d', $parsed_link_id, $this->parsed_partner_id, $link_id_to_user_id[$parsed_link_id]));
        }

        return $parsed_link_id;
    }

    /**
     * Возвращает offer_id, если указан parsed_link_id
     *
     * @throws PixelLogException
     * @return integer
     */
    public function parseAndValidateOfferId(): int
    {
        if (is_null($this->parsed_link_id)) {
            throw new PixelLogException('link_id не задан (или не число)');
        }

        $link_id_to_offer_id = Cache::remember('link_id_to_offer_id', now()->addMinutes(1), fn () => Link::pluck('offer_id', 'id'));
        if (!isset($link_id_to_offer_id[$this->parsed_link_id])) {
            throw new PixelLogException(sprintf('link_id #%d найдена в системе, но оффер у нее не задан', $this->parsed_link_id));
        }

        return $link_id_to_offer_id[$this->parsed_link_id];
    }

    /**
     * Возвращает click_id из данных пикселя
     *
     * @return string|null
     */
    public function parseClickIdFromOpenPixel(): ?string
    {
        return !empty($this->data['click_id']) && is_string($this->data['click_id']) ? (string) $this->data['click_id'] : null;
    }

    /**
     * Возвращает web_id из данных пикселя
     *
     * @return string|null
     */
    public function parseWebIdFromOpenPixel(): ?string
    {
        return !empty($this->data['utm_term']) && is_string($this->data['utm_term']) ? (string) $this->data['utm_term'] : null;
    }

    /**
     * Возвращает название события из данных пикселя
     *
     * @return string|null
     */
    public function parseEventNameFromOpenPixel(): ?string
    {
        return !empty($this->data['ev']) && is_string($this->data['ev']) ? (string) $this->data['ev'] : null;
    }

    /**
     * Возвращает массив с данными пользователя
     *
     * @return array|null
     */
    public function parseEventDataFromOpenPixel(): ?array
    {
        return !empty($this->data['ed']) && is_array($this->data['ed']) ? $this->data['ed'] : null;
    }

    /**
     * Возвращает id заказа, если это заказ
     *
     * @throws PixelLogException
     * @return string|null
     */
    public function parseOrderIdFromOpenPixel(): ?string
    {
        $event = $this->parseEventNameFromOpenPixel();
        if (is_null($event)) {
            return null;
        }

        if ($event === self::PIXEL_EVENT_PURCHASE) {
            $event_data = $this->parseEventDataFromOpenPixel();
            if (empty($event_data['order_id'])) {
                throw new PixelLogException('Получено событие purchase, но не передан обязательный параметр order_id');
            }

            return (string) $event_data['order_id'];
        } elseif ($event === self::PIXEL_EVENT_PAGELOAD && !empty($this->data['dataLayer'])) {
            // Если это событие "загрузка страницы"
            // Пробуем поискать по dataLayer, а вдруг это страница "спасибо за заказ"

            // Получаем все подходящие данные из dataLayer
            // Их может быть несколько, берем первый подходящий
            return array_values(array_filter(data_get($this->data['dataLayer'], '*.ecommerce.purchase.actionField.id')))[0] ?? null;
        }
        return null;
    }

    /**
     * Получает массив со списком продуктов для данного заказа
     *
     * @return array|null
     */
    public function parseProductsFromOpenPixel(): ?array
    {
        if ($this->is_order !== true) {
            throw new PixelLogException('Попытка получения продуктов для события, не являющегося заказом!');
        }

        $pp_id_to_pp_target = Cache::remember('pp_id_to_pp_target', now()->addHours(1), fn () => Pp::pluck('pp_target', 'id'));

        if ($pp_id_to_pp_target[$this->pp_id] === 'products') {
            $products = array_values(array_filter(data_get($this->data['dataLayer'], '*.ecommerce.purchase.products')))[0] ?? null;

            // Костыль: в пп моймир заказ в один клик не передает продукты. Пытаемся вытащить из оформления заказа (checkout)
            if (config('app.gocpa_project') === 'cloud' && $this->pp_id === 79 && empty($products)) {
                return array_values(array_filter(data_get($this->data['dataLayer'], '*.ecommerce.checkout.products')))[0] ?? null;
            }

            return $products;
        }

        return null;
    }

    /**
     * Является ли текущая запись переходом по партнерской ссылке
     *
     * @return boolean
     */
    public function isClickedLink(): bool
    {
        $document_location = !empty($this->data['dl']) && is_string($this->data['dl']) ? (string) $this->data['dl'] : null;
        if (is_null($document_location)) {
            throw new PixelLogException('Отсутствует параметр dl, не могу определить, клик ли это');
        }

        $query = parse_url($document_location, PHP_URL_QUERY);
        $url_params = [];
        parse_str($query, $url_params);

        /** @todo Переписать, учитывая link_template из оффера */
        $validator = Validator::make($url_params, [
            'utm_medium' => 'required|string|in:cpa',
            'utm_source' => 'required|string|in:partners,pimpay',
            'utm_campaign' => 'required|numeric',
            'utm_content' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return false;
        }
        return true;
    }

    /******************************************************************************/
    /******************************************************************************/
    /******************************************************************************/

    /** @return string|null */
    public function getEventTextAttribute(): ?string
    {
        $event_name = $this->parseEventNameFromOpenPixel();
        return self::$event_names[$event_name] ?? $event_name;
    }

    /** @return string|null */
    public function getTrClassAttribute(): ?string
    {
        if ($this->is_order) {
            return 'table-success';
        }
        if ($this->is_click) {
            return 'table-warning';
        }
        if ($this->is_valid) {
            return 'table-info';
        }
        return null;
    }

    /** @return string|null */
    public function getBrowserIconAttribute(): ?string
    {
        if (!isset($this->data['bn'])) {
            return null;
        }
        $browser_name = strtok($this->data['bn'], " ");
        $icons = [
            'Chrome' => '<i class="fab fa-chrome"></i>',
            'Safari' => '<i class="fab fa-safari"></i>',
            'Firefox' => '<i class="fab fa-firefox"></i>',
            'Opera' => '<i class="fab fa-opera"></i>',
            'Edge' => '<i class="fab fa-edge"></i>',
        ];

        return $icons[$browser_name] ?? null;
    }

    /**
     * Пиксель присылает все данные, как текст
     * Иногда в этом тексте может быть json
     *
     * @param PixelLog $pixel_log
     * @param string $attr_name
     * @return void
     */
    public static function convertSubJson(PixelLog &$pixel_log, string $attr_name)
    {
        // Или отсутствует поле
        // Или поле уже преобразовано
        if (!isset($pixel_log->data[$attr_name]) || is_array($pixel_log->data[$attr_name])) {
            return;
        }

        // laravel запрещает напрямую редактировать поля в json
        $data = $pixel_log->data;
        // Преобразуем строку с JSON о покупке
        $data[$attr_name] = json_decode($data[$attr_name], true, 512, JSON_THROW_ON_ERROR);
        // Если нет ошибок - сохраняем в модели
        $pixel_log->data = $data;
    }
}
