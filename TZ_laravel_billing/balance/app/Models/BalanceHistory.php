<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceHistory extends Model
{
    protected $table      = 'balance_history';
    public    $timestamps = false;
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'value', 'balance', 'user_id', 'created_at'
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
