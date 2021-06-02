<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;

/**
 * App\Models\FaqCategory
 *
 * @property int $id
 * @property int $pp_id
 * @property string $title
 * @property int $position
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property \Jenssegers\Date\Date|null $deleted_at
 * @property-read string $destroy_button
 * @property-read string $edit_button
 * @property-read string $view_link
 * @method static \Illuminate\Database\Eloquent\Builder|FaqCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FaqCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|FaqCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FaqCategory query()
 * @method static \Illuminate\Database\Query\Builder|FaqCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|FaqCategory withoutTrashed()
 * @mixin \Eloquent
 */
class FaqCategory extends Model
{
    use SoftDeletes;

    protected $table = 'faq_categories';

    protected $fillable = [
        'pp_id',
        'title',
        'position',
    ];

    public function getViewLinkAttribute(): string
    {
        $route = auth()->user()->role . '.settings.faq.show';

        if (Route::has($route)) {

            return view('components.faqcategory.view_link')->with(['route' => route($route, $this), 'title' => $this->title]);
        }

        return '';
    }

    public function getEditButtonAttribute(): string
    {
        $route = auth()->user()->role . '.settings.faq.edit';

        if (Route::has($route)) {

            return view('components.faqcategory.edit_button')->with(['route' => route($route, $this)]);
        }

        return '';
    }

    public function getDestroyButtonAttribute(): string
    {
        $route = auth()->user()->role . '.settings.faq.destroy';

        if (Route::has($route)) {

            return view('components.faqcategory.destroy_button')->with(['route' => route($route, $this)]);
        }

        return '';
    }
}
