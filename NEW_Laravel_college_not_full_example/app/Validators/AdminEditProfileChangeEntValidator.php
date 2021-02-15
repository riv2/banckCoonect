<?php
/**
 * User: dadicc
 * Date: 23.07.19
 * Time: 0:34
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminEditProfileChangeEntValidator extends Validator
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
                'ent_name_1'        => 'required|string|max:255',
                'ent_name_2'        => 'required|string|max:255',
                'ent_name_3'        => 'required|string|max:255',
                'ent_name_4'        => 'required|string|max:255',
                'ent_val_1'         => 'required|integer',
                'ent_val_2'         => 'required|integer',
                'ent_val_3'         => 'required|integer',
                'ent_val_4'         => 'required|integer',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}