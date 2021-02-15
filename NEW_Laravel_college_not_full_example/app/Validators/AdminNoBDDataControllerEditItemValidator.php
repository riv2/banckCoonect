<?php
/**
 * User: dadicc
 * Date: 4/8/20
 * Time: 8:30 PM
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminNoBDDataControllerEditItemValidator extends Validator
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
                'type'  => 'required|string',
                'model' => 'required',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}