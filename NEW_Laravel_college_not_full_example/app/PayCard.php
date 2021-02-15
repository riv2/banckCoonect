<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayCard extends Model
{
    protected $table = 'pay_cards';

    protected $fillable = [
        'first_digits',
        'last_digits',
        'type',
        'exp_date',
        'issuer',
        'issuer_bank_country',
        'token'
    ];
}
