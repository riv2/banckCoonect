<?php

namespace App\Http\Requests\Specialities;

use Illuminate\Foundation\Http\FormRequest;

class AddDeleteSpecialityDisciplineSemesterRequest extends FormRequest
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
            'disciplineId' => 'required',
            'specialityId' => 'required',
            'checked' => 'required'
        ];
    }
}
