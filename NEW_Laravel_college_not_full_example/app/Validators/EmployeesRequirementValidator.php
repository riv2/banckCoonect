<?php
/**
 * User: vlad
 * Date: 09.01.20
 * Time: 9:46
 */

namespace App\Validators;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class EmployeesRequirementValidator extends Validator
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
                'field_type' => [
                    'required',
                    Rule::in(['text', 'date', 'file', 'select']),
                ],
                'category'   => 'required',
                'name'       => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('employees_requirements')
                        ->where(function ($query) use ($aData) {
                            return $query->where('category', $aData['category']);
                        })
                ],
                'field_name' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/(^([a-zA-Z]+)(\d+)?$)/u',
                    Rule::unique('employees_requirements')
                        ->where(function ($query) use ($aData) {
                            return $query->where('category', $aData['category']);
                        })
                ]
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}

