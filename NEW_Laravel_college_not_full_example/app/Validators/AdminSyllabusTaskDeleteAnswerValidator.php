<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-11-04
 * Time: 2:27
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminSyllabusTaskDeleteAnswerValidator extends Validator
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
                'answer_id' => 'required'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}