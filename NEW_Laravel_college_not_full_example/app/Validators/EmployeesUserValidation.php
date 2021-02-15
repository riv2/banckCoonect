<?php
/**
 * User: dadicc
 * Date: 17.10.19
 * Time: 9:46
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class EmployeesUserValidation extends Validator
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
                'iin'                  => 'required|string|max:12',
                'email'                => 'required|email|max:255',
                'mobile_phone'         => 'required|min:7|max:255',
                'home_phone'           => 'nullable|max:255',
                'bdate'                => 'required|date',
                'nationality_id'       => 'required|integer',
                'sex'                  => 'required|integer|digits:1',
                'family_status'        => 'required|max:255',
                'docnumber'            => 'required|integer|digits_between:1,12',
                'doctype'              => 'required|string|max:255',
                'issuedate'            => 'required|date',
                'expire_date'          => 'required|date',
                'issuing'              => 'required|string|max:255',
                'citizenship'          => 'required|string|max:255',
                'address_registration' => 'required|string|max:255',
                'address_residence'    => 'required|string|max:255',
                'status'               => 'required|string',
                'user_id'              => 'required'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}

