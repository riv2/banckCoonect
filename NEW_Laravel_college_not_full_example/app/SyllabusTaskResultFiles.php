<?php
/**
 * User: dadicc
 * Date: 3/26/20
 * Time: 9:34 PM
 */

namespace App;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};

class SyllabusTaskResultFiles extends Model
{

    use SoftDeletes;

    protected $table = 'syllabus_task_result_files';

    protected $fillable = [
        'user_id',
        'discipline_id',
        'syllabus_id',
        'task_id',
        'name',
        'filename',
    ];


    /**
     * save result file
     * @param $file
     * @return bool
     */
    public function saveResultFile( $file )
    {

        if( !empty($file) && !empty($this->name) )
        {
            $filename = 'img_' . $this->id . '_' . time() . rand(1, 1000) . '.' . pathinfo($this->name, PATHINFO_EXTENSION);
            $this->filename = $filename;
            file_put_contents(public_path('syllabus_result_files') . '/' . $filename, base64_decode($file));

            if( filesize(public_path('syllabus_result_files') . '/' . $filename) > 10000000 )
            {
                unlink( public_path('syllabus_result_files') . '/' . $filename );
                return false;
            }
        }
        return true;
    }

}