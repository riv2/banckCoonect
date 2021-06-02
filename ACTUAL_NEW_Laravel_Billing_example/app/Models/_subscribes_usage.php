<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class _subscribes_usage extends Model
{
    protected $table      = '_subscribes_usage';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid', 'option_uid', 'member_uniq', 'amount', 'date_refreshed'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];
}
