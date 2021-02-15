<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderUserSignature extends Model
{
    protected $table = 'order_user_signatures';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
