<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-09-23
 * Time: 13:34
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class BuyServiceBuyWifiPackageValidator extends Validator
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
                'code' => 'required|string|max:255',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}