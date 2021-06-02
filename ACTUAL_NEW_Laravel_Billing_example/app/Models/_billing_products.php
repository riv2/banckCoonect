<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class _billing_products extends Model
{
    protected $table      = '_billing_products';
    public    $timestamps = false;
    protected $primaryKey = 'uid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid', 'name', 'date_created', 'date_updated', 'article', 'advanced', 'type_product', 'table', 'uniq_table', 'code', 'advanced_value'
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
