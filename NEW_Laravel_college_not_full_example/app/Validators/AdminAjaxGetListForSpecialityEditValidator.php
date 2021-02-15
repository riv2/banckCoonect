<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-12-01
 * Time: 13:55
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminAjaxGetListForSpecialityEditValidator extends Validator
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
                '_token' => 'required|string|max:255'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}