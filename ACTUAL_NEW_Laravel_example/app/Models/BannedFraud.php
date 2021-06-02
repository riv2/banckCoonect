<?php

namespace App\Models;

use App\Filters\ScopeFilter;
use App\Models\Traits\HasPpId;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;

class BannedFraud extends Model
{
    use SoftDeletes, ScopeFilter, HasPpId;


    protected $table = 'banned_frauds';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'id',
        'order_id',
        'offer_id',
        'comment',
        'evidence',
    ];

    /**
     * Get the banned_frauds's order.
     */
    public function order()
    {
        return $this->hasOne(Order::class, 'order_id', 'order_id');
    }

    public function getViewLinkAttribute()
    {
        $route = auth()->user()->role . '.banned-frauds.show';
        if (Route::has($route)) {

            return view('components.banned_frauds.view_link')->with([
                'route' => route($route, $this),
                'bannedOrderId' => $this->order_id,
            ]);
        }

        return '';
    }

    public function getOfferAttribute()
    {
        if(!$this->order) {
            return '';
        }
        $offer = Offer::query()
            ->where('id', '=', $this->order->offer_id)
            ->first();
        if (!$offer) {
            return '';
        }
        return view('components.banned_frauds.offer')->with([
            'offer' => $offer,
        ]);
    }

    public function getPartnerAttribute()
    {
        if(!$this->order) {
            return '';
        }
        $partner = User::query()
            ->where('id', '=', $this->order->partner_id)
            ->first();
        if (!$partner) {
            return '';
        }

        return view('components.banned_frauds.partner')->with([
            'partner' => $partner,
        ]);
    }
}
