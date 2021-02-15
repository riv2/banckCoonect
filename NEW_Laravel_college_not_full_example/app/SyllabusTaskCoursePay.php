<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-12-19
 * Time: 17:56
 */

namespace App;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};

class SyllabusTaskCoursePay extends Model
{

    use SoftDeletes;

    const STATUS_PROCESS = "process";
    const STATUS_OK      = "ok";

    protected $table = 'syllabus_task_course_pay';

    protected $fillable = [
        'discipline_id',
        'user_id',
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
    public function discipline()
    {
        return $this->hasOne(Discipline::class, 'id', 'discipline_id');
    }

}