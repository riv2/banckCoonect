<?php

namespace App\Models;

use App\Helpers\PartnerProgramStorage;
use App\Models\Traits\HasPpId;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;

/**
 * App\Models\News
 *
 * @property int $id
 * @property int|null $pp_id
 * @property string|null $news_title
 * @property string|null $news_text
 * @property string|null $send_to
 * @property string|null $send_to_value
 * @property \Jenssegers\Date\Date|null $deleted_at
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $emailRecipients
 * @property-read int|null $email_recipients_count
 * @property-read string $news_text_parsed
 * @property-read mixed $send_to_value_parsed
 * @property-read string $view_link
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $notReaded
 * @property-read int|null $not_readed_count
 * @property-read \App\Models\Pp|null $pp
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $readed
 * @property-read int|null $readed_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $recipients
 * @property-read int|null $recipients_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $sending
 * @property-read int|null $sending_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $sent
 * @property-read int|null $sent_count
 * @method static Builder|News newModelQuery()
 * @method static Builder|News newQuery()
 * @method static \Illuminate\Database\Query\Builder|News onlyTrashed()
 * @method static Builder|News query()
 * @method static \Illuminate\Database\Query\Builder|News withTrashed()
 * @method static \Illuminate\Database\Query\Builder|News withoutTrashed()
 * @mixin \Eloquent
 */
class News extends Model
{
    use SoftDeletes;
    use HasPpId;

    protected $table = 'news';

    protected $casts = [
        'pp_id' => 'int',
    ];

    protected $fillable = [
        'pp_id',
        'news_title',
        'news_text',
        'send_to',
        'send_to_value'
    ];

    const NEWS_SEND_TO_ALL_USERS = 'all';
    const NEWS_SEND_TO_USER_BY_CATEGORY = 'user_cats';
    const NEWS_SEND_TO_USER_BY_ONBRD = 'user_onbrd';
    const NEWS_SEND_TO_USER_BY_IDS = 'user_ids';
    const NEWS_SEND_TO_USER_BY_IDS_EXCLUDE = 'user_ids_exclude';
    const NEWS_SEND_TO_USER_BY_TAG = 'user_tag';

    public static $send_to_list = [
        self::NEWS_SEND_TO_ALL_USERS,
        self::NEWS_SEND_TO_USER_BY_CATEGORY,
        self::NEWS_SEND_TO_USER_BY_ONBRD,
        self::NEWS_SEND_TO_USER_BY_IDS,
        self::NEWS_SEND_TO_USER_BY_IDS_EXCLUDE,
        self::NEWS_SEND_TO_USER_BY_TAG,
    ];

    /** @return void */
    protected static function booted()
    {
        /**
         * При рассылке записываем в news_users
         */
        static::created(function (News $news) {
            $users = static::getRecipients($news)->get('id');
            $news->recipients()->attach($users);
        });
    }

