<?php
/**
 * User: dadicc
 * Date: 21.10.19
 * Time: 8:57
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminCourseTopicDeleteValidator extends Validator
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
                'id'      => 'required|integer',
                'course'  => 'required|integer'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}