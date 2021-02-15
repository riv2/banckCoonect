<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-11-01
 * Time: 21:03
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminSyllabusTaskEditTaskValidator extends Validator
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
                'id'                 => 'required',
                'model.syllabus_id'  => 'required',
                'model.type'         => 'required',
                'model.points'       => 'required|integer'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}
