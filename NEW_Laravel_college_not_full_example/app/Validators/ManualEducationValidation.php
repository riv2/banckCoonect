<?php
/**
 * User: vlad karpenko
 * Date: 3.12.19
 * Time: 9:46
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class ManualEducationValidation extends Validator
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
                'name'          => 'required|string|max:255',
                'name_en'       => 'required|string|max:255',
                'name_kz'       => 'required|string|max:255',
                'short_name'    => 'required|string|max:255',
                'short_name_en' => 'required|string|max:255',
                'short_name_kz' => 'required|string|max:255',
                'type'          => 'required|string|max:255'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}

