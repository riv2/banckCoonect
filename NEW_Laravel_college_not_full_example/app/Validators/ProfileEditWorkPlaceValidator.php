<?php
/**
 * User: dadicc
 * Date: 12.08.19
 * Time: 10:35
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class ProfileEditWorkPlaceValidator extends Validator
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
                'workplace' => 'max:1500'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}