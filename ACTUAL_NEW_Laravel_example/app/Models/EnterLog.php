<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EnterLog
 *
 * @property int $enter_id
 * @property \Jenssegers\Date\Date|null $datetime
 * @property int $user_id
 * @property string|null $result
 * @property string $ip
 * @property string|null $ua
 * @method static \Illuminate\Database\Eloquent\Builder|EnterLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnterLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnterLog query()
 * @mixin \Eloquent
 */
class EnterLog extends Model
{
    protected $table = 'enter_log';
    protected $primaryKey = 'enter_id';
    public $timestamps = false;

    protected $casts = [
        'user_id' => 'int',
        'datetime' => 'datetime',
    ];

    protected $fillable = [
        'datetime',
        'user_id',
        'result',
        'ip',
        'ua'
    ];
}
