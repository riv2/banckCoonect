<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;

/**
 * App\Models\OfferMaterial
 *
 * @property int $offer_material_id
 * @property int $offer_id
 * @property string|null $name
 * @property string $material_type
 * @property bool $status
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property \Jenssegers\Date\Date|null $deleted_at
 * @property array|null $material_params Параметры и настройки
 * @property array|null $material_files Прилагаемые файлы
 * @property-read mixed $delete_button
 * @property-read mixed $parameters
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Link[] $links
 * @property-read int|null $links_count
 * @property-read \App\Models\Offer $offer
 * @method static \Illuminate\Database\Eloquent\Builder|OfferMaterial newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfferMaterial newQuery()
 * @method static \Illuminate\Database\Query\Builder|OfferMaterial onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OfferMaterial query()
 * @method static \Illuminate\Database\Query\Builder|OfferMaterial withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OfferMaterial withoutTrashed()
 * @mixin \Eloquent
 */
class OfferMaterial extends Model
{
    use SoftDeletes;

    protected $table = 'offer_materials';
    protected $primaryKey = 'offer_material_id';

    protected $casts = [
        'offer_id' => 'int',
        'status' => 'bool',
        'material_params' => 'json',
        'material_files' => 'json'
    ];

    protected $fillable = [
        'offer_id',
        'name',
        'material_type',
        'status',
        'material_params',
        'material_files',
    ];

    public function setMaterialParam($key, $value)
    {
        $mp = $this->material_params;
        $mp[$key] = $value;
        $this->material_params = $mp;
    }

    public function links()
    {
        return $this->hasMany(Link::class, "offer_materials_id", "offer_material_id");
    }

    public static function storeLink($offerID, $data, $offerMaterial = null)
    {
        $offerMaterial = $offerMaterial ?? new static();
        $offerMaterial->material_params = ["link" => $data["link"]];
        $offerMaterial->name = $data["name"];
        $offerMaterial->offer_id = $offerID;
        $offerMaterial->material_type = "link";
        $offerMaterial->save();
        return $offerMaterial->offer_material_id;
    }

    public static function storeLanding($offerID, $data, $offerMaterial = null)
    {
        $offerMaterial = $offerMaterial ?? new static();
        $offerMaterial->material_params = ["link" => $data["link"]];
        $offerMaterial->name = $data["name"];
        $offerMaterial->offer_id = $offerID;
        $offerMaterial->material_type = "landing";
        $offerMaterial->save();
        return $offerMaterial->offer_material_id;
    }

    public static function storeFeed($offerID, $data, $offerMaterial = null)
    {
        $offerMaterial = $offerMaterial ?? new static();
        $offerMaterial->material_params = ["link" => $data["link"]];
        $offerMaterial->name = $data["name"];
        $offerMaterial->offer_id = $offerID;
        $offerMaterial->material_type = "xmlfeed";
        $offerMaterial->save();
        return $offerMaterial->offer_material_id;
    }

    public static function storePWA($offerID, $data, $offerMaterial = null)
    {
        $offerMaterial = $offerMaterial ?? new static();
        $offerMaterial->material_params = [
            "api_url" => $data["api_url"],
            "script" => $data["script"],
        ];
        $offerMaterial->name = $data["name"];
        $offerMaterial->offer_id = $offerID;
        $offerMaterial->material_type = "pwa";
        $offerMaterial->save();
        return $offerMaterial->offer_material_id;
    }

    public static function storeBanners($offerID, $name, $banners, $offerMaterial = null)
    {
        $offerMaterial = $offerMaterial ?? new static();
        $offerMaterial->material_params = [];
        $offerMaterial->name = $name;
        $offerMaterial->offer_id = $offerID;
        $offerMaterial->material_type = "banner";
        $offerMaterial->material_files = $banners;
        $offerMaterial->save();
        return $offerMaterial->offer_material_id;
    }

    public function getParametersAttribute()
    {
        switch ($this->material_type):
            case 'landing':
                $result = $this->material_params['link'];
                break;
            case 'banner':
                $result = '';
                foreach($this->material_files as $file) {
                    $result .= '<img height="60" src="/' . $file . '">';
                }
                break;
            case 'xmlfeed':
                $result = $this->material_params['link'];
                break;
        endswitch;
        return $result;
    }

    public function getDeleteButtonAttribute()
    {
        $route = 'advertiser.offers.materials.delete';
        if (Route::has($route)) {
            return view('components.offer_material.delete_button')->with([
                'route' => route($route, $this),
            ]);
        }

        return '';
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class, "offer_id", "id");
    }

}
