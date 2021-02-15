<?php
/**
 * User: dadicc
 * Date: 03.09.19
 * Time: 11:25
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminDiscountRequestSetStatusEmailValidator extends Validator
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
                'email'  => 'required|email'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}