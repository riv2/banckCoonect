<?php

namespace App\Models;

use App\Filters\ScopeFilter;
use App\Models\Traits\HasPpId;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;

/**
 * App\Models\BannedLink
 *
 * @property string $link_id
 * @property \Jenssegers\Date\Date $date_start
 * @property \Jenssegers\Date\Date|null $date_end
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BannedLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BannedLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BannedLink query()
 * @mixin \Eloquent
 */
class BannedLink extends Model
{
    use SoftDeletes, ScopeFilter, HasPpId;

    protected $table = 'banned_links';
    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $dates = [
        'date_start',
        'date_end',
    ];

    protected $fillable = [
        'id',
        'link_id',
        'date_start',
        'date_end',
        'web_id',
        'comment',
        'evidence',
    ];

    /**
     * Get the comments for the blog post.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'link_id', 'link_id');
    }

    /**
     * Get the banned_link's owner.
     */
    public function link()
    {
        return $this->hasOne(Link::class, 'id', 'link_id');
    }

    public function getViewLinkAttribute()
    {
        $route = auth()->user()->role . '.banned-links.show';
        if (Route::has($route)) {

            return view('components.banned_links.view_link')->with([
                'route' => route($route, $this),
                'bannedLinkId' => $this->link_id,
            ]);
        }

        return '';
    }

    public function getViewDetailsAttribute()
    {
        $route = auth()->user()->role . '.orders.index';

        if (Route::has($route)) {

            return view('components.banned_links.view_details')->with([
                'route' => route($route, ['link_id' => $this->link_id]),
                'bannedLinkId' => $this->link_id,
            ]);
        }
        return '';
    }

    public function getPartnerAttribute()
    {
        $partnerGetRow = $this->link()->first();
        if (isset($partnerGetRow->partner_id) && $partnerGetRow->partner_id != null) {
            $partner_id = $partnerGetRow->partner_id;
            $partner = User::query()->where('id', $partner_id)->first();
        } else {
            //TODO - чтож делать - пока болванку поставим
            $partner = User::query()->first();
            // TODO - Надо подумать тут.
        }
        return view('components.banned_links.partner')->with([
            'bannedLinkId' => $this->link_id,
            'partner' => $partner
        ]);
    }
}
