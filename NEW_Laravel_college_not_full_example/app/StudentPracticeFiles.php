<?php

namespace App;

use App\Services\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentPracticeFiles extends Model
{
    use SoftDeletes;

    protected $table = 'students_practice_files';

    public $timestamps = false;

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

    public function getSecondsRemove()
    {
        ini_set('date.timezone', 'Asia/Almaty');
        return time() - strtotime($this->created_at);
    }
}
