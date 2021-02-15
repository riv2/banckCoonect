<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class Poll extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include not completed polls
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  integer $user_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailablePolls($query, $user_id)
    {
        return $query->where('is_active', true)
                    ->where('end_date', '>', Carbon::now())
                    ->where(function ($query) use ($user_id) {
                        return $query->whereHas('polls_user', function ($query) use ($user_id) {
                            return $query->where('is_completed', false)
                                        ->where('is_available', true)
                                        ->where('user_id', $user_id);
                        });
                    });
    }

    public function scopeOrderByActive($query)
    {
        $activeColumn = DB::raw('IF(is_active = 1, IF(end_date > NOW(), 1, 0), 0) AS active');
        return $query->select('id', 'title_ru', $activeColumn);
    }

    /**
     * Get the questions for the poll.
     */
    public function questions()
    {
        return $this->hasMany('App\PollQuestion');
    }

    /**
     * Get the users polls for the poll.
     */
    public function polls_user()
    {
        return $this->hasMany('App\PollsUser');
    }
}
