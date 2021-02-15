<?php
/**
 * User: dadicc
 * Date: 17.10.19
 * Time: 9:46
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class EmployeesUserPositionsValidation extends Validator
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

        $array = [
            'schedule'                 => 'required|integer', 
            'payroll_type'             => 'required|string|max:255',
            'organization'             => 'required|integer',
            'salary'                   => 'required|numeric',
            'price'                    => 'required|numeric',
            'employment'               => 'required|string|max:255',
            'premium'                  => 'nullable|numeric',
            'perks'                    => 'nullable',
            'employment_form'          => 'required',
            'probation_from'           => 'required|date',
            'probation_to'             => 'required|date',
            'user_id'                  => 'required'
        ];

        if(!isset($aData['editPosition'])){
            $array += ['position_id' => 'required|integer|max:255'];
        }

        $aRuleList = $aRuleList ? $aRuleList : $array;

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}

