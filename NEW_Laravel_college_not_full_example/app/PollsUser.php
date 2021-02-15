<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PollsUser extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'user_id',
        'poll_id',
        'is_completed',
        'is_available',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    /**
     * Get the poll for the user.
     */
    public function poll()
    {
        return $this->belongsTo('App\Poll');
    }

    /**
     * Get the user for the poll.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
