<?php
/**
 * User: Vlad
 * Date: 2020-01
 * Time: 7:55
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class UserVacancyFormValidation extends Validator
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
        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}

