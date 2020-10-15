<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class _billing_transactions extends Model
{
    protected $table      = '_billing_transactions';
    public    $timestamps = false;
    protected $primaryKey = 'uid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid', 'uniq_member', 'uid_product', 'type_transaction', 'hide_transaction', 'sum', 'date_created', 'date', 'product_serialize', 'signature'
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
