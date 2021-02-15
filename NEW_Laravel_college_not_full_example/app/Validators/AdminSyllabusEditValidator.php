<?php
/**
 * User: dadicc
 * Date: 16.07.19
 * Time: 10:10
 */

namespace App\Validators;

use App\Rules\Syllabus\MaterialsExist;
use Illuminate\Support\Facades\Validator;

class AdminSyllabusEditValidator extends Validator
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
                'theme_number'             => 'required|string|max:255',
                'theme_name'               => 'required|string',
                'literature'               => 'required',
                'literature_added.*'       => 'integer',
                'contact_hours'            => 'required|numeric',
                'self_hours'               => 'required|numeric',
                'self_with_teacher_hours'  => 'required|numeric',
                'sro_hours'                => 'required|numeric',
                'practicalMaterials'       => new MaterialsExist(),
                'teoreticalMaterials'      => new MaterialsExist(),
                'sroMaterials'             => new MaterialsExist(),
                'sropMaterials'            => new MaterialsExist()
            ];



        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}