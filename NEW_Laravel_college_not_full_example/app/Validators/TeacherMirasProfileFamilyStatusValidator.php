<?php
/**
 * User: dadicc
 * Date: 02.08.19
 * Time: 7:50
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class TeacherMirasProfileFamilyStatusValidator extends Validator
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
                'family_status' => 'required|string|max:50',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}