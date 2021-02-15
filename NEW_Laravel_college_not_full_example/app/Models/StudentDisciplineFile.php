<?php

namespace App\Models;

use App\Services\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class StudentDisciplineFile extends Model
{
    use SoftDeletes;

    const AUTHOR_TEACHER = 'teacher';
    const AUTHOR_STUDENT = 'student';

    protected $table = 'student_discipline_files';

    public function saveFile($file)
    {
        $filename = 'syllabus_' . Auth::user()->id . '_' . time() . rand(1, 1000) . '.' . pathinfo($file['document']->getClientOriginalName(), PATHINFO_EXTENSION);

        $this->file_name = $filename;
        $this->original_name = $file['document']->getClientOriginalName();
        $file['document']->move(public_path('syllabus_documents/student_files'), $filename);
    }

    public function getUrlToDownload()
    {
        $fileName = config('app.url') . '/syllabus_documents/student_files/' . $this->file_name;

        return $fileName;
    }

    /**
     * @return false|int
     */
    public function getSecondsRemove()
    {
        ini_set('date.timezone', 'Asia/Almaty');
        return time() - strtotime($this->created_at);
    }

    public function canDeleteByTeacher($teacherId)
    {
        return $this->teacher_id == $teacherId;
    }
}
