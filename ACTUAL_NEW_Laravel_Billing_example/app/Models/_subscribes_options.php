<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class _subscribes_options extends Model
{
    protected $table      = '_subscribes_options';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid', 'subscribe_uid', 'code', 'name', 'limitation', 'refresh_period'
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
