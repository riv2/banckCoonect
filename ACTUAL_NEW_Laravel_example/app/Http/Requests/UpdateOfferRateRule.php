<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferRateRule extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "offer_id" => ["numeric"],
            "fee"=>["numeric", "required"],
            "date_start"=>["date","required"],

            /**
             * @todo Сделать валидацию позже, как уточнятся параметры.
             */
            // "date_end"=>["date|nullable|after:now"],
            // "business_unit_id"=>["string","min:1"],
        ];
    }
}
