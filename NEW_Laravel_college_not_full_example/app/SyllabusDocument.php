<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Services\FileType;
use OwenIt\Auditing\Contracts\Auditable;

class SyllabusDocument extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use SoftDeletes;

    const MATERIAL_TYPE_TEORETICAL  = 'teoretical';
    const MATERIAL_TYPE_PRACTICAL   = 'practical';
    const MATERIAL_TYPE_SRO         = 'sro';
    const MATERIAL_TYPE_SROP        = 'srop';

    const RESOURCE_TYPE_FILE        = 'file';
    const RESOURCE_TYPE_LINK        = 'link';

    protected $table    = 'syllabus_document';
    protected $fillable = [
        'link',
        'link_description',
        'filename',
        'lang'
    ];

    /**
     * @return bool|string
     */
    public function getPublicUrl()
    {
        $fileName = '';

        if($this->resource_type == self::RESOURCE_TYPE_FILE)
        {
            $fileName = config('app.url') . '/syllabuses/' . $this->filename;
        }

        if($this->resource_type == self::RESOURCE_TYPE_LINK)
        {
            $fileName = $this->link;

            if($this->isYoutubeLink())
            {
                if(strpos($this->link, 'youtu.be'))
                {
                    $parts = explode('/', $this->link);
                    $fileName = 'https://youtube.com/embed/' . $parts[count($parts) - 1];
                }
                else
                {
                    $fileName = str_replace('watch?v=', 'embed/', $fileName);
                }
            }
        }

        if(!$fileName)
        {
            return false;
        }

        if($this->document_type == 'msoffice')
        {
            $fileName = 'https://view.officeapps.live.com/op/view.aspx?src=' . urlencode($fileName);
        }

        return $fileName;
    }

    /**
     * @return bool
     */
    public function isYoutubeLink()
    {
        if($this->resource_type == \App\SyllabusDocument::RESOURCE_TYPE_LINK)
        {
            if(strpos($this->link, 'youtube.com/') || strpos($this->link, 'youtu.be'))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getFileBasePath()
    {
        return $this->resource_type == self::RESOURCE_TYPE_FILE ? public_path('/syllabuses/' . $this->filename) : false;
    }

    /**
     * @param $syllabusId
     * @param $materialType
     * @param $fillParams
     * @return bool|mixed
     */
    static function createLink($syllabusId, $materialType, $fillParams)
    {
        $syllabusDocument = new self();
        $syllabusDocument->syllabus_id = $syllabusId;
        $syllabusDocument->resource_type = SyllabusDocument::RESOURCE_TYPE_LINK;
        $syllabusDocument->material_type = $materialType;
        $syllabusDocument->fill($fillParams);
        $syllabusDocument->document_type = FileType::getType($syllabusDocument->link);

        if($syllabusDocument->save())
        {
            return $syllabusDocument->id;
        }

        return false;
    }

    /**
     * @param $syllabusId
     * @param $materialType
     * @param $file
     * @return bool|mixed
     */
    static function createFile($syllabusId, $materialType, $file, $lang = null)
    {
        $filename = 'syllabus_' . $syllabusId . '_' . time() . rand(1, 1000) . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

        $syllabusDocument = new self();
        $syllabusDocument->syllabus_id = $syllabusId;
        $syllabusDocument->resource_type = SyllabusDocument::RESOURCE_TYPE_FILE;
        $syllabusDocument->material_type = $materialType;
        $syllabusDocument->filename = $filename;
        $syllabusDocument->filename_original = $file->getClientOriginalName();
        $syllabusDocument->lang = $lang;

        $file->move(public_path('syllabuses'), $filename);

        $syllabusDocument->document_type = FileType::getType(public_path('syllabuses') . '/' . $filename);

        if($syllabusDocument->save())
        {
            return $syllabusDocument->id;
        }

        return false;
    }

    /**
     * @param $syllabusId
     * @param $documentId
     * @param $params
     * @return bool
     */
    static function updateLink($syllabusId, $documentId, $params)
    {
        $syllabusDocument = self
            ::where('syllabus_id', $syllabusId)
            ->where('id', $documentId)
            ->first();

        if ($syllabusDocument) {
            $syllabusDocument->fill($params);
            return $syllabusDocument->save();
        }

        return false;
    }

    /**
     * @param $syllabusId
     * @param $documentId
     * @param $file
     * @return bool
     */
    static function updateFile($syllabusId, $documentId, $file)
    {
        $syllabusDocument = self
            ::where('syllabus_id', $syllabusId)
            ->where('id', $documentId)
            ->first();

        if ($syllabusDocument) {

            $filename = 'syllabus_' . $syllabusId . '_' . time() . rand(1, 1000) . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            File::delete(public_path('syllabuses/' . $syllabusDocument->filename));

            $syllabusDocument->filename = $filename;
            $syllabusDocument->save();

            $file->move(public_path('syllabuses'), $filename);

            return $syllabusDocument->save();
        }

        return false;
    }

    /**
     * @return bool|null
     */
    public function delete()
    {
        /*if($this->resource_type == SyllabusDocument::RESOURCE_TYPE_FILE)
        {
            File::delete($this->getFileBasePath());
        }*/

        return parent::delete();
    }
}
