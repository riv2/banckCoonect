<?php

namespace App\Models\StudentRequest;

use Illuminate\Database\Eloquent\Model;
use App\Profiles;
use Auth;
use App\ProfileDoc;
use App\Models\StudentRequest\StudentRequestType;

class StudentRequest extends Model
{
    protected $table = 'student_requests';

    const STATUS_DECLINED = 'declined';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_NEW      = 'new';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function studentProfile()
    {
        return $this->hasOne(Profiles::class, 'user_id', 'user_id');
    }

    
    static public function getRequestsList()
    {
    	$id = Auth::user()->id;
    	$listDb = StudentRequest
            ::where('student_requests.user_id', $id)
            ->select('date', 'key', 'student_request_types.name_ru as type', 'profile_docs.filename', 'student_requests.id', 'student_requests.user_id_who_declined', 'student_requests.order_id')
            ->leftJoin('student_request_types', 'student_request_types.id', 'student_requests.type_id')
            ->leftJoin('profile_docs', 'profile_docs.id', 'student_requests.doc_id')
            ->orderBy('student_requests.id','DESC')
            ->get();


        $list = [];
        foreach ($listDb as $item) { 
        	$profileDoc = new ProfileDoc;
        	$item->url = url($profileDoc->getPathForDoc(StudentRequestType::DOCS_TYPE_PREFIX . $item->key, $item->filename) . $item->filename . ProfileDoc::EXT);

        	$item->comment = StudentRequestComment
        						::where('for_student', 1)
        						->where('request_id', $item->id)
        						->orderBy('id', 'asc')
        						->first();
            if(isset($item->comment)) {
                $item->comment = $item->comment->text;
            } else {
                $item->comment = '';
            }
        						
        	$item->status = self::getStatus($item);


        	unset($item->user_id_who_declined);
        	unset($item->order_id);
        	unset($item->filename);
        	$list[] = $item;
        }

        return $list;
    }

    static function getStatus($item)
    {
        if($item->user_id_who_declined !== null) {
            return self::STATUS_DECLINED;
        } elseif($item->order_id) {
            return self::STATUS_ACCEPTED;
        } else {
            return self::STATUS_NEW;
        }
    }



    
}


