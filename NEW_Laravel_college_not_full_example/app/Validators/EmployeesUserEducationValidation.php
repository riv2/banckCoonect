<?php
/**
 * User: dadicc
 * Date: 17.10.19
 * Time: 9:46
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class EmployeesUserEducationValidation extends Validator
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
                'education_degree'        => 'nullable|string|max:255',
                'institution'             => 'nullable|string|max:255',
                'start_education'         => 'nullable|date',
                'end_education'           => 'nullable|date',
                'education_lang'          => 'nullable|string|max:255',
                'education_speciality_id' => 'nullable|integer',
                'qualification_assigned'  => 'nullable|string|max:255',
                'protocol_number'         => 'nullable|string|max:255',
                'dissertation_topic'      => 'nullable|string|max:255',
                'nostrification'          => 'nullable|string|max:255',
                'user_id'                 => 'required'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}

