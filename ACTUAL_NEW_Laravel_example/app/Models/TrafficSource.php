<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;

/**
 * App\Models\TrafficSource
 *
 * @property int $id
 * @property string $title
 * @property \Jenssegers\Date\Date|null $deleted_at
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property-read mixed $view_link
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource newQuery()
 * @method static \Illuminate\Database\Query\Builder|TrafficSource onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource query()
 * @method static \Illuminate\Database\Query\Builder|TrafficSource withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TrafficSource withoutTrashed()
 * @mixin \Eloquent
 */
class TrafficSource extends Model
{
    use SoftDeletes;
    protected $table = 'traffic_sources';

    protected $fillable = [
        'title'
    ];

    public function getViewLinkAttribute()
    {
        $result = $this->title;
        $route = auth()->user()->role . '.traffic.sources.edit';
        if (Route::has($route)) {
            return view('components.traffic_source.view_link')->with([
                'route' => route($route, $this),
                'title' => $this->title
            ]);
        }
        return '';
    }
}
