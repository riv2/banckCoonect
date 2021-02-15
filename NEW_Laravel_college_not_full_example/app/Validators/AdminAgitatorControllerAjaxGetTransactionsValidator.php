<?php
/**
 * User: dadicc
 * Date: 2/4/20
 * Time: 5:37 PM
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminAgitatorControllerAjaxGetTransactionsValidator extends Validator
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
                '_token'  => 'required'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}