<?php
namespace App\Validators;

use App\Profiles;
use Illuminate\Support\Carbon;
use App\Services\Auth;
use Illuminate\Support\Facades\Validator;

class BcApplicationValidator
{
    /**
     * @param $requestParamList
     * @return \Illuminate\Validation\Validator
     */
    static function make($requestParamList)
    {
        $inputs = $requestParamList;
        $ruleList = [
            'region_id' => 'required|exists:regions,id',
            'city_id' => 'required|exists:cities,id',
            'street' => 'required',
            'building_number' => 'required'
        ];

        if(isset($inputs['has_residenceregistration']) && $inputs['has_residenceregistration'] == 'true') {
            $ruleList['residenceregistration'] = 'required';
        }

        if(isset($inputs['has_ent']) && $inputs['has_ent'] == 'true') {
            $ruleList['ikt'] = 'required';
        }

        $profile = Profiles::where('user_id', '=', Auth::user()->id)->first();
        $age = Carbon::parse($profile->bdate)->age;

        if( $age >= 18 && $age <= 29 && $profile->sex == 1 && isset($inputs['has_military']) && $inputs['has_military'] == 'true') {
            $ruleList['military'] = 'required';
        }

        if( $age < 23 && isset($inputs['r063']) && $inputs['has_r063'] == 'true') {
            $ruleList['r063'] = 'required';
        }

        if( isset($inputs['has_r086']) && $inputs['has_r086'] == 'true' ) {
            $ruleList['r086'] = 'required';
        }

        if(isset($inputs['bceducation']) && $inputs['bceducation'] != 'false') {
            $ruleList['numeducation'] = 'required';
            $ruleList['sereducation'] = 'required';
            $ruleList['nameeducation'] = 'required';
            $ruleList['dateeducation'] = 'required';
            if ($inputs['bceducation'] == 'vocational_education') {
                $ruleList['eduspecialty'] = 'required';
                $ruleList['typevocational'] = 'required';
            } elseif ($inputs['bceducation'] == 'bachelor') {
                $ruleList['eduspecialty'] = 'required';
                $ruleList['edudegree'] = 'required';
            }

            if( isset($inputs['has_diploma_supplement']) && $inputs['has_diploma_supplement'] == 'true') {
                $ruleList['atteducation'] = 'required';
            }

            if (isset($inputs['kzornot']) && $inputs['kzornot'] == 'false') {
                $ruleList['nostrification'] = 'required';
                $ruleList['nostrificationattach'] = 'required';
            }
        }

        return Validator::make($requestParamList, $ruleList);
    }
}