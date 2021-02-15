<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DebtTrust extends Model
{
    use SoftDeletes;

    protected $table = 'debt_trusts';
}
