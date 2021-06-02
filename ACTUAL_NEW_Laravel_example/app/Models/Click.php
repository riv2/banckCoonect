<?php

namespace App\Models;

use App\Models\Traits\HasPpId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Click
 *
 * @property int $id
 * @property int $pp_id
 * @property int $partner_id
 * @property int $link_id
 * @property string|null $client_id
 * @property string|null $click_id
 * @property string|null $web_id
 * @property int $pixel_log_id
 * @property \Jenssegers\Date\Date|null $created_at
 * @property-read \App\Models\Pp $pp
 * @method static Builder|Click newModelQuery()
 * @method static Builder|Click newQuery()
 * @method static Builder|Click query()
 * @mixin \Eloquent
 */
class Click extends Model
{
    use HasPpId;

    protected $table = 'clicks';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $casts = [
        'pp_id' => 'int',
        'partner_id' => 'int',
        'link_id' => 'int',
        'pixel_log_id' => 'int',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('user_id', function (Builder $builder) {
            $is_partner = auth()->check() && auth()->user()->role === 'partner';

            $builder->when($is_partner, function (Builder $builder) {
                $builder->where('clicks.partner_id', '=', auth()->id());
            });
        });
    }
}
