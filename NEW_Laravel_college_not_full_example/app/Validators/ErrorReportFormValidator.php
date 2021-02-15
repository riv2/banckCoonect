<?php
/**
 * User: dadicc
 * Date: 09.07.19
 * Time: 14:47
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class ErrorReportFormValidator extends Validator
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
                'user_id'  => 'string|max:20',
                'fio'      => 'max:150',
                'phone'    => 'required|string|max:150',
                'message'  => 'required|string'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}