<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PayMethod
 *
 * @property int $id
 * @property string $caption
 * @property-read Collection|\App\Models\Pp[] $pps
 * @property-read int|null $pps_count
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|PayMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PayMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PayMethod query()
 * @mixin \Eloquent
 */
class PayMethod extends Model
{
    protected $table = 'pay_methods';
    public $timestamps = false;

    protected $fillable = [
        'caption'
    ];

    public function pps()
    {
        return $this->belongsToMany(Pp::class, 'pp_pay_methods');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_pay_methods')
                    ->withPivot('id', 'cc_type', 'cc_number', 'company_name', 'company_inn', 'bank_company_account', 'bank_identifier_code', 'bank_beneficiary', 'bank_correspondent_account', 'vat_tax', 'taxation_system', 'webmoney_number', 'deleted_at')
                    ->withTimestamps();
    }
}
