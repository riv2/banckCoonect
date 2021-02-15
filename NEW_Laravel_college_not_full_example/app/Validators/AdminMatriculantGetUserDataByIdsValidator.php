<?php
/**
 * User: dadicc
 * Date: 20.08.19
 * Time: 10:37
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminMatriculantGetUserDataByIdsValidator extends Validator
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
                'ids' => 'required|array'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}