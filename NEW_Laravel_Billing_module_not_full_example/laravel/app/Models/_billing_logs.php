<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class _billing_logs extends Model
{
    protected $table      = '_billing_logs';
    public    $timestamps = false;
    protected $primaryKey = 'uid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid', 'requester', 'uniqMember', 'status', 'data', 'advanced', 'date_created'
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
