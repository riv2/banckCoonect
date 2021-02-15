<?php
/**
 * User: dadicc
 * Date: 2/28/20
 * Time: 12:14 PM
 */

namespace App;

use Auth;
use App\{
    CheckList,
    CheckListExam,
    EntranceExamFiles,
    EntranceExamUser
};
use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use Illuminate\Support\Facades\{DB,Log};

class EntranceExam extends Model
{

    use SoftDeletes;

    protected $table = 'entrance_exam';

    const PUBLIC_FILE_PATH = 'entrance_exam';

    protected $fillable = [
        'name',                                                                  // название ВИ
        'year',                                                                  // год ВИ
        'date_start',                                                            // срок записи ВИ
        'date_end',                                                              //
        'date_active',                                                           // показывать ввод срока записи
        'date_user_show',                                                        // показывать даты студику
        'date_employee_show',                                                    // показывать даты сотруднику

        'manual_active',                                                         // показывать ввод Методички
        'manual_user_show',                                                      //
        'manual_employee_show',                                                  //
        'statement_active',                                                      // показывать ввод Ведомости
        'statement_user_show',                                                   //
        'statement_employee_show',                                               //
        'commission_structure_active',                                           //
        'commission_structure_user_show',                                        //
        'commission_structure_employee_show',                                    //
        'composition_appeal_commission_active',                                  //
        'composition_appeal_commission_user_show',                               //
        'composition_appeal_commission_employee_show',                           //
        'schedule_active',                                                       //
        'schedule_user_show',                                                    //
        'schedule_employee_show',                                                //
        'protocols_creative_exams_active',                                       //
        'protocols_creative_exams_user_show',                                    //
        'protocols_creative_exams_employee_show',                                //
        'protocols_appeal_commission_active',                                    //
        'protocols_appeal_commission_user_show',                                 //
        'protocols_appeal_commission_employee_show',                             //
        'report_exams_active',                                                   // показывать дату Отчетов по творческим и специальным экзаменам
        'report_exams_user_show',                                                // показывать дату студику
        'report_exams_employee_show',                                            // показывать дату сотруднику
        'nct_number_active',                                                     // показывать ввод НЦТ
        'nct_number_user_show',                                                  // показывать НЦТ студику
        'nct_number_employee_show',                                              // показывать НЦТ сотруднику

        'passing_point',                                                         // проходной балл
        'passing_point_active',                                                  // показывать проходной балл
        'passing_point_user_show',                                               // показывать проходной балл студику
        'passing_point_employee_show',                                           // показывать проходной балл сотруднику

        'custom_checked_active',                                                 // показывать ручной ввод
        'custom_checked_user_show',                                              // показывать студику
        'custom_checked_employee_show',                                          // показывать сотруднику
    ];


    /**+
     * @param $value
     */
    public function setDateStartAttribute( $value )
    {
        $this->attributes['date_start'] = date('Y-m-d',strtotime( $value ));
    }


    /**
     * @param $value
     * @return false|null|string
     */
    public function getDateStartAttribute( $value )
    {
        if( !empty( $value ) )
        {
            return date('Y-m-d',strtotime( $value ));
        }
        return null;
    }


    /**
     * @param $value
     */
    public function setDateEndAttribute( $value )
    {
        $this->attributes['date_end'] = date('Y-m-d',strtotime( $value ));
    }


