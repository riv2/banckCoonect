<?php

namespace App\Models;

use App\Filters\ScopeFilter;
use App\Helpers\PartnerProgramStorage;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;

/**
 * App\Models\ServicedeskTask
 *
 * @property int $id
 * @property string|null $type
 * @property int $pp_id
 * @property int $creator_user_id
 * @property int|null $doer_user_id
 * @property string $subject
 * @property string|null $body
 * @property string $status
 * @property bool $not_closed
 * @property float|null $estimate_time
 * @property float|null $fact_time
 * @property \Jenssegers\Date\Date|null $deadline_at
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property \Jenssegers\Date\Date|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ServicedeskTaskComment[] $comments
 * @property-read int|null $comments_count
 * @property-read User $creator
 * @property-read User|null $doer
 * @property-read string $status_class
 * @property-read string $status_text
 * @property-read string $type_class
 * @property-read string $type_text
 * @property-read mixed $view_link
 * @method static Builder|ServicedeskTask filter(\App\Filters\QueryFilter $filters)
 * @method static Builder|ServicedeskTask newModelQuery()
 * @method static Builder|ServicedeskTask newQuery()
 * @method static \Illuminate\Database\Query\Builder|ServicedeskTask onlyTrashed()
 * @method static Builder|ServicedeskTask query()
 * @method static \Illuminate\Database\Query\Builder|ServicedeskTask withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ServicedeskTask withoutTrashed()
 * @mixin \Eloquent
 */
class ServicedeskTask extends Model
{
    use SoftDeletes;
    use ScopeFilter;

    /** @var string */
    protected $table = 'servicedesk_tasks';

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array */
    protected $casts = [
        'pp_id' => 'int',
        'creator_user_id' => 'int',
        'doer_user_id' => 'int',
        'not_closed' => 'bool',
        'estimate_time' => 'float',
        'fact_time' => 'float',
        'deadline_at' => 'datetime',
    ];

    /** @var array */
    public static array $task_types = [];

    /** @var array */
    public static array $statuses = [];

    /** @var array */
    public static $doers = [];

    public static $default_doer = null;

    public function getDefaultDoer()
    {
        return empty(static::$default_doer) ? PartnerProgramStorage::getAdminsIds()[0] : static::$default_doer;
    }

    public function __construct(array $attributes = [])
    {
        self::$task_types = [
            'commercial' => [
                'caption' => __('servicedeskTask.task_types.commercial'),
            ],
            'technical' => [
                'caption' => __('servicedeskTask.task_types.technical'),
            ],
            'payments' => [
                'caption' => __('servicedeskTask.task_types.payments'),
            ],
        ];
        self::$statuses = [
            'new' => [
                'caption' => __('servicedeskTask.statuses.new'),
                'class' => 'text-danger',
            ],
            'pending' => [
                'caption' => __('servicedeskTask.statuses.pending'),
                'class' => 'text-warning',
            ],
            'closed' => [
                'caption' => __('servicedeskTask.statuses.closed'),
                'class' => 'text-success',
            ],
        ];
        if (isset($attributes["doers"])) {
            static::$doers = $attributes["doers"];
            unset($attributes["doers"]);
            static::$default_doer = static::$doers[0];
        }
        parent::__construct($attributes);
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('pp', function (Builder $builder) {
            $builder->when(PartnerProgramStorage::getPP()->id ?? null, function (Builder $query, $pp_id) {
                $query->where('pp_id', '=', $pp_id);
            });
        });
        /**
         * При создании нового тикета записываем pp_id
         */
        static::creating(function (ServicedeskTask $task) {
            $task->pp_id = PartnerProgramStorage::getPP()->id;
        });

        static::addGlobalScope('order_by', function (Builder $builder) {
            return $builder->orderBy('id', 'desc');
        });

        static::addGlobalScope('creator_equals_auth', function (Builder $builder) {
            $builder->when(auth()->user()->role === 'partner', function (Builder $builder) {
                return $builder->where('creator_user_id', '=', auth()->user()->id);
            });
        });

        static::addGlobalScope('show_only_own_tasks', function (Builder $builder) {
            $builder->when(auth()->user()->role === 'agency', function (Builder $builder) {
                return $builder->where('doer_user_id', '=', auth()->user()->id);
            });
        });
    }

    /** @return \Illuminate\Database\Eloquent\Relations\Relation */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\Relation */
    public function doer()
    {
        return $this->belongsTo(User::class, 'doer_user_id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\Relation */
    public function comments()
    {
        return $this->hasMany(ServicedeskTaskComment::class, 'servicedesk_task_id');
    }

    public static function getTaskTypes()
    {
        return self::$task_types;
    }

    public static function getTaskTypesList()
    {
        $result = [];
        foreach (self::$task_types as $i => $t) {
            $result[$i] = $t['caption'];
        }
        return $result;
    }

    public static function getTaskStatusList()
    {
        $result = [];
        foreach (self::$statuses as $i => $t) {
            $result[$i] = $t['caption'];
        }
        return $result;
    }

    /** @return string */
    public function getStatusTextAttribute(): string
    {
        return self::$statuses[$this->status]['caption'] ?? '';
    }

    /** @return string */
    public function getTypeTextAttribute(): string
    {
        return self::$task_types[$this->type]['caption'] ?? '';
    }

    /** @return string */
    public function getStatusClassAttribute(): string
    {
        return self::$statuses[$this->status]['class'] ?? '';
    }

    /** @return string */
    public function getTypeClassAttribute(): string
    {
        return self::$task_types[$this->type]['class'] ?? '';
    }

    public function getViewLinkAttribute()
    {
        $route = auth()->user()->role . '.servicedesk.show';
        if (Route::has($route)) {

            return view('components.servicedesk_task.view_link')->with([
                'route' => route($route, $this),
                'subject' => $this->subject
            ]);
        }

        return '';
    }
}
