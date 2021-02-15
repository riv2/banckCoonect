<?php
/**
 * User: dadicc
 * Date: 2/5/20
 * Time: 11:13 AM
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminAgitatorControllerAjaxChangeTransactionStatusValidator extends Validator
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
                '_token'        => 'required',
                'transaction'   => 'required',
                'status'        => 'required'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}

