<?php
/**
 * User: dadicc
 * Date: 2/28/20
 * Time: 3:16 PM
 */

namespace App;

use App\{
    CheckListExam,
    EntranceExam,
    Speciality
};
use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use Illuminate\Support\Facades\{DB,Log};

class CheckList extends Model
{

    use SoftDeletes;

    protected $table = 'check_list';

    protected $fillable = [
        'speciality_id',
        'basic_education',
        'citizenship',
        'education_level',
        'year',
        'documents_checked',
        'documents_is_sum',
        'prerequisites_checked',
        'prerequisites_is_sum',
        'interview_checked',
        'interview_is_sum',
        'ent_checked',
        'ent_is_sum',
        'total_point_checked',
    ];


    // basic education - базовое образование
    const BASIC_EDUCATION_HIGN_SCHOOL           = 'high_school';                    // среднее
    const BASIC_EDUCATION_VOCATIONAL_EDUCATION  = 'vocational_education';           // средне-специальное
    const BASIC_EDUCATION_HIGHER                = 'higher';                         // высшее

    // citizenship - гражданство
    const CITIZENSHIP_TYPE_CITIZENSHIP_KZ           = 'citizenship_kz';             // гражданство KZ
    const CITIZENSHIP_TYPE_ALL_CITIZENSHIP          = 'all_citizenship';            // все гражданства
    const CITIZENSHIP_TYPE_CITIZENSHIP_WITHOUT_KZ   = 'citizenship_without_kz';     // все гражданства кроме KZ

    // education level - уровень образования
    const EDUCATION_LEVEL_BACHELOR   = 'bachelor';                                  // бакалавр
    const EDUCATION_LEVEL_MAGISTRACY = 'magistracy';                                // магистратура


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function speciality()
    {
        return $this->hasOne(Speciality::class, 'id', 'speciality_id');
    }


    /**
     * sync fields
     * @param array $aParams
     * @return
     */
    public function syncFields( $aParams )
    {
        if( !empty($aParams) && is_array($aParams) && (count($aParams) > 0) )
        {
            if( !empty($this->id) && empty($aParams['documents_checked']) )
            {
                $this->documents_checked = 0;
            }
            if( !empty($this->id) && empty($aParams['documents_is_sum']) )
            {
                $this->documents_is_sum = 0;
            }
            if( !empty($this->id) && empty($aParams['prerequisites_checked']) )
            {
                $this->prerequisites_checked = 0;
            }
            if( !empty($this->id) && empty($aParams['prerequisites_is_sum']) )
            {
                $this->prerequisites_is_sum = 0;
            }
            if( !empty($this->id) && empty($aParams['interview_checked']) )
            {
                $this->interview_checked = 0;
            }
            if( !empty($this->id) && empty($aParams['interview_is_sum']) )
            {
                $this->interview_is_sum = 0;
            }
            if( !empty($this->id) && empty($aParams['ent_checked']) )
            {
                $this->ent_checked = 0;
            }
            if( !empty($this->id) && empty($aParams['ent_is_sum']) )
            {
                $this->ent_is_sum = 0;
            }
            if( !empty($this->id) && empty($aParams['total_point_checked']) )
            {
                $this->total_point_checked = 0;
            }
        }
    }


    /**
     * sync entrance_exam data
     * @param array $aData
     * @param int $iCheckListId
     * @return
     */
    public function syncEntranceExamData( $aData, $iCheckListId )
    {
        if( !empty($aData['entrance_exam']) && is_array($aData['entrance_exam']) && (count($aData['entrance_exam']) > 0) )
        {
            foreach( $aData['entrance_exam'] as $iKey => $entranceExamItem )
            {
                $oEntranceExam = EntranceExam::where('id',$entranceExamItem['id'] ?? 0)->whereNull('deleted_at')->first();
                if( !empty($oEntranceExam) )
                {
                    $oEntranceExam->fill( $entranceExamItem );
                    $oEntranceExam->syncFields( $entranceExamItem );
                    $oEntranceExam->save();
                    $oEntranceExam->saveFiles( $entranceExamItem );

                    // создаем связь если ее нету
                    $oCheckListExam = CheckListExam::
                    withTrashed()->
                    where('check_list_id',$iCheckListId)->
                    where('entrance_exam_id',$oEntranceExam->id)->
                    first();
                    if( empty($oCheckListExam) )
                    {
                        $oCheckListExam = new CheckListExam();
                    }
                    $oCheckListExam->fill([
                        'check_list_id'    => $iCheckListId,
                        'entrance_exam_id' => $oEntranceExam->id,
                        'deleted_at'       => null
                    ]);
                    $oCheckListExam->save();
                }
                unset($oEntranceExam);
            }
        }
        if( !empty($aData['removeEntranceExam']) && (count($aData['removeEntranceExam']) > 0) )
        {
            CheckListExam::removeByIds( $iCheckListId, $aData['removeEntranceExam'] );
        }
    }


    /**
     * @param $value
     * @return false|string
     */
    public function getCreatedAtAttribute( $value )
    {
        return date('Y-m-d',strtotime($value));
    }


    /**
     * get speciality list
     * @param null $sYear
     * @return mixed
     */
    public static function getSpecialityList( $sYear=null )
    {
        $aResponse = [];
        if( empty($sYear) ){ $sYear = date('Y'); }
        $oSpeciality = Speciality::
        select(['id','name'])->
        where('year',$sYear)->
        get();
        if( !empty($oSpeciality) && (count($oSpeciality) > 0) )
        {
            foreach( $oSpeciality as $oItem )
            {
                $aResponse[$oItem->id] = $oItem->name;
            }
        }
        return $aResponse;
    }


    /**
     * get basic education
     * @param
     * @return array
     */
    public static function getBasicEducation()
    {
        return [
            'high_school',
            'vocational_education',
            'higher'
        ];
    }


    /**
     * get citizenship list
     * @param
     * @return array
     */
    public static function getCitizenshipList()
    {
        return [
            'citizenship_kz',
            'all_citizenship',
            'citizenship_without_kz'
        ];
    }


    /**
     * get education level
     * @param
     * @return array
     */
    public static function getEducationLevel()
    {
        return [
            'bachelor',
            'magistracy'
        ];
    }


    /**
     * remove by id
     * @param int $iId
     * @return
     */
    public static function removeById( $iId )
    {
        if( !empty($iId) )
        {
            // удаляем связь
            $oCheckListExam = CheckListExam::
            where('check_list_id',$iId)->
            whereNull('deleted_at')->
            get();
            if( !empty($oCheckListExam) && (count($oCheckListExam) > 0) )
            {
                foreach( $oCheckListExam as $itemCLE )
                {
                    $itemCLE->delete();
                }
            }
            // удалям ПЛ
            $oRemove = self::
            where('id',$iId)->
            delete();
        }
    }


}

