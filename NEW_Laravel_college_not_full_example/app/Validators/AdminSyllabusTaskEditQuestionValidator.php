<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-11-04
 * Time: 0:09
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminSyllabusTaskEditQuestionValidator extends Validator
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
                'id'               => 'required',
                'model.task_id'    => 'required',
                'model.points'     => 'required|integer',
                'model.question'   => 'required'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}