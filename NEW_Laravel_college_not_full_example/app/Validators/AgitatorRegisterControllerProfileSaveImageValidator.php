<?php
/**
 * User: dadicc
 * Date: 1/14/20
 * Time: 1:05 PM
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AgitatorRegisterControllerProfileSaveImageValidator extends Validator
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
                'profileImage'       => 'required|string|max:255',
                'profileImgSource'   => 'required',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}