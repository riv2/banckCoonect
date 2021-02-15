<?php
/**
 * User: Vlad
 * Date: 16.03.20
 * Time: 9:46
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class NomenclatureFileValidator extends Validator
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
                'name' => 'required|string|max:255',
                'load_date' => 'required|date',
                'template' => 'nullable|file',
                'votes_list.*' => 'required|integer'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}

