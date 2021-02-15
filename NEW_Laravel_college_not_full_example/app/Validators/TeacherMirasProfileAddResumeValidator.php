<?php
/**
 * User: dadicc
 * Date: 05.08.19
 * Time: 8:14
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class TeacherMirasProfileAddResumeValidator extends Validator
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
                'resume_link' => 'max:255',
                'resume_file' => 'file|max:5120'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}