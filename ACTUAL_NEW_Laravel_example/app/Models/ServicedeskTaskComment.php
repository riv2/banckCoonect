<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ServicedeskTaskComment
 *
 * @property int $id
 * @property int $servicedesk_task_id
 * @property int $partner_id
 * @property bool $is_public
 * @property string $body
 * @property array|null $attach
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property \Jenssegers\Date\Date|null $deleted_at
 * @property-read mixed $edit_link
 * @property-read User $partner
 * @property-read \App\Models\ServicedeskTask $task
 * @method static Builder|ServicedeskTaskComment newModelQuery()
 * @method static Builder|ServicedeskTaskComment newQuery()
 * @method static \Illuminate\Database\Query\Builder|ServicedeskTaskComment onlyTrashed()
 * @method static Builder|ServicedeskTaskComment query()
 * @method static \Illuminate\Database\Query\Builder|ServicedeskTaskComment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ServicedeskTaskComment withoutTrashed()
 * @mixin \Eloquent
 */
class ServicedeskTaskComment extends Model
{
    use SoftDeletes;
    protected $table = 'servicedesk_task_comments';

    /** @var array */
    protected $fillable = [
        'body',
        'partner_id',
        'attach',
        'is_public',
    ];

    /** @var array */
    protected $casts = [
        'servicedesk_task_id' => 'int',
        'partner_id' => 'int',
        'is_public' => 'bool',
        'attach' => 'array',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('is_public', function (Builder $builder) {
            $is_partner = (bool) (auth()->user()->role === 'partner');
            $builder->when($is_partner, function (Builder $builder) {
                return $builder->where('partner_id', '=', auth()->user()->id)->orWhere('is_public', '=', 1);
            });
        });
    }

    /** @return \Illuminate\Database\Eloquent\Relations\Relation */
    public function task(): Relation
    {
        return $this->belongsTo(ServicedeskTask::class, 'servicedesk_task_id', 'id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\Relation */
    public function partner(): Relation
    {
        return $this->belongsTo(User::class, 'partner_id', 'id');
    }

    public function getEditLinkAttribute()
    {
        // Если мы не партнер
        if (auth()->user()->role === 'partner') {
            return;
        }

        // Если комментарий - наш
        if ($this->partner_id !== auth()->user()->id) {
            return;
        }

        try {
            return route(
                auth()->user()->role . '.servicedesk.comments.edit',
                ['servicedesk' => $this->task, 'comment' => $this]
            );
        } catch (\Illuminate\Routing\Exceptions\UrlGenerationException $th) {
            // throw $th;
        }
    }
}
