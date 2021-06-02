<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\XmlfeedOffer
 *
 * @property int $id
 * @property int $offer_material_id
 * @property int $pp_id
 * @property int $category_id
 * @property string $url
 * @property array $xml_data
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|XmlfeedOffer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|XmlfeedOffer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|XmlfeedOffer query()
 * @mixin \Eloquent
 */
class XmlfeedOffer extends Model
{
    protected $table = 'xmlfeed_offers';

    protected $casts = [
        'pp_id' => 'int',
        'category_id' => 'int',
        'offer_material_id' => 'int',
        'xml_data' => 'json',
        'url'=>'string'
    ];

    protected $fillable = [
        'pp_id',
        'category_id',
        'offer_material_id',
        'url',
        'xml_data'
    ];
}
