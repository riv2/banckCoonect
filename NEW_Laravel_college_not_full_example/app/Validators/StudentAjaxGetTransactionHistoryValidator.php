<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-09-06
 * Time: 7:55
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class StudentAjaxGetTransactionHistoryValidator extends Validator
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
                'iin'        => 'required|string|max:255',
                'date_from'  => 'required',
                'date_to'    => 'required'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}

