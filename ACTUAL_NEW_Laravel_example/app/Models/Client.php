<?php

namespace App\Models;

use App\Models\Traits\HasPpId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Client
 *
 * @property string $id
 * @property int $pp_id
 * @property \Jenssegers\Date\Date|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Click[] $clicks
 * @property-read int|null $clicks_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PixelLog[] $pixelLogs
 * @property-read int|null $pixel_logs_count
 * @property-read \App\Models\Pp $pp
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @mixin \Eloquent
 */
class Client extends Model
{
    use HasPpId;

    /**
     * Поле updated_at отсутствует в таблице, выключаем его
     */
    public const UPDATED_AT = null;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'pp_id',
    ];

    public function clicks(): HasMany
    {
        return $this->hasMany(Click::class, 'client_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function pixelLogs(): HasMany
    {
        return $this->hasMany(PixelLog::class, 'parsed_client_id');
    }
}
