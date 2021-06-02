<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class _billing_gateways extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    protected $table        = '_billing_gateways';
    public    $timestamps   = false;
    protected $primaryKey   = 'uniq';
    public    $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uniq', 'name', 'date_created', 'date_updated', 'uniqs_currencies', 'advanced', 'settings'
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
