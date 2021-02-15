<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-09-24
 * Time: 16:32
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class StudyAjaxPrintBuyServiceValidator extends Validator
{

    /**
     * validation data
     * @param array $aData
     * @param array|null $aRuleList
     * @param array $aMessageList
     * @param array $aCustomAttributeList
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function make(array $aData, array $aRuleList = null, array $aMessageList = [], array $aCustomAttributeList = [])
    {

        $aRuleList = $aRuleList ? $aRuleList :
            [
                'service' => 'required',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}