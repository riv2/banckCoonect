<?php

namespace App;

use App\{Course};
use App\Services\{Auth,Avatar};
use Illuminate\Database\Eloquent\{Model,SoftDeletes};

class CourseTopics extends Model
{

    const STATUS_MODERATION = 'moderation';
    const STATUS_ACTIVE = 'active';

    const STATUS_FORM_HOLDING_FULLTIME = 'fulltime';
    const STATUS_FORM_HOLDING_ONLINE = 'online';
    const STATUS_FORM_HOLDING_DISTANT = 'distant';

    const IS_CERTIFICATE_YES = 'yes';
    const IS_CERTIFICATE_NO = 'no';

    protected $table = 'courses_topics';

    protected $fillable = [
        'courses_id',
        'language',
        'title',
        'resource_link',
        'resource_file',
        'questions',
    ];

    public function getDisciplineAttribute($value)
    {
        return $this->disciplines->first();
    }

    /**
     * @param $value
     */
    public function setResourceFileAttribute($value)
    {
        if($value) {
            $this->attributes['resource_file'] = 'res_file_' . time() . '.' . $value->getClientOriginalExtension();
        }
    }

    public function setResourceLinkAttribute($value)
    {
        if($value) {
            $this->attributes['resource_link'] = Course::getNormalizeLink($value);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function course()
    {
        return $this->hasOne('App\Course', 'id', 'courses_id');
    }

    /**
     * @return mixed
     */
    static function getListForAdmin($iCourseId, $lang = 'ru')
    {
        return self::select([
            'id',
            'title',
            'language'
        ])->
        where('courses_id',$iCourseId)->
        where('language',$lang)->
        whereNull('deleted_at')->
        orderBy('id','ASC')->
        get();

    }

    /**
     * @param $photoFile
     * @param $certificateFile
     * @return bool
     */
    public function saveFiles($resourceFile=null)
    {

        if ($resourceFile)
        {
            $resourceFile->move(public_path('images/uploads/courses'), $this->resource_file);
        }

        return true;
    }

    /**
     * @return bool|null
     */
    public function delete()
    {

        $resourceFile = public_path('images/uploads/courses/' . $this->resource_file);

        if(file_exists($resourceFile) && is_file($resourceFile))
        {
            \File::delete($resourceFile);
        }

        return parent::delete();
    }


}