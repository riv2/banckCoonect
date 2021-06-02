<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

/**
 * App\Models\Faq
 *
 * @property int $id
 * @property int $faq_category_id
 * @property string $question
 * @property string $answer
 * @property int $position
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property \Jenssegers\Date\Date|null $deleted_at
 * @property-read string $destroy_button
 * @property-read string $edit_button
 * @property-read string|\App\Models\view $view_link
 * @method static \Illuminate\Database\Eloquent\Builder|Faq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faq newQuery()
 * @method static \Illuminate\Database\Query\Builder|Faq onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Faq query()
 * @method static \Illuminate\Database\Query\Builder|Faq withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Faq withoutTrashed()
 * @mixin \Eloquent
 */
class Faq extends Model
{
    use SoftDeletes;

    protected $table = 'faq';

    protected $fillable = [
        'faq_category_id',
        'question',
        'answer',
        'position',
    ];

    /**
     * @return string|view
     */
    public function getViewLinkAttribute(): string
    {
        $route = auth()->user()->role . '.settings.faq.category.show';

        if (Route::has($route)) {

            return view('components.faq.view_link')->with(['route' => route($route,
                [
                    'faq' => $this->faq_category_id,
                    'category' => $this->id,
                ]),
                'question' => $this->question]);
        }

        return '';
    }

    public function getEditButtonAttribute(): string
    {
        $route = auth()->user()->role . '.settings.faq.category.edit';

        if (Route::has($route)) {

            return view('components.faq.edit_button')->with(['route' => route($route, [
                'faq' => $this->faq_category_id,
                'category' => $this->id
            ])]);
        }

        return '';
    }

    public function getDestroyButtonAttribute(): string
    {
        $route = auth()->user()->role . '.settings.faq.category.destroy';

        if (Route::has($route)) {

            return view('components.faq.destroy_button')
                ->with(['route' => route($route, [
                    'faq' => $this->faq_category_id,
                    'category' => $this
                ])]);
        }

        return '';
    }
}
