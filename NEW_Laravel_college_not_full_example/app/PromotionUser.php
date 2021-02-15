<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromotionUser extends Model
{
    const STATUS_ACTIVE         = 'active';
    const STATUS_MODERATION     = 'moderation';
    const STATUS_BLOCK          = 'block';
    const STATUS_REJECT         = 'reject';

    protected $table = 'promotion_user';

    public function work()
    {
        return $this->hasOne(PromotionUserWork::class, 'promotion_user_id', 'id');
    }
}
