<?php
/**
 * User: dadicc
 * Date: 2/28/20
 * Time: 4:02 PM
 */

namespace App;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use Illuminate\Support\Facades\{DB,Log};

class CheckListExam extends Model
{

    use SoftDeletes;

    protected $table = 'check_list_exam';

    protected $fillable = [
        'check_list_id',
        'entrance_exam_id',
        'checked',
        'is_sum',
        'nct_number',
        'nct_code',
        'exams_date',
        'deleted_at',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function checkList()
    {
        return $this->hasOne(CheckList::class, 'id', 'check_list_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function entranceExam()
    {
        return $this->hasOne(EntranceExam::class, 'id', 'entrance_exam_id');
    }


    /**
     * @param $value
     */
    public function setExamsDateAttribute($value)
    {
        $this->attributes['exams_date'] = date('Y-m-d',strtotime($value));
    }


    /**
     * @param $value
     * @return false|null|string
     */
    public function getExamsDateAttribute($value)
    {
        if( !empty($value) )
        {
            return date('Y-m-d',strtotime($value));
        }
        return null;
    }


    /**
     * remove check_list_exam
     * @param int $iCheckListId
     * @param array $aEntranceExamList
     * @return
     */
    public static function removeByIds( $iCheckListId, $aEntranceExamList )
    {
        if( !empty($iCheckListId) && !empty($aEntranceExamList) && (count($aEntranceExamList) > 0) )
        {
            $oRemove = self::
            where('check_list_id',$iCheckListId)->
            whereIn('entrance_exam_id',$aEntranceExamList)->
            delete();
        }
    }


}

