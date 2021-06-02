<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\XmlfeedCategory
 *
 * @property int $id
 * @property int $offer_material_id
 * @property int $pp_id
 * @property int $category_id
 * @property string $name
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|XmlfeedCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|XmlfeedCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|XmlfeedCategory query()
 * @mixin \Eloquent
 */
class XmlfeedCategory extends Model
{
    protected $table = 'xmlfeed_categories';

    protected $casts = [
        'pp_id' => 'int',
        'category_id' => 'int',
        'offer_material_id' => 'int',
    ];

    protected $fillable = [
        'pp_id',
        'category_id',
        'offer_material_id',
        'name'
    ];
}
