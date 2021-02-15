<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-10-08
 * Time: 17:37
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class LoginAjaxResetPasswordValidator extends Validator
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
                'password' => 'required|string|max:50',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}