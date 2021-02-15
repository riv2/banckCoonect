<?php

namespace App\Http\Requests\Info;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasRight('info_table', ['create', 'edit']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title_ru'          => 'required|max:255',
            'title_kz'          => 'required|max:255',
            'title_en'          => 'required|max:255',
            'text_preview_ru'   => 'required',
            'text_preview_kz'   => 'required',
            'text_preview_en'   => 'required',
            'text_ru'           => 'required',
            'text_kz'           => 'required',
            'text_en'           => 'required',
        ];
    }
}
