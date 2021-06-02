<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\BusinessUnit
 *
 * @property int $category_id
 * @property int $pp_id
 * @property string|null $category_name
 * @property string|null $category_param
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property \Jenssegers\Date\Date|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BusinessUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BusinessUnit newQuery()
 * @method static \Illuminate\Database\Query\Builder|BusinessUnit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BusinessUnit query()
 * @method static \Illuminate\Database\Query\Builder|BusinessUnit withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BusinessUnit withoutTrashed()
 * @mixin \Eloquent
 */
class BusinessUnit extends Model
{
    use SoftDeletes;
    protected $table = 'business_units';
    protected $primaryKey = 'category_id';

    protected $casts = [
        'pp_id' => 'int'
    ];

    protected $fillable = [
        'pp_id',
        'category_name',
        'category_param'
    ];
}
