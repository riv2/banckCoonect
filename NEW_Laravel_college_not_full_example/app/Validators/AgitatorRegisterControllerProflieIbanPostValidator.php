<?php
/**
 * User: dadicc
 * Date: 1/14/20
 * Time: 6:52 PM
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AgitatorRegisterControllerProflieIbanPostValidator extends Validator
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
                'iban'      => 'required|string|min:20|max:20',
                'bank_id'   => 'required',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}