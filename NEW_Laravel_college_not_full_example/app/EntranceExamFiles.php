<?php
/**
 * User: dadicc
 * Date: 2/28/20
 * Time: 12:39 PM
 */

namespace App;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use Illuminate\Support\Facades\{DB,Log};

class EntranceExamFiles extends Model
{

    use SoftDeletes;

    protected $table = 'entrance_exam_files';

    const PUBLIC_FILE_PATH = 'entrance_exam';

    protected $fillable = [
        'entrance_exam_id',
        'speciality_id',
        'name',
        'filename',
        'user_show',
        'employee_show',
        'type',
    ];


    /**
     * file type
     */
    const TYPE_FILE_MANUAL                      = 'manual';                          // методичка
    const TYPE_FILE_STATEMENT                   = 'statement';                       // ведомость
    const TYPE_FILE_COMMISSION_STRUCTURE        = 'commission_structure';            // состав комиссии
    const TYPE_FILE_COMMISSION_APPEAL_STRUCTURE = 'composition_appeal_commission';   // состав аппеляционной комиссии
    const TYPE_FILE_SCHEDULE                    = 'schedule';                        // расписание
    const TYPE_FILE_PROTOCOLS_CREATIVE_EXAMS    = 'protocols_creative_exams';        // общие протоколы по результатам творческих экзаменов
    const TYPE_FILE_PROTOCOLS_APPEAL_COMMISSION = 'protocols_appeal_commission';     // общие протоколы по апелляционной комиссии
    const TYPE_FILE_REPORT_EXAMS                = 'report_exams';                    // отчеты по творческим и (или) специальным экзаменам направленных в МОН РК


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function entranceExam()
    {
        return $this->hasOne(EntranceExam::class, 'id', 'entrance_exam_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function speciality()
    {
        return $this->hasOne(Speciality::class, 'id', 'speciality_id');
    }


    /**
     * @return bool|mixed|string
     */
    public function getPublicUrl()
    {
        $fileName = config('app.url') . '/entrance_exam/' . $this->filename;
        if(!$fileName)
        {
            return false;
        }
        return $fileName;
    }


    /**
     * @param $aIds
     */
    public static function removeFiles( $aIds )
    {
        if( !empty($aIds) && is_array($aIds) && (count($aIds) > 0) )
        {
            foreach( $aIds as $item )
            {
                $oModel = self::
                where('id',$item)->
                whereNull('deleted_at')->
                first();
                if( !empty($oModel) )
                {
                    if( file_exists( public_path(self::PUBLIC_FILE_PATH).'/'. $oModel->filename ) )
                    {
                        unlink( public_path(self::PUBLIC_FILE_PATH).'/'. $oModel->filename);
                    }
                    $oModel->delete();
                }
                unset($oModel);
            }
        }
    }


}

