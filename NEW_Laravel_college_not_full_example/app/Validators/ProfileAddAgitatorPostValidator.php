<?php
/**
 * Created by PhpStorm.
 * User: dadicc
 * Date: 12/8/19
 * Time: 8:30 PM
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class ProfileAddAgitatorPostValidator extends Validator
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
                'fio' => 'required|string|max:1500'
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}

