<?php
/**
 * User: vlad
 * Date: 17.10.19
 * Time: 9:46
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class EmployeesUserSocialPackageValidation extends Validator
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
                'gas'       => 'nullable|numeric',
                'basket'    => 'nullable|numeric',
                'medicines' => 'nullable|numeric',
                'cellular'  => 'nullable|numeric',
                'taxi'	    => 'nullable|numeric'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}

