<?php
/**
 * User: dadicc
 * Date: 04.08.19
 * Time: 14:36
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class TeacherMirasProfileAddAdressInputsValidator extends Validator
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
                'country_id'            => 'integer',
                'region_id'             => 'integer',
                'city_id'               => 'required|integer',
                'street'                => 'required|string|max:255',
                'building_number'       => 'required|string|max:32',
                'apartment_number'      => 'max:32',
                'home_country_id'       => 'integer',
                'home_region_id'        => 'integer',
                'home_city_id'          => 'required|integer',
                'home_street'           => 'required|string|max:255',
                'home_building_number'  => 'required|string|max:32',
                'home_apartment_number' => 'max:32',
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}