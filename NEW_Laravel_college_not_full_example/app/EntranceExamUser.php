<?php
/**
 * User: dadicc
 * Date: 2/28/20
 * Time: 4:26 PM
 */

namespace App;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use Illuminate\Support\Facades\{DB,Log};

class EntranceExamUser extends Model
{

    use SoftDeletes;

    protected $table = 'entrance_exam_user';

    protected $fillable = [
        'speciality_id',
        'user_id',
        'entrance_exam_id',
        'point',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function speciality()
    {
        return $this->hasOne(Speciality::class, 'id', 'speciality_id');
    }


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
    public function entranceExam()
    {
        return $this->hasOne(EntranceExam::class, 'id', 'entrance_exam_id');
    }


}

