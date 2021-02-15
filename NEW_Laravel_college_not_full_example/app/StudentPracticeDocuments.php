<?php

namespace App;

use File;
use App\Services\Auth;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StudentPracticeDocuments
 *
 * @package App
 *
 * @property string file_name
 * @property string original_name
 */
class StudentPracticeDocuments extends Model
{
    protected $table = 'students_practice_documents';

    /**
     * @param $file
     */
    public function saveUserDocument($file)
    {
        $filename = 'syllabus_' . Auth::user()->id . '_' . time() . rand(1, 1000) . '.' . pathinfo($file['document']->getClientOriginalName(), PATHINFO_EXTENSION);

        $this->file_name = $filename;
        $this->original_name = $file['document']->getClientOriginalName();
        $file['document']->move(public_path('syllabus_documents/student_documents'), $filename);
    }

    /**
     * @return string
     */
    public function getUrlToDownload()
    {
        $fileName = config('app.url') . '/syllabus_documents/student_documents/' . $this->file_name;

        return $fileName;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function removeFile()
    {
        File::delete(public_path('syllabus_documents/student_documents') . '/' . $this->file_name);
        $this->delete();

        return true;
    }
}
