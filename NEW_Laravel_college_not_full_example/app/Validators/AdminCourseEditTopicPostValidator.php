<?php
/**
 * User: dadicc
 * Date: 17.10.19
 * Time: 11:46
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminCourseEditTopicPostValidator extends Validator
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
                'course_id'      => 'required',
                'title'          => 'required|string|max:255',
                'language'       => 'required',
                'resource_file'  => 'file',
                'resource_link'  => 'max:255',
                'questions'      => 'max:1500'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}