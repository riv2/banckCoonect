<?php
/**
 * User: dadicc
 * Date: 3/7/20
 * Time: 10:45 PM
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminEntranceExamEditPostValidator extends Validator
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
                '_token'       => 'required',
                'year'         => 'required|string|max:4',
                'name'         => 'required|string|max:255',
                'date_start'   => 'string|max:12',
                'date_end'     => 'string|max:12',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}