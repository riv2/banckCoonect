<?php

namespace App\Models\Traits;

use App\Models\Offer;

/**
 * Трейт для моделей, содержащих колонку pp_id
 *
 * @author Tony V <vaninanton@gmail.com>
 * @method mixed methodName()
 */
trait HasOfferId
{
    /** @return \Illuminate\Database\Eloquent\Relations\Relation */
    public function offer()
    {
        return $this->belongsTo(Offer::class, 'id', 'offer_id');
    }
}
