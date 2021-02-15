<?php
/**
 * User: vlad
 * Date: 17.10.19
 * Time: 9:46
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class ManualPerksValidation extends Validator
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
                'name'  => 'required|string|max:255',
                'value' => 'required|integer'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}

