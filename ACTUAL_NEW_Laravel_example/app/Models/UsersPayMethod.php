<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\UsersPayMethod
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $pay_method_id
 * @property string|null $cc_type
 * @property string|null $cc_number
 * @property string|null $company_name Наименование
 * @property string|null $company_inn ИНН
 * @property string|null $bank_company_account Номер расчетного счета
 * @property string|null $bank_identifier_code БИК
 * @property string|null $bank_beneficiary Название банка
 * @property string|null $bank_correspondent_account Корр. счет
 * @property string|null $webmoney_number Номер WebMoney кошелька
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @property \Jenssegers\Date\Date|null $deleted_at
 * @property-read \App\Models\PayMethod|null $pay_method
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UsersPayMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UsersPayMethod newQuery()
 * @method static \Illuminate\Database\Query\Builder|UsersPayMethod onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UsersPayMethod query()
 * @method static \Illuminate\Database\Query\Builder|UsersPayMethod withTrashed()
 * @method static \Illuminate\Database\Query\Builder|UsersPayMethod withoutTrashed()
 * @mixin \Eloquent
 */
class UsersPayMethod extends Model
{
    use SoftDeletes;
    protected $table = 'users_pay_methods';

    protected $casts = [
        'user_id' => 'int',
        'pay_method_id' => 'int'
    ];

    protected $fillable = [
        'user_id',
        'pay_method_id',
        'cc_type',
        'cc_number',
        'company_name',
        'company_inn',
        'bank_company_account',
        'bank_identifier_code',
        'bank_beneficiary',
        'bank_correspondent_account',
        'webmoney_number'
    ];

    public function pay_method()
    {
        return $this->belongsTo(PayMethod::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
