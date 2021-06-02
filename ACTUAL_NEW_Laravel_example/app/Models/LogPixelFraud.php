<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LogPixelFraud
 *
 * @property int $id
 * @property array|null $data
 * @property string|null $referer
 * @property string|null $ip
 * @property bool|null $is_valid
 * @property string|null $status
 * @property \Jenssegers\Date\Date|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|LogPixelFraud newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LogPixelFraud newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LogPixelFraud query()
 * @mixin \Eloquent
 */
class LogPixelFraud extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'is_valid' => 'boolean',
    ];
}
