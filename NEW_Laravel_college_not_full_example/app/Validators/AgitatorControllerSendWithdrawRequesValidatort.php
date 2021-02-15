<?php
/**
 * User: dadicc
 * Date: 1/23/20
 * Time: 6:23 PM
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AgitatorControllerSendWithdrawRequesValidatort extends Validator
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
                'iban'      => 'required|string|max:255',
                'bank_id'   => 'required',
                'cost'      => 'required|integer',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}