    /**
     * @param $value
     * @return false|null|string
     */
    public function getDateEndAttribute( $value )
    {
        if( !empty( $value ) )
        {
            return date('Y-m-d',strtotime( $value ));
        }
        return null;
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
     * @param $value
     */
    public function setDateActiveAttribute( $value )
    {
        if( is_string($value) && ( strcmp($value,'true') === 0 ) )
        {
            $this->attributes['date_active'] = 1;
        } else {

            $this->attributes['date_active'] = intval( $value );
        }
    }


    /**
     * @param $value
     */
    public function setManualActiveAttribute( $value )
    {
        if( is_string($value) && ( strcmp($value,'true') === 0 ) )
        {
            $this->attributes['manual_active'] = 1;
        } else {

            $this->attributes['manual_active'] = intval( $value );
        }
    }


    /**
     * @param $value
     */
    public function setNctNumberActiveAttribute( $value )
    {
        if( is_string($value) && ( strcmp($value,'true') === 0 ) )
        {
            $this->attributes['nct_number_active'] = 1;
        } else {

            $this->attributes['nct_number_active'] = intval( $value );
        }
    }


    /**
     * @param $value
     */
    public function setPassingPointActiveAttribute( $value )
    {
        if( is_string($value) && ( strcmp($value,'true') === 0 ) )
        {
            $this->attributes['passing_point_active'] = 1;
        } else {

            $this->attributes['passing_point_active'] = intval( $value );
        }
    }


    /**
     * @param $value
     */
    public function setStatementActiveAttribute( $value )
    {
        if( is_string($value) && ( strcmp($value,'true') === 0 ) )
        {
            $this->attributes['statement_active'] = 1;
        } else {

            $this->attributes['statement_active'] = intval( $value );
        }
    }


    /**
     * @param $value
     */
    public function setCommissionStructureActiveAttribute( $value )
    {
        if( is_string($value) && ( strcmp($value,'true') === 0 ) )
        {
            $this->attributes['commission_structure_active'] = 1;
        } else {

            $this->attributes['commission_structure_active'] = intval( $value );
        }
    }


    /**
     * @param $value
     */
    public function setCompositionAppealCommissionActiveAttribute( $value )
    {
        if( is_string($value) && ( strcmp($value,'true') === 0 ) )
        {
            $this->attributes['composition_appeal_commission_active'] = 1;
        } else {

            $this->attributes['composition_appeal_commission_active'] = intval( $value );
        }
    }


    /**
     * @param $value
     */
    public function setScheduleActiveAttribute( $value )
    {
        if( is_string($value) && ( strcmp($value,'true') === 0 ) )
        {
            $this->attributes['schedule_active'] = 1;
        } else {

            $this->attributes['schedule_active'] = intval( $value );
        }
    }


    /**
     * @param $value
     */
    public function setProtocolsCreativeExamsActiveAttribute( $value )
    {
        if( is_string($value) && ( strcmp($value,'true') === 0 ) )
        {
            $this->attributes['protocols_creative_exams_active'] = 1;
        } else {

            $this->attributes['protocols_creative_exams_active'] = intval( $value );
        }
    }


    /**
     * @param $value
     */
    public function setProtocolsAppealCommissionActiveAttribute( $value )
    {
        if( is_string($value) && ( strcmp($value,'true') === 0 ) )
        {
            $this->attributes['protocols_appeal_commission_active'] = 1;
        } else {

            $this->attributes['protocols_appeal_commission_active'] = intval( $value );
        }
    }


    /**
     * @param $value
     */
    public function setReportExamsActiveAttribute( $value )
    {
        if( is_string($value) && ( strcmp($value,'true') === 0 ) )
        {
            $this->attributes['report_exams_active'] = 1;
        } else {

            $this->attributes['report_exams_active'] = intval( $value );
        }
    }


    /**
     * @param $value
     */
    public function setCustomCheckedActiveAttribute( $value )
    {
        if( is_string($value) && ( strcmp($value,'true') === 0 ) )
        {
            $this->attributes['custom_checked_active'] = 1;
        } else {

            $this->attributes['custom_checked_active'] = intval( $value );
        }
    }


    /**
     * sync show fields
     * @param $aParams
     */
    public function syncFields( $aParams )
    {
        if( !empty($aParams) && is_array($aParams) && (count($aParams) > 0) )
        {
            if( !empty($this->id) && empty($aParams['date_user_show']) )
            {
                $this->date_user_show = 0;
            }
            if( !empty($this->id) && empty($aParams['date_employee_show']) )
            {
                $this->date_employee_show = 0;
            }
            if( !empty($this->id) && empty($aParams['manual_user_show']) )
            {
                $this->manual_user_show = 0;
            }
            if( !empty($this->id) && empty($aParams['manual_employee_show']) )
            {
                $this->manual_employee_show = 0;
            }
            if( !empty($this->id) && empty($aParams['statement_user_show']) )
            {
                $this->statement_user_show = 0;
            }
            if( !empty($this->id) && empty($aParams['statement_employee_show']) )
            {
                $this->statement_employee_show = 0;
            }
            if( !empty($this->id) && empty($aParams['commission_structure_user_show']) )
            {
                $this->commission_structure_user_show = 0;
            }
            if( !empty($this->id) && empty($aParams['commission_structure_employee_show']) )
            {
                $this->commission_structure_employee_show = 0;
            }
            if( !empty($this->id) && empty($aParams['composition_appeal_commission_user_show']) )
            {
                $this->composition_appeal_commission_user_show = 0;
            }
            if( !empty($this->id) && empty($aParams['composition_appeal_commission_employee_show']) )
            {
                $this->composition_appeal_commission_employee_show = 0;
            }
            if( !empty($this->id) && empty($aParams['schedule_user_show']) )
            {
                $this->schedule_user_show = 0;
            }
            if( !empty($this->id) && empty($aParams['schedule_employee_show']) )
            {
                $this->schedule_employee_show = 0;
            }
            if( !empty($this->id) && empty($aParams['protocols_creative_exams_user_show']) )
            {
                $this->protocols_creative_exams_user_show = 0;
            }
            if( !empty($this->id) && empty($aParams['protocols_creative_exams_employee_show']) )
            {
                $this->protocols_creative_exams_employee_show = 0;
            }
            if( !empty($this->id) && empty($aParams['protocols_appeal_commission_user_show']) )
            {
                $this->protocols_appeal_commission_user_show = 0;
            }
            if( !empty($this->id) && empty($aParams['protocols_appeal_commission_employee_show']) )
            {
                $this->protocols_appeal_commission_employee_show = 0;
            }
            if( !empty($this->id) && empty($aParams['report_exams_user_show']) )
            {
                $this->report_exams_user_show = 0;
            }
            if( !empty($this->id) && empty($aParams['report_exams_employee_show']) )
            {
                $this->report_exams_employee_show = 0;
            }
            if( !empty($this->id) && empty($aParams['nct_number_user_show']) )
            {
                $this->nct_number_user_show = 0;
            }
            if( !empty($this->id) && empty($aParams['nct_number_employee_show']) )
            {
                $this->nct_number_employee_show = 0;
            }
            if( !empty($this->id) && empty($aParams['passing_point_user_show']) )
            {
                $this->passing_point_user_show = 0;
            }
            if( !empty($this->id) && empty($aParams['passing_point_employee_show']) )
            {
                $this->passing_point_employee_show = 0;
            }
            if( !empty($this->id) && empty($aParams['custom_checked_user_show']) )
            {
                $this->custom_checked_user_show = 0;
            }
            if( !empty($this->id) && empty($aParams['custom_checked_employee_show']) )
            {
                $this->custom_checked_employee_show = 0;
            }

            if( !empty($aParams['removeFiles']) && (count($aParams['removeFiles']) > 0) )
            {
                EntranceExamFiles::removeFiles( $aParams['removeFiles'] );
            }

        }
    }


    /**
     * get self by id
     * @param $iId
     * @return bool
     */
    public static function getById( $iId )
    {
        $mResponse = false;
        if( !empty($iId) )
        {
            $oEE = self::
            where('id',$iId)->
            whereNull('deleted_at')->
            first();
            if( !empty($oEE) ){ $mResponse = $oEE; }
        }
        return $mResponse;
    }


    /**
     * remove by ids
     * @param array $aData
     * @return
     */
    public static function removeByIds( $aData )
    {
        if( !empty($aData['removeEntranceExam']) && (count($aData['removeEntranceExam']) > 0) )
        {
            $oRemove = self::whereIn('id',$aData['removeEntranceExam'])->delete();
        } elseif( !empty($aData) && (count($aData) > 0) )
        {
            $oRemove = self::whereIn('id',$aData)->delete();
        }
    }


    /**
     * @param $oFile
     * @param $sType
     */
    public function createFile( $oFile,$sType )
    {

        $sFilename = 'entrance_exam_' . $this->id . '_' . time() . rand(1, 1000) . '.' . pathinfo($oFile->getClientOriginalName(), PATHINFO_EXTENSION);

        $oEntranceExamFiles = new EntranceExamFiles();
        $oEntranceExamFiles->entrance_exam_id = $this->id;
        $oEntranceExamFiles->name = $oFile->getClientOriginalName();
        $oEntranceExamFiles->filename = $sFilename;
        $oEntranceExamFiles->type = $sType;

        if($oEntranceExamFiles->save())
        {
            $oFile->move(public_path(self::PUBLIC_FILE_PATH), $sFilename);
        }

    }


    /**
     * @param $aParams
     */
    public function saveFiles( $aParams )
    {
        if( !empty($aParams) )
        {
            if( !empty($aParams['manualFiles']['file']) )
            {
                $manualFiles = $aParams['manualFiles']['file'];
                foreach( $manualFiles as $manualItem )
                {
                    $this->createFile($manualItem,EntranceExamFiles::TYPE_FILE_MANUAL);
                }
            }
            if( !empty($aParams['statementFiles']['file']) )
            {
                $statementFiles = $aParams['statementFiles']['file'];
                foreach( $statementFiles as $statementItem )
                {
                    $this->createFile($statementItem,EntranceExamFiles::TYPE_FILE_STATEMENT);
                }
            }
            if( !empty($aParams['commissionStructureFiles']['file']) )
            {
                $commissionStructureFiles = $aParams['commissionStructureFiles']['file'];
                foreach( $commissionStructureFiles as $commissionStructureItem )
                {
                    $this->createFile($commissionStructureItem,EntranceExamFiles::TYPE_FILE_COMMISSION_STRUCTURE);
                }
            }
            if( !empty($aParams['compositionAppealCommissionFiles']['file']) )
            {
                $compositionAppealCommissionFiles = $aParams['compositionAppealCommissionFiles']['file'];
                foreach( $compositionAppealCommissionFiles as $compositionAppealCommissionFilesItem )
                {
                    $this->createFile($compositionAppealCommissionFilesItem,EntranceExamFiles::TYPE_FILE_COMMISSION_APPEAL_STRUCTURE);
                }
            }
            if( !empty($aParams['scheduleFiles']['file']) )
            {
                $scheduleFiles = $aParams['scheduleFiles']['file'];
                foreach( $scheduleFiles as $scheduleFilesItem)
                {
                    $this->createFile($scheduleFilesItem,EntranceExamFiles::TYPE_FILE_SCHEDULE);
                }
            }
            if( !empty($aParams['protocolsCreativeExamsFiles']['file']) )
            {
                $protocolsCreativeExamsFiles = $aParams['protocolsCreativeExamsFiles']['file'];
                foreach( $protocolsCreativeExamsFiles as $protocolsCreativeExamsFilesItem)
                {
                    $this->createFile($protocolsCreativeExamsFilesItem,EntranceExamFiles::TYPE_FILE_PROTOCOLS_CREATIVE_EXAMS);
                }
            }
            if( !empty($aParams['protocolsAppealCommissionFiles']['file']) )
            {
                $protocolsAppealCommissionFiles = $aParams['protocolsAppealCommissionFiles']['file'];
                foreach( $protocolsAppealCommissionFiles as $protocolsAppealCommissionFilesItem)
                {
                    $this->createFile($protocolsAppealCommissionFilesItem,EntranceExamFiles::TYPE_FILE_PROTOCOLS_APPEAL_COMMISSION);
                }
            }
            if( !empty($aParams['reportExamsFiles']['file']) )
            {
                $reportExamsFiles = $aParams['reportExamsFiles']['file'];
                foreach( $reportExamsFiles as $reportExamsFilesItem)
                {
                    $this->createFile($reportExamsFilesItem,EntranceExamFiles::TYPE_FILE_REPORT_EXAMS);
                }
            }

        }
    }


    /**
     * @return mixed
     */
    public function getFiles( $type=null )
    {
        $oEntranceExamFiles = EntranceExamFiles::where('entrance_exam_id',$this->id)->whereNull('deleted_at');
        if( !empty($type) ){ $oEntranceExamFiles->where('type',$type); }
        return $oEntranceExamFiles->get();
    }


    /**
     * is valid VI - проверка валидности ВИ
     * @param int $iCheckListId
     * @param int $iUserId
     * @return bool
     */
    public function isValid( $iCheckListId, $iUserId )
    {

        // проверка срока записи
        if( !$this->isDateValid() ){ return false; }

        // проверка регистрации в базе НЦТ
        if( !$this->isNCTValid( $iCheckListId ) ){ return false; }

        // проверка проходного балла
        if( !$this->isPointValid( $iCheckListId, $iUserId ) ){ return false; }

        // ручная проверка
        if( !$this->isCustomValid() ){ return false; }

        return true;
    }


    /**
     * проверка текущей даты на вхождение в период
     * @return bool
     */
    public function isDateValid()
    {
        if( !empty($this->date_active) )
        {
            // если не установлена одна из дат
            if( empty($this->date_start) || empty($this->date_end) )
            {
                return false;
            }
            // если текущая дата не входит в период
            if( ( strtotime( $this->date_start ) > strtotime(date('Y-m-d')) ) || ( strtotime( $this->date_end ) < strtotime(date('Y-m-d')) ) )
            {
                return false;
            }
        }
        return true;
    }


    /**
     * проверка данных в базе НЦТ
     * @param int $iCheckListId
     * @return bool
     */
    public function isNCTValid( $iCheckListId )
    {
        if( !empty($this->nct_number_active) )
        {
            $oCheckListExam = CheckListExam::
            where('check_list_id',$iCheckListId)->
            where('entrance_exam_id',$this->id)->
            whereNull('deleted_at')->
            first();
            if( empty($oCheckListExam) || empty($oCheckListExam->nct_number) || empty($oCheckListExam->nct_code) )
            {
                return false;
            }
        }
        return true;
    }


    /**
     * проверка проходного балла по ВИ
     * @param int $iCheckListId
     * @param int $iUserId
     * @return bool
     */
    public function isPointValid( $iCheckListId, $iUserId )
    {

        // если установлена галочка проверки проходного балла
        if( !empty( $this->passing_point_active ) )
        {

            // если не установлен проходной балл
            if( empty( $this->passing_point ) ) { return false; }

            // получаем специальность для ВИ
            $oCheckList = CheckList::
            where('id',$iCheckListId)->
            whereNull('deleted_at')->
            first();
            if( empty( $oCheckList ) )
            {
                return false;
            }

            $oEntranceExamUser = EntranceExamUser::
            where('speciality_id',$oCheckList->speciality_id)->
            where('entrance_exam_id',$this->id)->
            where('user_id',$iUserId)->
            whereNull('deleted_at')->
            first();
            if( empty( $oEntranceExamUser ) || ( $oEntranceExamUser->point < $this->passing_point ) )
            {
                // если нет баллов студика или балл студика меньше проходного балла
                return false;
            }
        }

        return true;
    }


    /**
     * ручная проверка
     * @return bool
     */
    public function isCustomValid()
    {
        if( !empty($this->custom_checked_active) )
        {
            if( empty($this->custom_checked) ){ return false; }
        }
        return true;
    }


    /**
     * @param int $iStartYear
     * @return array
     */
    public static function generateYearsForAdmin( $iStartYear=2017 )
    {
        $aResponse=[];
        for( $i=$iStartYear;$i<(intval(date('Y'))+3);$i++ )
        {
            $aResponse[] = $i;
        }
        return $aResponse;
    }


}

