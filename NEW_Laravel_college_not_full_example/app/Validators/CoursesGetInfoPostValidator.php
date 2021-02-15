<?php
/**
 * User: dadicc
 * Date: 23.10.19
 * Time: 10:27
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class CoursesGetInfoPostValidator extends Validator
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
                'courses_id' => 'required|integer',
                'language'   => 'required|string|max:255',
                'schedule'   => 'max:300',
                'cost'       => 'required|integer',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}