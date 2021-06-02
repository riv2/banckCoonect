<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ServicedeskTaskTemplate
 *
 * @property int $id
 * @property string $title
 * @property string $body
 * @property bool $is_favorite
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property \Jenssegers\Date\Date|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ServicedeskTaskTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServicedeskTaskTemplate newQuery()
 * @method static \Illuminate\Database\Query\Builder|ServicedeskTaskTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServicedeskTaskTemplate query()
 * @method static \Illuminate\Database\Query\Builder|ServicedeskTaskTemplate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ServicedeskTaskTemplate withoutTrashed()
 * @mixin \Eloquent
 */
class ServicedeskTaskTemplate extends Model
{
    use SoftDeletes;
    protected $table = 'servicedesk_task_templates';

    protected $casts = [
        'is_favorite' => 'bool'
    ];

    protected $fillable = [
        'title',
        'body',
        'is_favorite'
    ];
}
