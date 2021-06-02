<?php

namespace App\Models;

use App\Models\Traits\HasPpId;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Link
 *
 * @property int $id
 * @property int $pp_id
 * @property int $partner_id
 * @property string $link_name
 * @property string $link
 * @property int|null $link_source
 * @property int|null $offer_id
 * @property int|null $offer_materials_id
 * @property string $status
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Click[] $clicks
 * @property-read int|null $clicks_count
 * @property-read \App\Models\Offer|null $offer
 * @property-read \App\Models\OfferMaterial|null $offersMaterial
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders_sales
 * @property-read int|null $orders_sales_count
 * @property-read User $partner
 * @property-read \App\Models\Pp $pp
 * @method static Builder|Link active()
 * @method static Builder|Link allowed()
 * @method static Builder|Link newModelQuery()
 * @method static Builder|Link newQuery()
 * @method static Builder|Link query()
 * @method static Builder|Link withDeleted()
 * @mixin \Eloquent
 */
class Link extends Model
{
    use HasPpId;

    protected $table = 'links';
    protected $primaryKey = 'id';

    protected $casts = [
        'pp_id' => 'int',
        'partner_id' => 'int',
        'offer_id' => 'int',
        'offer_materials_id' => 'int',
        'has_macros' => 'boolean',
    ];

