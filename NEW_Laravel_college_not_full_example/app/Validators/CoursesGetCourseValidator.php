<?php
/**
 * User: dadicc
 * Date: 22.10.19
 * Time: 9:42
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class CoursesGetCourseValidator extends Validator
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
                'course' => 'required|integer'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}