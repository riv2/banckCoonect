<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PracticeStore extends FormRequest
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
        $rules = [
            'organization_name'             => 'required|max:250',
            'organization_activity_type'    => 'required|max:250',
            'specialities'                  => 'required',
            'contract_number'               => 'required|max:50',
            'contract_start_date'           => 'required|date',
            'contract_end_date'             => 'required|date|after:contract_start_date',
            'capacity'                      => 'required|max:100',
            'scan.*'                        => 'file|mimes:doc,docx,jpeg,png,pdf',

        ];
        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'scan.*' => 'scan'
        ];

        return $attributes;
    }
}
