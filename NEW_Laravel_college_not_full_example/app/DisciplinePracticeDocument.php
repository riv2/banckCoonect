<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Services\FileType;

class DisciplinePracticeDocument extends Model
{
    protected $table = 'discipline_practice_documents';

    public static function createDocument($disciplines_id, $request)
    {
        $filename = 'syllabus_' . $disciplines_id . '_' . time() . rand(1, 1000) . '.' . pathinfo($request['document']->getClientOriginalName(), PATHINFO_EXTENSION);

        $disciplines_document = new self();
        $disciplines_document->lang = $request['language'];
        $disciplines_document->discipline_id = $disciplines_id;
        $disciplines_document->file_name = $filename;
        $disciplines_document->original_name = $request['document']->getClientOriginalName();
        $disciplines_document->description = $request['description'];

        $request['document']->move(public_path('syllabus_documents'), $filename);

        $disciplines_document->file_type = FileType::getType(public_path('syllabus_documents') . '/' . $filename);;

        if($disciplines_document->save())
        {
            return $disciplines_document;
        }

        return false;
    }

    public function updateDocument($request)
    {
        $this->is_student_filed = 1;
        $request['document']->move(public_path('syllabus_documents'), $this->file_name);

        if($this->save())
        {
            return $this->id;
        }
        return false;
    }

    public function getPublicUrl()
    {
        $fileName = config('app.url') . '/syllabus_documents/' . $this->file_name;

        if($this->file_type == 'msoffice')
        {
            $fileName = 'https://view.officeapps.live.com/op/view.aspx?src=' . urlencode($fileName);
        }

        return $fileName;
    }

    public function deleteFile()
    {
        \File::delete(public_path() .'/syllabus_documents/' . $this->file_name);
    }
}
