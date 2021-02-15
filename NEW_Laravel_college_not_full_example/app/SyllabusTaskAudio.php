<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-10-30
 * Time: 11:06
 */

namespace App;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};

class SyllabusTaskAudio extends Model
{

    use SoftDeletes;

    protected $table = 'syllabus_task_audio';

    protected $fillable = [
        'question_id',
        'filename',
        'origin_filename'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function question()
    {
        return $this->hasOne(Syllabus::class, 'id', 'question_id');
    }


}