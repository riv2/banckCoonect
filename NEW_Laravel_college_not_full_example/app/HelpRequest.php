<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpRequest extends Model
{
    use SoftDeletes;

    protected $table = 'help_requests';

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