    /**
     * Список получателей новости
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function recipients(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'news_users', 'news_id', 'user_id', 'id')
            ->withPivot(['readed_at', 'sended_at']);
    }

    /**
     * Список получателей новости, которые её еще не прочитали
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function notReaded(): BelongsToMany
    {
        return $this
            ->recipients()
            ->wherePivotNull('readed_at');
    }

    /**
     * Список получателей новости, которые её уже прочитали
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function readed(): BelongsToMany
    {
        return $this
            ->recipients()
            ->wherePivotNotNull('readed_at');
    }

    /**
     * Список получателей новости, которым разрешена отправка по e-mail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function emailRecipients(): BelongsToMany
    {
        return $this
            ->recipients()
            ->whereNotNull('users.email_verified_at')
            ->where('users.email_unsubs', '=', 0);
    }

    /**
     * Список получателей новости, которым разрешена отправка по e-mail и письмо еще не было отправлено
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sending(): BelongsToMany
    {
        return $this
            ->emailRecipients()
            ->wherePivotNull('sended_at');
    }

    /**
     * Список получателей новости, которым разрешена отправка по e-mail и письмо уже было отправлено
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sent(): BelongsToMany
    {
        return $this
            ->emailRecipients()
            ->wherePivotNotNull('sended_at');
    }

    /**
     * Подготавливает запрос для выдачи списка пользователей,
     * которые должны увидеть данную новость
     *
     * @param \App\Models\News $news
     * @return \Illuminate\Database\Eloquent\Builder|\App\User
     * @throws \Exception
     */
    public static function getRecipients(News $news): Builder
    {
        if (!in_array($news->send_to, self::$send_to_list, true)) {
            throw new \Exception('Ошибка при получении списка пользователей для новости!');
        }

        /** @var array Роли пользователей, которым разрешено отправлять новости */
        $allowed = ['manager', 'advertiser'];

        /** @var string */
        $sender_role = auth()->user()->role;
        if (!in_array($sender_role, $allowed)) {
            throw new \Exception('Непонятно кто делает рассылку новости!');
        }

        /** @var boolean */
        $is_manager = ($sender_role === 'manager');

        /** @var boolean */
        $is_advertiser = ($sender_role === 'advertiser');

        // Берем только подтвержденных юзеров
        return User::query()
            ->whereNotNull('email_verified_at')
            ->when($is_manager, function (Builder $query) {
                $query->where('role', '=', 'advertiser');
            })
            ->when($is_advertiser, function (Builder $query) {
                $query->where('role', '=', 'partner')
                    ->where('pp_id', '=', PartnerProgramStorage::getPP()->id);
            })
            ->when($news->send_to === self::NEWS_SEND_TO_USER_BY_CATEGORY, function (Builder $query) use ($news) {
                $query->where('cat', '=', $news->send_to_value);
            })
            ->when($news->send_to === self::NEWS_SEND_TO_USER_BY_ONBRD, function (Builder $query) use ($news) {
                $query->whereHas('pp', function (Builder $query) use ($news) {
                    $query->where('onboarding_status', '=', $news->send_to_value);
                });
            })
            ->when($news->send_to === self::NEWS_SEND_TO_USER_BY_IDS, function (Builder $query) use ($news) {
                $query->whereIn('id', $news->send_to_value_parsed);
            })
            ->when($news->send_to === self::NEWS_SEND_TO_USER_BY_IDS_EXCLUDE, function (Builder $query) use ($news) {
                $query->whereNotIn('id', $news->send_to_value_parsed);
            })
            ->when($news->send_to === self::NEWS_SEND_TO_USER_BY_TAG, function (Builder $query) use ($news) {
                $query->whereHas('tagsCurrent', function (Builder $query) use ($news) {
                    $query->where('tag_id', '=', $news->send_to_value);
                });
            });
    }

    public function getSendToValueParsedAttribute()
    {
        // Функция должна обработать только в этом случае, иначе выведем реальное положение дел
        $allowed = [self::NEWS_SEND_TO_USER_BY_IDS, self::NEWS_SEND_TO_USER_BY_IDS_EXCLUDE];
        if (!in_array($this->send_to, $allowed)) {
            return $this->attributes['send_to_value'];
        }

        $ids = $this->attributes['send_to_value'];
        $ids = str_replace(' ', '', $ids);
        $ids = explode(',', $ids);

        $result = [];
        foreach ($ids as $id) {
            $diap = explode('-', $id);
            if (count($diap) == 2) {
                // Это диапазон
                $result = array_merge($result, range($diap[0], $diap[1]));
            } elseif (is_numeric($id)) {
                // Это отдельный юзер
                $result[] = (int) $id;
            } else {
                throw new \Exception('Ошибка при отправке почты: указан некорректный диапазон id: ' . print_r($id, true), 1);
            }
        }
        $result = array_unique($result);
        sort($result);
        return $result;
    }

    /**
     * Выводим текст новости
     *
     * @return string
     */
    public function getNewsTextParsedAttribute()
    {
        return strip_tags($this->news_text, '<code><span><div><label><a><br><p><b><i>
            <del><strike><u><img><video><audio><iframe><object><embed><param>
            <blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup>
            <sub><big><pre><code><figure><figcaption><strong><em>
            <table><tr><td><th><tbody><thead><tfoot>
            <h1><h2><h3><h4><h5><h6>');
    }

    /**
     * Выводим ссылку на просмотр новости
     *
     * @return string
     */
    public function getViewLinkAttribute()
    {
        $route = auth()->user()->role . '.news.show';
        if (Route::has($route)) {
            return view('components.news.view_link')
                ->with(['route' => route($route, $this), 'newsTitle' => $this->news_title]);
        }
        return '';
    }
}
