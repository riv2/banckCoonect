<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-10-31
 * Time: 16:39
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminSyllabusTaskGetListValidator extends Validator
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
                'discipline_id' => 'required',
                'language'      => 'required'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}