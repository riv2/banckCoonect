<?php

namespace App\Models;

use App\Helpers\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OffersMetum
 *
 * @property int $offer_id
 * @property string $meta_name
 * @property array $meta_value
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OffersMetum newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OffersMetum newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OffersMetum query()
 * @mixin \Eloquent
 */
class OffersMetum extends Model
{
    use HasCompositePrimaryKey;

    /** @var array */
    protected $primaryKey = [
        'offer_id',
        'meta_name',
    ];

    protected $table = 'offers_meta';
    public $incrementing = false;

    protected $casts = [
        'offer_id' => 'int',
        'meta_value' => 'json'
    ];

    protected $fillable = [
        'offer_id',
        'meta_name',
        'meta_value'
    ];
}
