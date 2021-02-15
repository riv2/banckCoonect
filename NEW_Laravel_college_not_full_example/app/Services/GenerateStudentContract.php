<?php

namespace App\Services;

use App\User;
use App\Profiles;
use App\Speciality;
use Illuminate\Support\Facades\{Response};
use Storage;


class GenerateStudentContract
{
    /**
     * generate education statement
     * @param
     * @return array
     * @throws \Exception
     */
    static public function printEducationStatementGenParams($id)
    {
        $user = User::where('id', $id)->first();

        $profile = $user->studentProfile;
        $application = null;
        $educationLevel = '';

        if($profile->speciality->code_char == 'b')
        {
            $application = $user->bcApplication;
            $educationLevel = 'bachelor_origin';
        }

        if($profile->speciality->code_char == 'm')
        {
            $application = $user->mgApplication;
            $educationLevel = 'master_origin';
        }

        if(!$application)
        {
            abort(404);
        }

        $currentLocale = app()->getLocale();

        app()->setLocale('ru');
        $educationLevel = __($educationLevel);
        $educationForm = __($profile->education_study_form);
        $studyLang = __($profile->education_lang);
        $gender = ($profile->sex === 1) ? __('gender_male') : __('gender_female');
        $nationalityRu = $profile->nationality_ru;

        app()->setLocale('kz');
        $educationLevelKz = __($educationLevel);
        $educationFormKz = __($profile->education_study_form);
        $studyLangKz = __($profile->education_lang);
        $genderKz = ($profile->sex === 1) ? __('gender_male') : __('gender_female');
        $nationalityKz = $profile->nationality_kz;

        app()->setLocale($currentLocale);

        $params = [
            '${t_fio}'                     => $profile->fio,
            '${t_address}'                 => implode(', ', [
                $application->region->name ?? '',
                $application->city->name ?? '',
                $application->street ?? '',
                $application->building_number ?? '',
                $application->apartment_number ?? ''
            ] ),
            '${t_mobile}'                  => $profile->mobile,
            '${t_education_place_name}'    => $application->nameeducation,
            '${t_education_doc_num}'       => $application->numeducation,
            '${t_education_level_kz}'      => $educationLevelKz,
            '${t_education_level}'         => $educationLevel,
            '${t_education_form_kz}'       => $educationFormKz,
            '${t_education_form}'          => $educationForm,
            '${t_trend_kz}'                => $profile->speciality->trend->name_kz ?? '',
            '${t_trend}'                   => $profile->speciality->trend->name ?? '',
            '${t_speciality_kz}'           => $profile->speciality->name_kz ?? '',
            '${t_speciality}'              => $profile->speciality->name ?? '',
            '${t_bdate}'                   => date('d.m.Y', strtotime($profile->bdate)),
            '${t_nationality}'             => $nationalityRu,
            '${t_nationality_kz}'          => $nationalityKz,
            '${t_study_language_kz}'       => $studyLangKz,
            '${t_study_language}'          => $studyLang,
            '${t_year}'                    => date('Y', time()),
            '${t_gender_kz}'               => $genderKz,
            '${t_gender}'                  => $gender
        ];

        return $params;
    }

    /**
     * generate education statement
     * @param
     * @return string
     * @throws \Exception
     */
    static public function printEducationStatement($id)
    {
        $params = self::printEducationStatementGenParams($id);

        $file = DocxHelper::replace(resource_path('docx/education_statement_template.docx'), $params, 'docx');

        return Response::download($file, 'Заявление.docx')->deleteFileAfterSend(true);
    }

    /**
     * generate education statement
     * @param
     * @return string
     * @throws \Exception
     */
    static public function saveEducationStatement($id)
    {
        $params = self::printEducationStatementGenParams($id);

        $file = DocxHelper::replace(resource_path('docx/education_statement_template.docx'), $params, 'docx');
        $newFile = storage_path() . '/docxReplace/' .$id . ' '. $params['${t_fio}'] . '.docx';
        
        return rename($file, $newFile);
        
    }

    /**
     * generate education contract
     * @param
     * @return string
     * @throws \Exception
     */
    static public function generateEducationContract($id)
    {
        $user = User::where('id', $id)->first();

        $aConfigCost = config('education_prices');

        $oProfile = $user->studentProfile;
        $sAlien = '';
        $sSpeciality = '';
        $oApplication = null;
        $sLocale = Profiles::EDUCATION_LANG_RU;

        if( !empty( $oProfile->education_lang ) && ( $oProfile->education_lang == Profiles::EDUCATION_LANG_KZ ) )
        {
            $sLocale = Profiles::EDUCATION_LANG_KZ;
        }

        // иностранец / резидент
        if( $oProfile->alien == true )
        {
            $sAlien = 'alien';
        } else {
            $sAlien = 'resident';
        }

        if( !empty( $user->bcApplication )  )
        {
            $oApplication = $user->bcApplication;
            $obSpeciality = Speciality::
            where('id',$oProfile->speciality->id)->
            whereIn('trend_id',$aConfigCost['trends_desing_ids'])->
            first();

            if( !empty($obSpeciality) )
            {
                $sSpeciality = 'b_design';
            } else {
                $sSpeciality = 'b';
            }
        }

        if( !empty( $user->mgApplication ) )
        {
            $oApplication = $user->mgApplication;
            $sSpeciality = 'm';
        }

        if(!$oApplication)
        {
            abort(404);
        }

        $currentLocale = app()->getLocale();
        app()->setLocale('ru');
        app()->setLocale($currentLocale);

        $iCurDate =  time();
        if( $iCurDate > strtotime('25-08-2019') )
        {
            $iCurDate = strtotime('25-08-2019');
        }

        $params = [
            '${t_user_id}'                 => $user->id,
            '${t_fio}'                     => $oProfile->fio,
            '${t_specialty}'               => $oProfile->speciality->trend->name ?? '',
            '${t_specialty_kz}'            => $oProfile->speciality->trend->name_kz ?? '',
            '${t_cost}'                    => !empty($aConfigCost[$sAlien][$sSpeciality]) ? intval($aConfigCost[$sAlien][$sSpeciality]) : '',
            '${t_iin}'                     => $oProfile->iin,
            '${t_identification_id}'       => $oProfile->docnumber,                                    // удостоверение номер
            '${t_date}'                    => date('d.m.Y',strtotime($oProfile->issuedate)),   // от дата
            '${t_given}'                   => $oProfile->issuing,                                      // выдан
            '${t_address}'                 => implode(', ', [
                $oApplication->region->name ?? '',
                $oApplication->city->name ?? '',
                $oApplication->street ?? '',
                $oApplication->apartment_number ?? ''
            ] ),
            '${t_phone}'                   => $oProfile->mobile,
            '${t_date_create}'             => date('d-m-Y',$iCurDate),
        ];

        $file = DocxHelper::replace(resource_path('docx/education_contract_template_'.$sLocale.'.docx'), $params, 'docx');

        return Response::download($file, 'Договор.docx')->deleteFileAfterSend(true);

    }
}