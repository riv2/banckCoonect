<?php
/**
 * User: dadicc
 * Date: 20.08.19
 * Time: 17:25
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AdminMatriculantAjaxAttachServiceValidator extends Validator
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
                'service' => 'required|integer',
                'ids'     => 'required|array',
                'count'   => 'required|integer',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}