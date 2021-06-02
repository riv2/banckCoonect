<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class _billing_currences extends Model
{
    protected $table      = '_billing_currences';
    public    $timestamps = false;
    protected $primaryKey = 'uniq';
    public    $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid', 'name', 'ratio', 'code', 'created', 'date_updated'
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
