<?php
/**
 * User: dadicc
 * Date: 04.08.19
 * Time: 22:39
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class TeacherMirasProfilePhoneCodeApprove extends Validator
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
                'code' => 'required|numeric',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}