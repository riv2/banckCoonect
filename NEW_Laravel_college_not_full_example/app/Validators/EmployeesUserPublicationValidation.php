<?php
/**
 * User: dadicc
 * Date: 17.10.19
 * Time: 9:46
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class EmployeesUserPublicationValidation extends Validator
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
                'theme'            => 'nullable|string|max:255',
                'science_branch'   => 'nullable|string|max:255',
                'content'          => 'nullable|string|max:255',
                'publication_date' => 'nullable|date',
                'publication_name' => 'nullable|string|max:255',
                'info'             => 'nullable|string|max:255',
                'impact_factor'    => 'nullable|string|max:255',
                'user_id'          => 'required|integer'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}

