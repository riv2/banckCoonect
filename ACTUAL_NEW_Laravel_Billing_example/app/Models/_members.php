<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class _members extends Model
{
    protected $table      = '_members';
    public    $timestamps = false;
    protected $primaryKey = 'uniq';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uniq', 'email', 'currency_uniq'
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
