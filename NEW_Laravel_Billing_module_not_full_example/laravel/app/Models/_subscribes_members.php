<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class _subscribes_members extends Model
{
    protected $table      = '_subscribes_members';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid', 'member_uniq', 'subscribe_uid', 'date_started', 'date_expires', 'grantor_uniq', 'payment_uid'
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
