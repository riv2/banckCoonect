<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-09-25
 * Time: 19:35
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class ProfileProfileIDPostValidator extends Validator
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
                'front'  => 'required|file',
                'back'   => 'required|file',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}