<?php
/**
 * User: dadicc
 * Date: 23.10.19
 * Time: 9:02
 */

namespace App;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};

class CourseStudent extends Model
{

    const STATUS_ACTIVE         = 'active';
    const STATUS_INACTIVE       = 'inactive';
    const STATUS_PROCESSING     = 'processing';

    const STATUS_PAYED_YES      = 'yes';
    const STATUS_PAYED_NO       = 'no';

    const PAYMENT_METHOD_BALANCE  = 'balance';
    const PAYMENT_METHOD_EPAY     = 'epay';

    protected $table = 'courses_student';

    protected $fillable = [
        'courses_id',
        'user_id',
        'language',
        'cost',
        'pay_method',
        'payed',
        'status',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function course()
    {
        return $this->hasOne('App\Course', 'id', 'courses_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }



}
