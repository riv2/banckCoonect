<?php

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminQuizCreateValidator extends Validator
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
                'title_ru'      => 'required|string|max:255',
                'title_kz'      => 'required|string|max:255',
                'question_ru.*' => 'required|string|max:255',
                'question_kz.*' => 'required|string|max:255'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }
}
