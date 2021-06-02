<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartner extends FormRequest
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
            'name' => ['nullable', 'string'],
            'comment' => ['nullable', 'string'],
            'status' => ['required', 'integer'],
            'phone' => ['nullable', 'string'],
            'skype' => ['nullable', 'string'],
            'email_unsubs' => ['nullable', 'integer'],
            'company' => ['nullable', 'string'],
            'pay_account' => ['nullable', 'string'],
            'contract_number' => ['nullable', 'string'],
            'contract_date' => ['nullable', 'date'],
            'inn' => ['nullable', 'string'],
            'bic' => ['nullable', 'string'],
            'rs' => ['nullable', 'string'],
        ];
    }
}
