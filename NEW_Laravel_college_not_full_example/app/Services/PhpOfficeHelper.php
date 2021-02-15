<?php
/**
 * User: dadicc
 * Date: 04.07.19
 * Time: 8:14
 */

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Services\PHPWord_Template;

class PhpOfficeHelper
{

    /**
     * add table to admin order
     * @param $sFileName
     * @param $aData
     * @param string $sFormat
     * @return string
     * @throws \Exception
     */
    public static function addTableForOrder($sFileName, $oData, $sFormat = 'docx')
    {

        $oPhpWord = new PHPWord_Template( $sFileName );
        $oPhpWord->cloneRow('fio',count($oData));
        foreach( $oData as $iKey => $oUser )
        {
            $oProfile = $oUser->studentProfile;
            $oPhpWord->setValue('fio#' . ($iKey+1), $oProfile->fio );
            $oPhpWord->setValue('speciality#' . ($iKey+1), $oProfile->speciality->name );
            $oPhpWord->setValue('education#' . ($iKey+1), __($oProfile->education_study_form) );
            unset( $oProfile );
        }
        return $oPhpWord->save();

    }

    /**
     * add table profile note opis list
     * @param $sFileName
     * @param $aData
     * @param string $sFormat
     * @return string
     * @throws \Exception
     */
    public static function addTableForProfileNoteOpisList($sFileName, $oData, $sFormat = 'docx')
    {

        $oPhpWord = new PHPWord_Template( $sFileName );
        $oPhpWord->cloneRow('t_num',count($oData));
        foreach( $oData as $iKey => $oItem)
        {
            $oPhpWord->setValue('t_num#' . ($iKey+1), ($iKey+1) );
            $oPhpWord->setValue('t_name#' . ($iKey+1), $oItem );
        }
        return $oPhpWord->save();

    }


}
