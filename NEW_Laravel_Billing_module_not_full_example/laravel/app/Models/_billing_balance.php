<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class _billing_balance extends Model
{
    protected $table      = '_billing_balance';
    public    $timestamps = false;
    protected $primaryKey = 'uid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid', 'uniq_member', 'balance', 'date_updated'
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
