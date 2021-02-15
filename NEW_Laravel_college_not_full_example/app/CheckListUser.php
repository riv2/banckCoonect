<?php
/**
 * User: dadicc
 * Date: 2/28/20
 * Time: 4:10 PM
 */

namespace App;

use Auth;
use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use Illuminate\Support\Facades\{DB,Log};

class CheckListUser extends Model
{

    use SoftDeletes;

    protected $table = 'check_list_user';

    protected $fillable = [
        'user_id',
        'check_list_id',
        'prerequisites_active',
        'interview_active',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function checkList()
    {
        return $this->hasOne(CheckList::class, 'id', 'check_list_id');
    }


}


