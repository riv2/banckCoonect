<?php
/**
 * User: dadicc
 * Date: 17.10.19
 * Time: 9:46
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class EmployeesNewPosition extends Validator
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
                'name'           => 'required|string|max:255',
                'description'    => 'nullable|string|max:500',
                'department_id'  => 'required|string'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}
