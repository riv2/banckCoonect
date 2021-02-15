<?php
/**
 * User: Vlad
 * Date: 11.05.20
 * Time: 18:00
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class NomenclatureNewTemplateFileValidator extends Validator
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
                'new_template_file' => 'nullable|file',
                'template_id' => 'required|integer|max:255',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}