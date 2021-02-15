<?php
/**
 * User: dadicc
 * Date: 1/12/20
 * Time: 6:47 PM
 */

namespace App;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};

class UserBank extends Model
{

    use SoftDeletes;

    protected $table = 'user_bank';

    protected $fillable = [
        'user_id',
        'bank_id',
        'iban'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bank()
    {
        return $this->hasOne('App\Bank', 'id', 'bank_id');
    }


}