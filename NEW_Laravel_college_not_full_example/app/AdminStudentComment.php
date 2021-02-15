<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminStudentComment extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->hasOne(User::class,  'id', 'author_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class,  'id', 'user_id');
    }

    protected $table = 'admin_student_comment';
}
