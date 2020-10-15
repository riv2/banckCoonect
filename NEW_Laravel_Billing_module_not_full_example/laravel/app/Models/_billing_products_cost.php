<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class _billing_products_cost extends Model
{
    protected $table      = '_billing_products_cost';
    public    $timestamps = false;
    protected $primaryKey = 'uid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid', 'uniq_currency', 'uid_product', 'date_created', 'date_updated', 'article', 'cost', 'count', 'advanced'
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
