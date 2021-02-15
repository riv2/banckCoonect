<?php
/**
 * User: dadicc
 * Date: 27.08.19
 * Time: 9:14
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminDiscountRequestSetStatusValidator extends Validator
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
                'discount_id'          => 'required|integer',
                //'semesters'            => 'required',
                'status'               => 'required|string|max:255',
                'comment'              => 'string|max:255',
                'discount_custom_size' => 'string|max:50',
                'reason_refusal'       => 'string|max:255',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}