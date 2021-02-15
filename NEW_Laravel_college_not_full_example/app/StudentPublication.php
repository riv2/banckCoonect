<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Image;
use OwenIt\Auditing\Contracts\Auditable;

class StudentPublication extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'student_publications';

    protected $fillable = [
        'type',
        'name',
        'place',
        'year',
        'issue_number',
        'colleagues',
        'lang',
        'isbn'
    ];

    /**
     * @param $file
     * @return bool
     */
    public function syncFile($file)
    {
        $this->file_name = '';

        if($file)
        {
            $tmpFilePath = 'images/uploads/student_publication/';
            $fileName =  str_slug($file->getClientOriginalName(), '-').'-'.md5(str_random(15));

            $img = Image::make($file);
            $img->fit(1350, 963)->save($tmpFilePath.$fileName.'-b.jpg', 75);
            $img->fit(450, 321)->save($tmpFilePath.$fileName. '-s.jpg', 75);

            $this->file_name = $fileName;
        }

        return true;
    }
}
