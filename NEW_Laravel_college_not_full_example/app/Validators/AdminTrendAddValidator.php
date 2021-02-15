<?php
/**
 * User: dadicc
 * Date: 05.07.19
 * Time: 15:17
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminTrendAddValidator extends Validator
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
                'name'                  => 'required|string|max:255',
                'name_kz'               => 'required|string|max:255',
                'name_en'               => 'required|string|max:255',
                'qualifications.*.name_ru'    => 'required|string|max:255',
                'qualifications.*.name_kz'    => 'required|string|max:255',
                'qualifications.*.name_en'    => 'required|string|max:255',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}
