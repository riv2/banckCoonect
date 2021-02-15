<?php
/**
 * User: dadicc
 * Date: 11/15/19
 * Time: 12:23 AM
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class SyllabusTaskGetListValidator extends Validator
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
                'discipline_id' => 'required|integer'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}