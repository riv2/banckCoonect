<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class FinanceOperation extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'finance_operations';

    protected $fillable = [
        'cost',
        'finance_nomenclature_id',
        'balance'
    ];

    public function nomenclature()
    {
        return $this->hasOne(FinanceNomenclature::class);
    }
}
