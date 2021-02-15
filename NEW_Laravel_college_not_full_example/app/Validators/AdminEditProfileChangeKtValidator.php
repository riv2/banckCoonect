<?php
/**
 * User: dadicc
 * Date: 15.08.19
 * Time: 9:34
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminEditProfileChangeKtValidator extends Validator
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
                'kt_name_1'        => 'required|string|max:255',
                'kt_name_2'        => 'required|string|max:255',
                'kt_name_3'        => 'required|string|max:255',
                'kt_name_4'        => 'required|string|max:255',
                'kt_val_1'         => 'required|integer',
                'kt_val_2'         => 'required|integer',
                'kt_val_3'         => 'required|integer',
                'kt_val_4'         => 'required|integer',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}