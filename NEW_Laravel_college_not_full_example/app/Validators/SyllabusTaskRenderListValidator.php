<?php
/**
 * User: dadicc
 * Date: 3/16/20
 * Time: 3:34 PM
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class SyllabusTaskRenderListValidator extends Validator
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
                'discipline_id' => 'required|integer',
                'lang'          => 'required|string',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}