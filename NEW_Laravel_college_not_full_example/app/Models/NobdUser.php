<?php
/**
 * User: dadicc
 * Date: 4/9/20
 * Time: 6:14 PM
 */

namespace App\Models;

use App\Models\{NobdUserPc};
use App\{User};
use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use Illuminate\Support\Facades\{DB,Log};

class NobdUser extends Model
{

    use SoftDeletes;

    protected $table = 'nobd_user';

    protected $fillable = [
        "user_id",
        "study_exchange",                                      // студент обучается по обмену
        "host_country",                                        // принимающая страна
        "host_university_name",                                // Наименование принимающего зарубежного вуза-партнера
        "host_university_language",                            // Язык обучения в принимающем вузе
        "exchange_specialty",                                  // Специальность по обмену
        "exchange_specialty_st",                               // Специальность по обмену
        "exchange_date_start",                                 // Начало срока пребывания по обмену
        "exchange_date_end",                                   // Окончание срока пребывания
        "academic_mobility",                                   // Академическая мобильность
        "academic_leave",                                      // Находится в академическом отпуске
        "academic_leave_order_number",                         // Номер приказа о предоставлении обучающемуся академического отпуска
        "academic_leave_order_date",                           // Дата приказа о предоставлении обучающемуся академического отпуска
        "academic_leave_out_order_number",                     // Номер приказа о выходе обучающегося из академического отпуска
        "academic_leave_out_order_date",                       // Дата приказа о выходе обучающегося из академического отпуска
        "is_national_student_league",                          // Участвует в Национальной студенческой лиге
        "is_world_winter_universiade",                         // Участвует во всемирной зимней Универсиаде
        "is_world_summer_universiade",                         // Участвует во всемирной летней Универсиаде
        "is_winter_universiade_republic_kz",                   // Участвует в зимней Универсиаде Республики Казахстан
        "is_summer_universiade_republic_kz",                   // Участвует в летней Универсиаде Республики Казахстан
        "is_nonresident_student",                              // Иногородний студент
        "is_needs_hostel",                                     // Нуждается в общежитии
        "is_lives_hostel",                                     // Проживает в общежитии
        "payment_type",                                        // Обучение за счет средств
        "cost_education",                                      // Стоимость обучения (за год), тысяч тенге
        "number_grant_certificate",                            // Номер свидетельства об присуждении гранта
        "trained_quota",                                       // Обучается по квоте
        "cause_stay_year",                                     // Оставлен на повторный курс
        "is_participation_competitions",                       // Участие в соревнованиях
        "is_orphan",                                           // Сирота
        "is_child_without_parents",                            // Ребенок оставшийся без попечения родителей
        "is_invalid",                                          // Инвалид
        "disability_group",                                    // Группа инвалидности
        "type_violation",                                      // Виды нарушений
        "conclusion_pmpc",                                     // Заключение ПМПК
        "conclusion_date",                                     // Дата заключения
        "is_thesis_defense",                                   // С защитой диссертации
        "form_diplom",                                         // Вид диплома
        "diplom_series",                                       // Серия диплома
        "diplom_number",                                       // Номер диплома
        "date_disposal",                                       // Дата выбытия
        "number_disposal_order",                               // Номер приказа выбытия
        "reason_disposal",                                     // Причина выбытия
        "employment_opportunity"                               // Трудоустройство
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pc()
    {
        return $this->hasMany(NobdUserPc::class, 'nobd_user_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function studyExchangeRef()
    {
        return $this->hasOne(NobdStudyExchange::class, 'id', 'study_exchange');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function languageRef()
    {
        return $this->hasOne(NobdLanguage::class, 'id', 'host_university_language');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function exchangeSpecialtyRef()
    {
        return $this->hasOne(NobdExchangeSpecialty::class, 'id', 'exchange_specialty');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function academicMobilityRef()
    {
        return $this->hasOne(NobdAcademicMobility::class, 'id', 'academic_mobility');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function academicLeaveRef()
    {
        return $this->hasOne(NobdAcademicLeave::class, 'id', 'academic_leave');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function paymentTypeRef()
    {
        return $this->hasOne(NobdPaymentType::class, 'id', 'payment_type');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function trainedQuotaRef()
    {
        return $this->hasOne(NobdTrainedQuota::class, 'id', 'trained_quota');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function causeStayYearRef()
    {
        return $this->hasOne(NobdCauseStayYear::class, 'id', 'cause_stay_year');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function disabilityGroupRef()
    {
        return $this->hasOne(NobdDisabilityGroup::class, 'id', 'disability_group');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function typeViolationRef()
    {
        return $this->hasOne(NobdTypeViolation::class, 'id', 'type_violation');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function formDiplomRef()
    {
        return $this->hasOne(NobdFormDiplom::class, 'id', 'form_diplom');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function reasonDisposalRef()
    {
        return $this->hasOne(NobdReasonDisposal::class, 'id', 'reason_disposal');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function employmentOpportunityRef()
    {
        return $this->hasOne(NobdEmploymentOpportunity::class, 'id', 'employment_opportunity');
    }


    /**
     * @param $value
     */
    public function setExchangeDateStartAttribute( $value )
    {
        if( !empty($value) && ($value != '') )
        {
            $this->attributes['exchange_date_start'] = date('Y-m-d',strtotime( $value ));
        }
    }


    /**
     * @param $value
     */
    public function setExchangeDateEndAttribute( $value )
    {
        if( !empty($value) && ($value != '') )
        {
            $this->attributes['exchange_date_end'] = date('Y-m-d', strtotime($value));
        }
    }


    /**
     * @param $value
     */
    public function setAcademicLeaveOrderDateAttribute( $value )
    {
        if( !empty($value) && ($value != '') )
        {
            $this->attributes['academic_leave_order_date'] = date('Y-m-d', strtotime($value));
        }
    }


    /**
     * @param $value
     */
    public function setAcademicLeaveOutOrderDateAttribute( $value )
    {
        if( !empty($value) && ($value != '') )
        {
            $this->attributes['academic_leave_out_order_date'] = date('Y-m-d', strtotime($value));
        }
    }


    /**
     * @param $value
     */
    public function setConclusionDateAttribute( $value )
    {
        if( !empty($value) && ($value != '') )
        {
            $this->attributes['conclusion_date'] = date('Y-m-d', strtotime($value));
        }
    }


    /**
     * @param $value
     */
    public function setDateDisposalAttribute( $value )
    {
        if( !empty($value) && ($value != '') )
        {
            $this->attributes['date_disposal'] = date('Y-m-d', strtotime($value));
        }
    }


    /**
     * @param $value
     */
    public function setStudyExchangeAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['study_exchange'] = $value;
        }
    }


    /**
     * @param $value
     */
    public function setHostCountryAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['host_country'] = $value;
        }
    }


    /**
     * @param $value
     */
    public function setHostUniversityLanguageAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['host_university_language'] = $value;
        }
    }


    /**
     * @param $value
     */
    public function setExchangeSpecialtyAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['exchange_specialty'] = $value;
        }
    }


    /**
     * @param $value
     */
    public function setAcademicMobilityAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['academic_mobility'] = $value;
        }
    }


    /**
     * @param $value
     */
    public function setAcademicLeaveAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['academic_leave'] = $value;
        }
    }


    /**
     * @param $value
     */
    public function setPaymentTypeAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['payment_type'] = $value;
        }
    }


    /**
     * @param $value
     */
    public function setTrainedQuotaAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['trained_quota'] = $value;
        }
    }


    /**
     * @param $value
     */
    public function setCauseStayYearAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['cause_stay_year'] = $value;
        }
    }


    /**
     * @param $value
     */
    public function setDisabilityGroupAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['disability_group'] = $value;
        }
    }


    /**
     * @param $value
     */
    public function setTypeViolationAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['type_violation'] = $value;
        }
    }


    /**
     * @param $value
     */
    public function setFormDiplomAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['form_diplom'] = $value;
        }
    }


    /**
     * @param $value
     */
    public function setReasonDisposalAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['reason_disposal'] = $value;
        }
    }


    /**
     * @param $value
     */
    public function setEmploymentOpportunityAttribute( $value )
    {
        if( !empty($value) && ($value != '') && ($value != '...') )
        {
            $this->attributes['employment_opportunity'] = $value;
        }
    }


    /**
     * sync show fields
     * @param $aParams
     */
    public function syncFields( $aParams )
    {
        if (!empty($aParams) && is_array($aParams) && (count($aParams) > 0)) {
            if (!empty($this->is_national_student_league) && empty($aParams['is_national_student_league'])) {
                $this->is_national_student_league = 0;
            }
            if (!empty($this->is_world_winter_universiade) && empty($aParams['is_world_winter_universiade'])) {
                $this->is_world_winter_universiade = 0;
            }
            if (!empty($this->is_world_summer_universiade) && empty($aParams['is_world_summer_universiade'])) {
                $this->is_world_summer_universiade = 0;
            }
            if (!empty($this->is_winter_universiade_republic_kz) && empty($aParams['is_winter_universiade_republic_kz'])) {
                $this->is_winter_universiade_republic_kz = 0;
            }
            if (!empty($this->is_summer_universiade_republic_kz) && empty($aParams['is_summer_universiade_republic_kz'])) {
                $this->is_summer_universiade_republic_kz = 0;
            }
            if (!empty($this->is_nonresident_student) && empty($aParams['is_nonresident_student'])) {
                $this->is_nonresident_student = 0;
            }
            if (!empty($this->is_needs_hostel) && empty($aParams['is_needs_hostel'])) {
                $this->is_needs_hostel = 0;
            }
            if (!empty($this->is_lives_hostel) && empty($aParams['is_lives_hostel'])) {
                $this->is_lives_hostel = 0;
            }
            if (!empty($this->is_participation_competitions) && empty($aParams['is_participation_competitions'])) {
                $this->is_participation_competitions = 0;
            }
            if (!empty($this->is_orphan) && empty($aParams['is_orphan'])) {
                $this->is_orphan = 0;
            }
            if (!empty($this->is_child_without_parents) && empty($aParams['is_child_without_parents'])) {
                $this->is_child_without_parents = 0;
            }
            if (!empty($this->is_invalid) && empty($aParams['is_invalid'])) {
                $this->is_invalid = 0;
            }
            if (!empty($this->is_thesis_defense) && empty($aParams['is_thesis_defense'])) {
                $this->is_thesis_defense = 0;
            }
        }
    }


    /**
     * @param $params
     */
    public function updateNobdUserPc( $params )
    {
        if( !empty($params['nobdUserPc']) && (count($params['nobdUserPc']) > 0) )
        {
            foreach($params['nobdUserPc'] as $item)
            {
                $id = intval($item['id']);
                if( $id != 0 )
                {
                    $oNobdUserPc = NobdUserPc::where('id',$id)->whereNull('deleted_at')->first();
                } else {
                    $oNobdUserPc = new NobdUserPc();
                }
                if( !empty($oNobdUserPc) )
                {
                    $oNobdUserPc->user_id = $this->user_id;
                    $oNobdUserPc->nobd_user_id = $this->id;
                    $oNobdUserPc->fill($item);
                    $oNobdUserPc->save();
                }
            }
        }
    }


    /**
     * @param $aIds
     */
    public function removeNobdUserPc( $aIds )
    {

        if( !empty($aIds) && (count($aIds) > 0) )
        {
            $oNobdUserPc = NobdUserPc::
            whereIn('id',$aIds)->
            whereNull('deleted_at')->
            get();
            if( !empty($oNobdUserPc) && (count($oNobdUserPc) > 0) )
            {
                foreach($oNobdUserPc as $item)
                {
                    $item->delete();
                }
            }
        }

    }



}