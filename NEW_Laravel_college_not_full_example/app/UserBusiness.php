<?php
/**
 * User: dadicc
 * Date: 1/28/20
 * Time: 3:57 PM
 */

namespace App;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};

class UserBusiness extends Model
{

    use SoftDeletes;

    protected $table = 'user_business';

    protected $fillable = [
        'user_id',
        'name',
        'adress',
        'bin',
        'bank_name',
        'bank_bic',
        'iik',
        'kbe',
        'phone',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }


}

