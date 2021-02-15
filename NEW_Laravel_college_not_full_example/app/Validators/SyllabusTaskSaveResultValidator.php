<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-11-19
 * Time: 13:45
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class SyllabusTaskSaveResultValidator extends Validator
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
                'questionList'                  => 'required|array',
                'questionList.syllabus_id'      => 'required',
                'questionList.task_id'          => 'required',
                'questionList.payed'            => 'required',
                'questionList.answers'          => 'array',
                'discipline_id'                 => 'required'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }
}