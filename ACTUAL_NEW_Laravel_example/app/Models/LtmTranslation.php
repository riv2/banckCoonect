<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LtmTranslation
 *
 * @property int $id
 * @property int $status
 * @property string $locale
 * @property string $group
 * @property string $key
 * @property string|null $value
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LtmTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtmTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtmTranslation query()
 * @mixin \Eloquent
 */
class LtmTranslation extends Model
{
    protected $table = 'ltm_translations';

    protected $casts = [
        'status' => 'int'
    ];

    protected $fillable = [
        'status',
        'locale',
        'group',
        'key',
        'value'
    ];
}