    /** @return void */
    protected static function booted(): void
    {
        // Генерируем ссылку при создании
        static::created(function (Link $link) {
            try {
                $link->has_macros = NotifyParam::where('partner_id', '=', $link->partner->id)->exists();
                $link->link = $link->generateLink();
                $link->save();
            } catch (\Throwable $th) {
                $link->forceDelete();
                throw $th;
            }
        });
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo */
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id', 'id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo */
    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id', 'id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo */
    public function offersMaterial()
    {
        return $this->belongsTo(OfferMaterial::class, 'offer_materials_id', 'offer_material_id')->withTrashed();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany */
    public function clicks()
    {
        return $this->hasMany(Click::class, 'link_id', 'id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany */
    public function orders()
    {
        return $this->hasMany(Order::class, 'link_id', 'id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany */
    public function orders_sales()
    {
        return $this->orders()->sales();
    }

    /**
     * Scope для показа элементов по ролям
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllowed(Builder $query): Builder
    {
        $user = auth()->user();
        return $query
            ->when($user && $user->role === 'partner', function (Builder $query) use ($user) {
                $query->where('partner_id', '=', $user->id);
            });
    }

    /**
     * Scope для показа элементов по ролям
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('status', '=', 'ACTIVE');
    }

    /**
     * Scope для показа элементов по ролям
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithDeleted(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Генерирует ссылку в зависимости от текущих данных
     *
     * @throws Exception
     * @return string
     */
    public function generateLink(): string
    {
        if (is_null($this->partner)) {
            throw new Exception('Ошибка при генерации ссылки #' . $this->id . ': не найден партнер "' . $this->partner_id . '"!');
        }

        if (is_null($this->offer)) {
            throw new Exception('Ошибка при генерации ссылки #' . $this->id . ': не найден оффер "' . $this->offer_id . '"!');
        }

        if (is_null($this->offersMaterial)) {
            throw new Exception('Ошибка при генерации ссылки #' . $this->id . ': не найден материал "' . $this->offer_materials_id . '"!');
        }

        if ($this->offersMaterial->offer_id != $this->offer_id) {
            throw new Exception('Ошибка при генерации ссылки #' . $this->id . ': не совпадает offer_id "' . $this->offer_id . '" в материале "' . $this->offersMaterial->offer_id . '"!');
        }

        switch ($this->offersMaterial->material_type) {
            case 'landing':
                return $this->generateLinkForLanding();
            case 'xmlfeed':
                return $this->generateLinkForXmlfeed();
            case 'link':
                return $this->generateLinkForLink();
            case 'pwa':
                return $this->generateLinkForPwa();
            default:
                throw new Exception('Ошибка при генерации ссылки #' . $this->id . ': неожиданный тип материала "' . $this->offersMaterial->material_type . '"');
        }
    }

    /**
     * Генерирует адрес ссылки с метками для выбранного лендинга
     *
     * @return string
     */
    protected function generateLinkForLanding(): string
    {
        $landing_url = $this->offersMaterial->material_params['link'];
        if (empty($landing_url)) {
            throw new Exception('Не могу создать ссылку, так как landing_url у оффера ' . $this->offer->id . ' и материала ' . $this->offer_materials_id . ' пустое!');
        }

        return $this->generateUrlWithTemplate($landing_url, true);
    }

    /**
     * Генерирует URL для XML-фида
     *
     * @return string
     */
    protected function generateLinkForXmlfeed(): string
    {
        return 'https://' . ($this->offer->pp->prod_domain ?? $this->offer->pp->tech_domain) . '/feeds/' . md5('GoCPA' . $this->id . 'Th3Be$$t') . '.xml';
    }

    /**
     * Генерирует адрес ссылки с метками для введенного URL
     *
     * @return string
     */
    protected function generateLinkForLink(): string
    {
        $url = request()->get('url');

        if (empty($url) || strpos($url, $this->offersMaterial->material_params['link']) !== 0) {
            return redirect('/partner/offers/' . $this->offersMaterial->offer_id)
                ->withErrors('Вы ввели ссылку, которая не совпадает с адресом в оффере!');
        }

        return $this->generateUrlWithTemplate($url, true);
    }

    /**
     * Генерирует PWA
     *
     * @todo Переписать, чтобы данные сохранялись и выводились
     *
     * @return string
     */
    protected function generateLinkForPwa()
    {
        $script = $this->offersMaterial->material_params['script'] ?? '<script></script>';
        $script = str_replace('%partner_id%', (string) $this->partner_id, $script);
        $script = str_replace('%offer_id%', (string) $this->offer_id, $script);

        $tmp = [

            'name' => $this->offer->offer_name,
            'short_name' => $this->offer->offer_name,
            'description' => $this->offer->description,
            'Scope' => './',
            'start_url' => '/',
            'icons' => [
                [
                    'src' => $this->offer->getMeta('icon192'),
                    'sizes' => '192x192',
                    'type' => 'image/png'
                ],
                [
                    'src' => $this->offer->getMeta('icon512'),
                    'sizes' => '512x512',
                    'type' => 'image/png'
                ]
            ],
            'theme_color' => '#ffffff',
            'background_color' => '#ffffff',
            'display' => 'minimal-ui'

        ];

        $manifest = json_encode($tmp, JSON_UNESCAPED_UNICODE, JSON_PRETTY_PRINT);
        $fname = 'materials/pwa/' . $this->offer->id . '-' . auth()->id() . '.json';
        MultiStorage::put($fname, $manifest);

        $file = 'Скрипт: {$script}\n\nМанифест: ' . asset($fname);
        header('Content-Description: File Transfer');
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename=pwa.txt');
        header('Content-Transfer-Encoding: binary');

        /** @todo А зачем это тут выводится? */
        echo $file;
        die();
    }

    /**
     * Добавляет (и заменяет) UTM-метки по шаблону
     *
     * @param string $base_url URL, к которому необходимо добавить метки
     * @param bool $save_base_query_string Нужно ли оставлять старый QUERY_STRING
     * @return string Сгенерированный URL
     */
    public function generateUrlWithTemplate(string $base_url, bool $save_base_query_string = false): string
    {
        $link_template = $this->offer->link_template;

        /** @var array Ассоциативный массив, содержащий все компоненты базового URL */
        $url_parsed = parse_url($base_url);

        /** @var array Массив с разбитым query_string базового URL */
        $base_url_query = [];
        if ($save_base_query_string === true) {
            parse_str(($url_parsed['query'] ?? ''), $base_url_query);
        }

        /** @var array Массив с разбитым query_string шаблона */
        $link_template_query = [];
        parse_str($link_template, $link_template_query);

        // Объединяем query из ссылки и link_template
        $url_parsed['query'] = array_merge($base_url_query, $link_template_query);

        if (!$this->has_macros) {
            // Удаляем параметры совпадающие с макросами
            foreach (['{WEB_ID}', '{CLICK_ID}'] as $del_val) {
                if (($key = array_search($del_val, $url_parsed['query'])) !== false) {
                    unset($url_parsed['query'][$key]);
                }
            }

            // У CPADroid формируется строка, нужно заменить на нолики
            if (config('app.gocpa_project') == 'cpadroid') {
                $url_parsed['query']['utm_campaign'] = str_replace(['{WEB_ID}', '{CLICK_ID}'], ['0', '0'], $url_parsed['query']['utm_campaign']);
            }
        }

        // Преобразуем из массива обратно в строку
        $url_parsed['query'] = http_build_query($url_parsed['query']);
        $result = self::http_build_url($url_parsed);

        // У нас в ссылке могут быть переменные, заменяем их
        // Так как по правилам формирования ссылок фигурные скобки должны быть закодированы
        // Поэтому ключи пропускаем через urlencode
        // Сразу не стал кодировать, чтобы можно было легко найти эти строки в проекте
        // Можно было бы раскодировать всю строку, но я думаю, что могут быть случаи
        // Когда исходный URL содержит символы, которые должны кодироваться по стандарту
        $replaces = [
            urlencode('{$link_id}') => $this->id,
            urlencode('{$partner_id}') => $this->partner->id,
            urlencode('{$partner_hash_name}') => $this->partner->hash_name,
            urlencode('{WEB_ID}') => '{WEB_ID}',
            urlencode('{CLICK_ID}') => '{CLICK_ID}',
        ];
        $result = str_replace(array_keys($replaces), array_values($replaces), $result);

        return $result;
    }

    /**
     * Формирует ссылку из массива выданного функцией parse_url
     *
     * @param array $parsed_url
     * @return string
     */
    public static function http_build_url(array $parsed_url): string
    {
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        return $scheme . $host . $path . $query;
    }
}
